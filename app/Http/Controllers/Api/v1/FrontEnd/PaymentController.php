<?php

namespace App\Http\Controllers\Api\v1\FrontEnd;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Models\Item;
use App\Models\PiUser;
use App\Models\Order;
use App\Models\Payment;

use App\Traits\Helper;

use App\Models\LineOrder;
use App\Models\Notification;
use App\Models\Setting;
use App\Models\ApprovedLineOrder;
use App\Exceptions\InsufficientAmountException;
use App\Exceptions\InsufficientQuantityException;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    use Helper;

    //private $key = "vgufdu7tprbpdsrjxybfpkf6eci8q7crg1kbbcmends5vtulnotn5jv1gjpzsfzj";//piketplace
    //private $key = "awnvszy2pwdfey6jcnj7qye6yamaz5shtvqeaomsjhbazaqjadd2qeibvvaog8re";//marketplace
    private $key = "xkfnxpqvrejgtgputjt1wg6xgcgzxp0nhqmpmbtlrxh8pdzxdizcprfgctpxundj";//mapchat
    private $pi_url = "https://api.mainnet.minepi.com";

    public function __construct()
    {
        $this->key = $this->getSetting('api_key');
        $this->pi_url = $this->getSetting('pi_url');
    }

    public function getPaymentDTO($paymentId)
    {
        $client_http = new \GuzzleHttp\Client(["verify"=>false]);
        $rep = $client_http->request('GET', 'https://api.minepi.com/v2/payments/'.$paymentId.'?Key='.$this->key, [
            'headers' => [
                'Accept' => 'application/json',
                'Authorization' => 'Key '.$this->key
            ],
        ]);
        $body = $rep->getBody();
        Log::info("[body] $body");
        $body_obj = json_decode($body, false, 512, JSON_UNESCAPED_UNICODE);
        return $body_obj;
    }

    public function approve(Request $request)
    {
        try {
            DB::beginTransaction();
            Log::info("Payment approve start");
            //$paymentId = $_GET['paymentId'];
            $paymentId = $request->paymentId;
            $body = $this->getPaymentDTO($paymentId);
            //////////////////////////////////////////////////////////
            $endpoint = 'https://api.minepi.com/v2/payments/'.$paymentId.'/approve';
    		$client = new \GuzzleHttp\Client(["verify"=>false]);

    		$response = $client->request('POST', $endpoint, [
                'headers' => [
                    'Accept' => 'application/json',
                    'Authorization' => 'Key '.$this->key
                ],
                //'body' => ['txid' => $txid]
            ]);

    		$statusCode = $response->getStatusCode();
            Log::info("Payment complete end");
            return response()->json($statusCode);
            //return new JsonResponse($status);
        } catch (\Exception $e) {
            Log::info($e->getMessage());
            DB::rollBack();
        }
    }

    public function completePayment($paymentId, $txid)
    {
        $body = $paymentDTO = $this->getPaymentDTO($paymentId);
        $userId = $body->metadata->userId;
        $user = $this->getUpdatedUser($userId);
        try {
            DB::beginTransaction();
            Log::channel('pi_payment')->info('Payment complete start');
            $amount = sprintf("%.7f", $body->amount);
            Log::info('sprintf("%.7f", $body->amount) : '.$amount);
            $public_key = $this->getPublicKey($txid);
            $metadata = $body->metadata;
            $uniqueId = $metadata->uniqueId;

            $now = now();
            $cart = $metadata->cart;
            $data_order = [
                'pi_users_id' => $user->id,
                'ordered_at' => $now,
            ];
            $order = Order::create($data_order);
            $total = 0;
            foreach ($cart as $key => $line) {
                $item = Item::where('id', $line->id)->first();
                $data_line_order = [
                    'items_id' => $item->id,
                    'orders_id' => $order->id,
                    'name' => $item->name,
                    'price' => $item->price,
                    'quantity' => $line->qty,
                    'ordered_at' => $now,
                ];
                $total = piket_multiply($item->price, $line->qty);
                $line_order = LineOrder::create($data_line_order);
            }
            $order->update(['total' => $total]);

            $payment = new Payment();
            $payment->orders_id = $order->id;
            $payment->pi_users_id = $user->id;
            $payment->pi_users_username = $user->username;
            $payment->identifier = $paymentId;
            $payment->txid = $txid;
            $payment->amount = $amount;
            $payment->public_key = $public_key;
            $payment->unique_id = $uniqueId;
            $payment->save();
            $user->public_key = $public_key;
            $user->save();
            ///////////////////////////////////////////////////////////
            $client = new \GuzzleHttp\Client(["verify"=>false]);
            $response = $client->request('POST', 'https://api.minepi.com/v2/payments/'.$paymentId.'/complete', [
                'headers' => [
                    'Accept' => 'application/json',
                    'Authorization' => 'Key '.$this->key
                ],
                'query' => ['txid' => $txid],
            ]);
            $statusCode = $response->getStatusCode();
            if ($statusCode != 200) {
                Log::channel('pi_payment')->info("[statusCode] $statusCode return false");
                Log::channel('pi_payment')->error("PiNetwork complete payment error : ".$response->getBody());
                throw new \Exception("Error Processing Request", 1);
                //return false;
            }else{
                Log::channel('pi_payment')->info('Completion successfull');
            }
            DB::commit();
            return $statusCode;
        } catch (\Exception $e) {
            Log::info($e->getMessage());
            DB::rollBack();
        }
    }

    public function complete(Request $request)
    {
        $paymentId = $request->paymentId;
        $txid = $request->txid;
        $this->completePayment($paymentId, $txid);
        return response()->json([]);
    }

    public function incomplete(Request $request)
    {
        Log::channel('pi_payment')->info("complete incomplete payment start");
        $paymentId = $request->paymentId;
        $txid = $request->txid;
        $this->completePayment($paymentId, $txid);
        return response()->json([]);
    }

    /**
     * @Route("/cancel", name="cancel", methods="GET")
     */
    public function cancel(Request $request)
    {
        $paymentId = $request->paymentId;

        $client = new \GuzzleHttp\Client(["verify"=>false]);
        $response = $client->request('POST', 'https://api.minepi.com/v2/payments/'.$paymentId.'/cancel', [
            'headers' => [
                'Accept' => 'application/json',
                'Authorization' => 'Key '.$this->key
            ]
        ]);

        $statusCode = $response->getStatusCode();
        
        return response()->json($statusCode);
    }
}
