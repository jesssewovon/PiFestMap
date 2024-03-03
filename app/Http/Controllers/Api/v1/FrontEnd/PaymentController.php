<?php

namespace App\Http\Controllers\Api\v1\FrontEnd;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Models\Product;
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
            if (isset($body->metadata->line_order_id)) {
                $line_order = $this->getLineOrder($body->metadata->line_order_id, true);
                if (isset($line_order->order->paid) && $line_order->order->paid===true) {
                    return 0;
                }
            }

            /*$body = $this->getPaymentDTO($paymentId);
            $type = $body->metadata->type;
            $userId = $body->metadata->userId;
            $buyer = $current_user = $this->getUpdatedUser($userId);
            $cart = $buyer->cart;
            if (isset($body->metadata->product)) {//Buy now without a cart
                $panier = $body->metadata->product;
                $cart = [];
                $cart[] = [
                    'id'=>$panier->id,
                    'quantity'=>$panier->quantity,
                    'noshipping'=>$panier->noshipping,
                ];
            }
            //Type 5 is purchase
            if ($type == 5 && !is_null($cart)) {        
                foreach ($cart as $key => $product) {
                    $product_obj = Product::find($product['id']);
                    if ($product_obj->is_digital) {
                        continue;
                    }
                    if ($product_obj->quantity < $product['quantity']) {
                        throw new InsufficientQuantityException($product_obj->id, $product['quantity']);
                    }
                    $quantity_left = $product_obj->quantity - intval($product['quantity']);
                    $product_obj->update(['quantity' => $quantity_left]);
                    ApprovedLineOrder::create([
                        'products_id' => $product_obj->id,
                        'pi_users_id' => $request->user()->id,
                        'quantity' => $product['quantity'],
                        'approved_at' => now(),
                    ]);
                }
            }
            DB::commit();*/
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
        } catch (\InsufficientQuantityException $e) {
            Log::info($e->getMessage());
            DB::rollBack();
        } catch (\Exception $e) {
            Log::info($e->getMessage());
            DB::rollBack();
        }
    }

    public function completePayment($paymentId, $txid, $from = "complete")
    {
        $body = $paymentDTO = $this->getPaymentDTO($paymentId);
        try {
            DB::beginTransaction();
            Log::channel('pi_payment')->info('Payment complete start');
            $amount = sprintf("%.7f", $body->amount);
            Log::info('sprintf("%.7f", $body->amount) : '.$amount);
            $type = $body->metadata->type;
            $userId = $body->metadata->userId;
            
            /*$ids = $body->metadata->orderId;
            Log::info('[ids] '.$ids);
            list($orderId, $userId) = explode('_', $ids);*/
            $buyer = $current_user = $this->getUpdatedUser($userId);
            $public_key = $this->getPublicKey($txid);
            $uniqueId = $body->metadata->uniqueId;

            $payment = new Payment();
            $payment->origin = Payment::PINETWORK_WALLET;
            $payment->confirmed_at = now();
            $payment->pi_users_id = $current_user->id;
            $payment->pi_users_username = $current_user->username;
            $payment->type = $type;
            $payment->identifier = $paymentId;
            $payment->txid = $txid;
            $payment->amount = $body->amount;
            //$payment->amount = floatval($body->amount);
            //$payment->amount = floatval(sprintf('%f', $body->amount));
            $payment->public_key = $public_key;
            $payment->unique_id = $uniqueId;
            $current_user->public_key = $public_key;
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
            ///////////////////////////////////////////////////////////
            //////PAYMENT COMPLETION WHEN PAYMENT ON PRE ORDER PURCHASE
            if ($type == 5 && isset($body->metadata->is_pre_order)) {
                Log::channel('pi_payment')->info('Case payment on preorder start');
                $line_order_id = $body->metadata->line_order_id;
                /*$line_order = LineOrder::where('id', $line_order_id)
                    ->with('product')->first();*/
                $line_order = $this->getLineOrder($line_order_id, false);
                $line_order->is_pre_order = false;
                $product = $line_order->product;
                //Case when stock insufficient
                if (!$product->is_digital && $line_order->quantity > $product->quantity) {
                    $data = [
                        'product_id' => $product->id,
                        'quantity' => $line_order->quantity,
                        'line_order_id' => $line_order_id,
                    ];
                    throw new InsufficientQuantityException($data);
                }

                //////////ORDER
                $order = new Order();
                $now = new \Datetime();
                $order->reference = $now->format('YmdHis').rand(1, 1000000);
                $order->pi_users_id = $buyer->id;
                $order->pi_users_username = $buyer->username;
                $order->noshipping = 0;
                $order->paid = 1;
                $order->unique_id = $uniqueId;
                $order->total = $line_order->total;
                $order->shipping = $line_order->pre_order_address;
                $order->ordered_at = now();
                Log::channel('pi_payment')->info('in preorder 3');
                $addresses = $current_user->addresses;
                if (!$this->address_exists($line_order->pre_order_address, $addresses)) {
                    $addresses[] = $line_order->pre_order_address;
                }
                Log::channel('pi_payment')->info('in preorder 4');
                $current_user->addresses = $addresses;
                
                $order->save();
                $line_order->orders_id = $order->id;
                $line_order->price = $product->price;
                $line_order->price_str = $product->price;
                $line_order->libelle = $product->libelle;
                $line_order->save();
                
                $payment->amount = $line_order->total;
                $payment->orders_id = $order->id;
                Setting::increasePiketplaceBalance($payment->amount);

                $current_user->save();
                $payment->save();
                //Decrease product quantity
                $quantity = $product->quantity - $line_order->quantity;
                $product->update(['quantity' => $quantity]);
                //Delete approved line order
                ApprovedLineOrder::where('products_id', $product->id)
                    ->where('pi_users_id', $buyer->id)
                    ->update(['deleted_at' => now()]);

                ////////////////SHIPPING FEE PAYMENT/////////////////////////
                $payment = new Payment();
                $payment->confirmed_at = now();
                $payment->type = Payment::SHIPPING_FEE;
                $payment->origin = Payment::PINETWORK_WALLET;
                $payment->pi_users_id = $current_user->id;
                $payment->pi_users_username = $current_user->username;
                $payment->amount = $line_order->fee;
                $meta = [
                    'old_balance'=>$current_user->wallet->balance,
                ];
                Setting::increasePiketplaceBalance($payment->amount);
                $current_user->wallet->balance = piket_sub($current_user->wallet->balance, $payment->amount);
                $current_user->wallet->save();
                $meta ['new_balance'] = $current_user->wallet->balance;
                $payment->meta = $meta;
                $payment->save();
                //////////////////////////////////////////////////////

                $this->sendPurchaseNotifications($current_user, $line_order);
                $this->sendPurchaseEmail($current_user, $line_order);
                Log::channel('pi_payment')->info('Case payment on preorder end');
                DB::commit(); 
                return $statusCode;
            }
            //END PAYMENT COMPLETION WHEN PAYMENT ON PRE ORDER PURCHASE
            /////////PAYMENT COMPLETION WHEN DEPOSIT OR DONATION
            if ($type == 1 || $type == 4) {
                Log::channel('pi_payment')->info('Case deposit or donation start');
                //$current_user = session('user');
                Setting::increasePiketplaceBalance($amount);

                $current_user->save();
                $payment->save();
                /////////////////////////////////////////////////////
                $notification = new Notification();
                $notification->message = "message.donation_done";
                $notification->url = "";
                $notification->pi_users_id = $current_user->id;
                $notification->save();
                /////////////////////////////////////////////////////
                if ($type==1) {
                    Log::channel('pi_payment')->info('Case deposit');
                    $notification->message = "message.balance.deposit_success";
                    $notification->datas = ["amount"=>$amount];
                    $notification->save();
                    //$current_user->deposit($body->amount);
                    $meta = $payment->meta;
                    if (is_null($current_user->wallet)) {
                        $current_user->createWallet();
                    }
                    $current_user->refresh();
                    $meta['old_balance'] = $current_user->wallet->balance;
                    //$total = floatval($current_user->wallet->balance) + floatval($body->amount);
                    //$total = floatval(bcadd(strval($current_user->wallet->balance), strval($amount), 7));
                    $total = piket_add($current_user->wallet->balance, $amount);
                    $current_user->wallet->balance = $total;
                    $meta['new_balance'] = $current_user->wallet->balance;
                    $payment->meta = $meta;
                    $payment->save();
                    
                    $current_user->wallet->save();
                }
                Log::channel('pi_payment')->info('Case deposit or donation end');
                DB::commit();
                return $statusCode;
            }
            /////////END PAYMENT COMPLETION WHEN DEPOSIT OR DONATION
            /////////START PAYMENT COMPLETION WHEN CART PAYMENT
            Log::channel('pi_payment')->info('Case payment on cart start');
            //////////ORDER
            $order = new Order();
            $now = new \Datetime();
            $order->reference = $now->format('YmdHis').rand(1, 1000000);
            $order->pi_users_id = $buyer->id;
            $order->pi_users_username = $buyer->username;
            $order->noshipping = 1;
            $order->save();

            //$cart = json_decode($buyer->cart, true);
            $cart = $buyer->cart;
            if (isset($body->metadata->product)) {//Buy now without a cart
                $panier = $body->metadata->product;
                //$cart_one = json_decode($body->metadata->product, true);
                $cart = [];
                $cart[] = [
                    'id'=>$panier->id,
                    'quantity'=>$panier->quantity,
                    'noshipping'=>$panier->noshipping,
                ];
                //Log::info("eeeeeeeeeeee ".$body->metadata->product);
            }else{
                //We empty the cart only if the purchase is done trough cart
                $current_user->cart = [];//Empty the cart - vider le panier
            }
            $noshipping = true;
            $seller_tab = [];
            $total = 0;
            $line_order_tab = [];
            foreach ($cart as $key => $product) {
                //$product = json_decode($product);
                $line_order = new LineOrder();
                $product_obj = Product::find($product['id']);
                if (!$product_obj->is_digital && $product_obj->quantity < $product['quantity']) {
                    Log::channel('quantity_insufficient')->info('Low quantity : product_id='.$product['id'].', buyer_id='.$buyer->id);
                    $data = [
                        'product_id' => $product['id'],
                        'quantity' => $product['quantity'],
                        'cart' => $cart,
                    ];
                    throw new InsufficientQuantityException($data);
                }
                $line_order->products_id = $product['id'];
                $line_order->orders_id = $order->id;
                $line_order->libelle = $product_obj->libelle;
                $line_order->price = $product_obj->price;
                $line_order->price_str = $product_obj->price_str;
                $line_order->quantity = $product['quantity'];
                $line_order->noshipping = $product['noshipping'];
                $line_order->seller_free_shipping = $product_obj->free_shipping;
                if ($product_obj->free_shipping && isset($product['in_free_shipping_zone'])) {
                    $line_order->in_free_shipping_zone = $product['in_free_shipping_zone'];
                }
                //Decrease product quantity
                $product_obj->quantity -= $line_order->quantity;
                $product_obj->save();
                //Delete approved line order
                ApprovedLineOrder::where('products_id', $product_obj->id)
                    ->where('pi_users_id', $buyer->id)
                    ->update(['deleted_at' => now()]);
                //////////////////////////////
                /*if (!array_key_exists($idSeller, $seller_tab)) {
                    array_push($seller_tab, [
                        "$idSeller"=> [$product_obj],
                    ]);
                }else{
                    $seller_tab["$idSeller"][] = $product_obj;
                }*/
                if ($product['noshipping'] == 0) {
                    $noshipping = false;
                }

                if (isset($product['pre_order']) && $product['pre_order']) {
                    $line_order->orders_id = null;
                    $line_order->is_pre_order = true;
                    $line_order->pre_order_pi_users_id = $current_user->id;
                    $line_order->pre_order_pi_users_username = $current_user->username;
                    $line_order->pre_order_address = null;
                    $line_order->noshipping = false;
                    $line_order->save();
                    array_push($line_order_tab, $line_order);
                    continue;
                }
                if (isset($product['final_paid_shipping']) && $product['final_paid_shipping']) {
                    //$total += floatval($product['fee']);
                    $total = piket_add($total, $product['fee']);
                    $line_order->shipping_info = [
                        'final_paid_shipping'=>true,
                        'fee'=>$product['fee'],
                    ];
                }
                if (isset($product['final_free_shipping']) && $product['final_free_shipping']) {
                    $line_order->shipping_info = [
                        'final_free_shipping'=>true,
                    ];
                }
                $line_order->save();
                /////////////////////////////////////////////
                $idSeller = $product_obj->user->id;
                $seller_tab["$idSeller"][] = $this->getLineOrder($line_order->id, -1);

                //$total += intval($product['quantity']) * floatval($product_obj->price);
                $current_total = piket_multiply($product['quantity'], $product_obj->price);
                $total = piket_add($total, $current_total);
            }
            $order->total = $total;
            ////////////////////////////////////////////////////////////
            if (!$noshipping) {
                $order->noshipping = 0;
                $isNewAddress = $body->metadata->isNewAddress;
                /*$name = $body->metadata->address_obj->name;
                $country_name = $body->metadata->address_obj->country_name;
                $city = $body->metadata->address_obj->city;
                $addressText = $body->metadata->address_obj->address;
                $phone_number = $body->metadata->address_obj->phone_number;*/
                $address = json_decode(json_encode($body->metadata->address_obj), true);
                /*$address = [
                    'name'=>$name,
                    'country_name'=>$country_name,
                    'city'=>$city,
                    'address'=>$addressText,
                    'phone_number'=>$phone_number,
                ];*/
                if ($isNewAddress == 'true') {
                    $addresses = $current_user->addresses;
                    $addresses[] = $address;
                    $current_user->addresses = $addresses;
                }
                $order->shipping = $address;

                foreach ($line_order_tab as $key => $line_order) {
                    $line_order->pre_order_address = $address;
                    $line_order->save();
                }
            }
            //$buyer->save();
            //////////END ORDER

            //$seller = $product->user;
            $payment->orders_id = $order->id;
            $order->paid = true;
            $order->unique_id = $uniqueId;
            Setting::increasePiketplaceBalance($payment->amount);

            $current_user->save();
            $payment->save();
            $order->ordered_at = now();
            $order->save();
            
            //////////////////////////////////

            $this->sendPurchaseNotifications($current_user, null, $seller_tab);
            $this->sendPurchaseEmail($current_user, null, $seller_tab);
            
            Log::channel('pi_payment')->info('Case payment on cart end');
            DB::commit();
            return $statusCode;
            //return response()->json($statusCode);
        } catch (\InsufficientQuantityException $e) {
            Log::info($e->getMessage());
            DB::rollBack();
            $from = $this->pi_url." / ".$from;
            $this->save_failed_pi_payment($paymentId, $txid, $paymentDTO, $from, $e->getMessage());
        } catch (\Exception $e) {
            Log::info($e->getMessage());
            DB::rollBack();
            $from = $this->pi_url." / ".$from;
            $this->save_failed_pi_payment($paymentId, $txid, $paymentDTO, $from, $e->getMessage());
        }
    }

    public function complete(Request $request)
    {
        $paymentId = $request->paymentId;
        $txid = $request->txid;
        //$paymentId = 'AGZiASOTS0c9MmDeyeQeBv0pBHTX';
        $res = $this->completePayment($paymentId, $txid);
        return response()->json($res);/*
        try {
            DB::beginTransaction();
            $res = $this->completePayment($paymentId, $txid);
            DB::commit();
            return response()->json($res);
        } catch (\InsufficientQuantityException $e) {
            Log::info($e->getMessage());
            $this->save_failed_pi_payment($paymentId, $txid, $paymentDTO, 'complete');
            DB::rollBack();
        } catch (\Exception $e) {
            Log::info($e->getMessage());
            $this->save_failed_pi_payment($paymentId, $txid, $paymentDTO, 'complete');
            DB::rollBack();
        }*/
    }

    public function incomplete(Request $request)
    {
        Log::channel('pi_payment')->info("complete incomplete payment start");
        $paymentId = $request->paymentId;
        $txid = $request->txid;
        //$paymentId = 'AGZiASOTS0c9MmDeyeQeBv0pBHTX';
        $res = $this->completePayment($paymentId, $txid, "incomplete");
        return response()->json($res);
        /*try {
            DB::beginTransaction();
            $paymentDTO = $this->getPaymentDTO($paymentId);
            $res = $this->completePayment($paymentId, $txid, $paymentDTO);
            Log::channel('pi_payment')->info("complete incomplete payment end");
            DB::commit();
            return response()->json($res);
        } catch (\InsufficientQuantityException $e) {
            Log::info($e->getMessage());
            $this->save_failed_pi_payment($paymentId, $txid, $paymentDTO, 'incomplete');
            DB::rollBack();
        } catch (\Exception $e) {
            Log::info($e->getMessage());
            $this->save_failed_pi_payment($paymentId, $txid, $paymentDTO, 'incomplete');
            DB::rollBack();
        }*/
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

    public function payment_piketplace_wallet(Request $request)
    {
        $user_id = $request->user_id;
        $user = $this->getUpdatedUser($user_id);
        DB::beginTransaction();
        try {
            if (is_null($user->balance) || $user->balance == 0) {
                return response()->json([
                    'user' => $user,
                    'status' => false,
                    'message' => "message.donate.not_enough_amount",
                ]);
            }
            //$address = json_decode($request->address);
            $address = $request->address;
            $cart = $user->cart;
            if (isset($request->isBuyNow) && $request->isBuyNow=="true") {//Buy now without a cart
                $panier = $request->product;
                //$cart_one = json_decode($body->metadata->product, true);
                $cart = [];
                $cart[] = [
                    'id'=>$panier['id'],
                    'quantity'=>$panier['quantity'],
                    'noshipping'=>$panier['noshipping'],
                ];
                //Log::info("eeeeeeeeeeee ".$body->metadata->product);
            }
            $res = $this->total($cart);
            if (!isset($res['total'])) {
                return response()->json([
                    'user' => $user,
                    'status' => false,
                    'message' => "message.an_error_occured",
                ]);
            }
            if (isset($res['total']) && $res['total']  > $user->balance) {
                return response()->json([
                    'user' => $user,
                    'status' => false,
                    'message' => "message.donate.not_enough_amountg",
                ]);
            }
            $payment['origin'] = Payment::PIKETPLACE_WALLET;
            $payment['type'] = Payment::PURCHASE;
            $payment['amount'] = $request->total;
            $isNewAddress = $request->isNewAddress;
            $this->payment_global_function($user, $cart, $address, $isNewAddress, $payment);
            $user->refresh();

            $last = $user->last_notification_page_refresh;
            $nb_notifications = Notification::where('pi_users_id', $user_id)->whereRaw("created_at >= '".$last."'")->count();
            $user = $this->getUpdatedUser($user_id);

            DB::commit();
            return response()->json([
                'user' => $user,
                'address' => $address,
                'status' => true,
                'nb_notifications' => $nb_notifications,
            ]);
        } catch (InsufficientQuantityException $e) {
            Log::info($e->getMessage());
            DB::rollBack();
            return response()->json([
                'user' => $user,
                'status' => false,
                'data' => $e->data,
                'message' => "message.cart.quantity_insufficient",
            ]);
        } catch (InsufficientAmountException $e) {
            Log::error($e->getMessage());
            DB::rollBack();
            return response()->json([
                'user' => $user,
                'status' => false,
                'message' => "message.donate.not_enough_amount",
            ]);
        } catch (\Exception $e) {
            Log::critical($e->getMessage());
            DB::rollBack();
            return response()->json([
                'user' => $user,
                'status' => false,
                'message' => "message.an_error_occured",
            ]);
        }
    }

    public function payment_global_function($user, $cart, $address, $isNewAddress, $paymentData)
    {
        $current_user = $user;
        //////////ORDER
        $order = new Order();
        $now = new \Datetime();
        $order->reference = $now->format('YmdHis').rand(1, 1000000);
        $order->pi_users_id = $user->id;
        $order->pi_users_username = $user->username;
        $order->noshipping = 1;
        $order->save();

        //$cart = json_decode($buyer->cart, true);
        
        $noOrdershipping = true;
        $seller_tab = [];
        $total = 0;
        $line_order_tab = [];
        foreach ($cart as $key => $product) {
            //$product = json_decode($product);
            $line_order = new LineOrder();
            $product_obj = Product::find($product['id']);
            $line_order->products_id = $product['id'];
            $line_order->noshipping = $product['noshipping']=='false'?false:true;
            $line_order->orders_id = $order->id;
            $line_order->libelle = $product_obj->libelle;
            $line_order->price = $product_obj->price;
            $line_order->price_str = $product_obj->price_str;
            $line_order->quantity = $product['quantity'];
            $line_order->seller_free_shipping = $product_obj->free_shipping;
            if ($product_obj->free_shipping && isset($product['in_free_shipping_zone'])) {
                $line_order->in_free_shipping_zone = $product['in_free_shipping_zone'];
            }
            $avail = $product_obj->quantity-$product_obj->quantity_selling;
            if (!$product_obj->is_digital && $avail < $line_order->quantity) {
                $data = [
                    'product_id' => $product['id'],
                    'quantity' => $line_order->quantity,
                    'cart' => $cart,
                ];
                throw new InsufficientQuantityException($data);
            }
            $product_obj->quantity -= $line_order->quantity;
            $product_obj->save();
            //////////////////////////////
            /*if (!array_key_exists($idSeller, $seller_tab)) {
                array_push($seller_tab, [
                    "$idSeller"=> [$product_obj],
                ]);
            }else{
                $seller_tab["$idSeller"][] = $product_obj;
            }*/
            if ($product['noshipping'] == 'false') {
                $noOrdershipping = false;
            }

            if (isset($product['pre_order']) && $product['pre_order']) {
                $line_order->orders_id = null;
                $line_order->is_pre_order = 1;
                $line_order->pre_order_pi_users_id = $current_user->id;
                $line_order->pre_order_pi_users_username = $current_user->username;
                $line_order->pre_order_address = $address;
                $line_order->save();
                array_push($line_order_tab, $line_order);
                continue;
            }
            if (isset($product['final_paid_shipping']) && $product['final_paid_shipping']) {
                $total = piket_add($total, $product['fee']);
                $line_order->shipping_info = [
                    'final_paid_shipping'=>true,
                    'fee'=>$product['fee'],
                ];
            }
            if (isset($product['final_free_shipping']) && $product['final_free_shipping']) {
                $line_order->shipping_info = [
                    'final_free_shipping'=>true,
                ];
            }
            $line_order->save();
            /////////////////////////////////////////
            $idSeller = $product_obj->user->id;
            $seller_tab["$idSeller"][] = $this->getLineOrder($line_order->id, -1);

            //$total += intval($product['quantity']) * floatval($product_obj->price);
            $current_total = piket_multiply($product['quantity'], $product_obj->price);
            $total = piket_add($total, $current_total);
        }
        if (piket_sub($total, $user->balance) > 0) {
            throw new InsufficientAmountException("Error Processing Request", 1);
        }
        $order->total = $total;
        if (!$noOrdershipping) {
            $order->noshipping = 0;
            if ($isNewAddress == 'true') {
                $addresses = $current_user->addresses;
                $addresses[] = $address;
                $user->addresses = $addresses;
            }
            $order->shipping = $address;

            foreach ($line_order_tab as $key => $line_order) {
                $line_order->pre_order_address = $address;
                $line_order->save();
            }
        }
        //$buyer->save();
        //////////END ORDER

        //$seller = $product->user;
        $payment = new Payment();
        $payment->confirmed_at = now();
        $payment->type = $paymentData['type'];
        $payment->origin = $paymentData['origin'];
        $payment->pi_users_id = $user->id;
        $payment->pi_users_username = $user->username;
        if (isset($paymentData['paymentId'])) {
            $payment->identifier = $paymentData['paymentId'];
        }
        if (isset($paymentData['txid'])) {
            $payment->txid = $paymentData['txid'];
        }
        $payment->amount = $paymentData['amount'];
        $payment->orders_id = $order->id;
        if (isset($paymentData['public_key'])) {
            $payment->public_key = $paymentData['public_key'];
            $user->public_key = $paymentData['public_key'];
        }
        if (isset($paymentData['uniqueId'])) {
            $payment->unique_id = $paymentData['uniqueId'];
            $order->unique_id = $paymentData['uniqueId'];
        }
        $order->paid = 1;
        $meta = [
            'old_balance'=>$user->wallet->balance,
        ];
        //Setting::increasePiketplaceBalance($payment->amount);

        $user->wallet->balance = piket_sub($user->wallet->balance, $payment->amount);
        $user->cart = [];
        $user->wallet->save();
        $user->save();
        $meta ['new_balance'] = $user->wallet->balance;
        $payment->meta = $meta;
        $payment->save();
        $order->ordered_at = now();
        $order->save();
        
        //////////////////////////////////

        $this->sendPurchaseNotifications($user, null, $seller_tab);
        $this->sendPurchaseEmail($user, null, $seller_tab);

        return $total;
    }

    public function getPayments(Request $request)
    {
        $user = $this->getUpdatedUser($request->user_id);
        $payments = Payment::whereNotNull('confirmed_at')
            ->orderBy('id', "desc")
            //->where('pi_users_id', $user_id)
            ->where(function ($q) use($user){
                $q->where('from_wallet_id', $user->id)
                    ->orWhere('to_wallet_id', $user->id)
                    ->orWhere('wallet_id', $user->id)
                    ->orWhere('pi_users_id', $user->id)
                ;
            })
        ->paginate(50);

        return response()->json([
            'payments' => $payments,
        ]);
    }

    public function pay_piketplace_wallet_preorder(Request $request)
    {
        $user = $this->getUpdatedUser($request->user_id);
        try {
            DB::beginTransaction();
            $line_order = $this->getLineOrder($request->line_order_id);
            if (isset($line_order->order->paid) && $line_order->order->paid===true) {
                return response()->json([
                    'status' => false,
                    'message' => 'already_paid'
                ]);
            }
            $product = $line_order->product;
            $amount = $this->getLineOrderTotalAmountToPay($line_order);

            if (piket_sub($user->balance, $amount) < 0) {
                throw new InsufficientAmountException("Error Processing Request", 1);
                /*return response()->json([
                    'status' => false,
                    'amount' => $amount,
                    'user' => $this->getUpdatedUser($request->user_id),
                    'message' => 'message.donate.not_enough_amount'
                ]);*/
            }
            if ($product->quantity < $line_order->quantity) {
                Log::channel('quantity_insufficient')->info('Low quantity : product_id='.$product->id.', buyer_id='.$line_order->pre_order_pi_users_id);
                $data = [
                    'product_id' => $product->id,
                    'quantity' => $line_order->quantity,
                    'line_order_id' => $line_order->id,
                ];
                throw new InsufficientQuantityException($data);
            }
            $product->update(['quantity' => $product->quantity-$line_order->quantity]);

            $payment = new Payment();
            $payment->confirmed_at = now();
            $payment->type = Payment::PURCHASE;
            $payment->origin = Payment::PIKETPLACE_WALLET;
            $payment->pi_users_id = $user->id;
            $payment->pi_users_username = $user->username;
            $payment->amount = $line_order->total;
            $meta = [
                'old_balance'=>$user->wallet->balance,
            ];
            
            //Setting::increasePiketplaceBalance($payment->amount);

            //$user->wallet->balance -= $payment->amount;
            $user->wallet->balance = piket_sub($user->wallet->balance, $payment->amount);
            $user->wallet->save();
            $meta ['new_balance'] = $user->wallet->balance;
            $payment->meta = $meta;
            $payment->save();

            ////////////////SHIPPING FEE PAYMENT/////////////////////////
            $payment = new Payment();
            $payment->confirmed_at = now();
            $payment->type = Payment::SHIPPING_FEE;
            $payment->origin = Payment::PIKETPLACE_WALLET;
            $payment->pi_users_id = $user->id;
            $payment->pi_users_username = $user->username;
            $payment->amount = $line_order->fee;
            $meta = [
                'old_balance'=>$user->wallet->balance,
            ];
            
            //Setting::increasePiketplaceBalance($payment->amount);

            //$user->wallet->balance -= $payment->amount;
            $user->wallet->balance = piket_sub($user->wallet->balance, $payment->amount);
            $user->wallet->save();
            $meta ['new_balance'] = $user->wallet->balance;
            $payment->meta = $meta;
            $payment->save();
            //////////////////////////////////////////////////////

            //////////ORDER
            $order = new Order();
            $now = new \Datetime();
            $order->reference = $now->format('YmdHis').rand(1, 1000000);
            $order->pi_users_id = $user->id;
            $order->pi_users_username = $user->username;
            $order->noshipping = $line_order->noshipping;
            $order->shipping = $line_order->pre_order_address;
            $order->paid = 1;
            $order->total = $line_order->total;
            $order->ordered_at = $now;
            $order->save();

            $line_order->orders_id = $order->id;
            $line_order->ordered_at = now();
            $line_order->is_pre_order = false;
            $line_order->price = $product->price;
            $line_order->price_str = $product->price;
            $line_order->libelle = $product->libelle;
            $line_order->save();

            $this->sendPurchaseNotifications($user, $line_order);
            $this->sendPurchaseEmail($user, $line_order);

            DB::commit();

            return response()->json([
                'status' => true,
                'user' => $this->getUpdatedUser($request->user_id)
            ]);
        } catch (InsufficientQuantityException $e) {
            Log::info($e->getMessage());
            DB::rollBack();
            return response()->json([
                'user' => $user,
                'status' => false,
                'data' => $e->data,
                'message' => "message.cart.quantity_insufficient",
            ]);
        } catch (InsufficientAmountException $e) {
            Log::error($e->getMessage());
            DB::rollBack();
            return response()->json([
                'user' => $user,
                'status' => false,
                'message' => "message.donate.not_enough_amount",
            ]);
        } catch (\Exception $e) {
            Log::critical($e->getMessage());
            DB::rollBack();
            return response()->json([
                'user' => $user,
                'status' => false,
                'message' => "message.an_error_occured",
            ]);
        }
    }
}
