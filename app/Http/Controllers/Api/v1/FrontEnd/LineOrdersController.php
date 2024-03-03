<?php

namespace App\Http\Controllers\Api\v1\FrontEnd;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Product;
use App\Models\Image;
use App\Models\LineOrder;
use App\Models\PiUser;
use App\Models\Category;
use App\Models\Payment;
use App\Models\Notification;
use File;
use Validator;
use App\Traits\Helper;
use Illuminate\Support\Facades\Auth;

class LineOrdersController extends Controller
{
    use Helper;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $user_applicant_uid = $request->user_applicant_uid;
        //$uid = 'e526a096-9abf-4bf0-9d0e-88c3e975b29b';
        $req = LineOrder::where('noshipping', false)->orderBy('id', 'desc')->with(['product'=> function ($query) {
                $query->with('image')->with('country');
            }])->with(['order'=>function ($query) {
                $query->where('paid', 1);
            }])->with('deliver');
        if ($user_applicant_uid != '') {
            $req->with(['application_selected'=>function ($query) use ($user_applicant_uid) {
                if ($user_applicant_uid != '') {
                    $query->where('uid', $user_applicant_uid);
                }
                
            }]);
        }
        $line_orders = $req->get();
        return response()->json([
            'line_orders' => $line_orders,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        return [
            "status" => true,
            "product" => []
        ];
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {
        //$uid = 'e526a096-9abf-4bf0-9d0e-88c3e975b29b';
        $line_order = $this->getLineOrder($id);
        $user = $this->getUpdatedUser($request->user()->id);
        return response()->json([
            'line_order' => $line_order,
            'user' => $user,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        /*$line_order = LineOrder::where('id', $id)->with(['product'=> function ($query) {
                $query->with('image')->with('country')->with('user');
            }])->with(['order'=>function ($query) {
                $query->where('paid', true)->with('user');
            }])->with('deliver')->first();*/

        $line_order = $this->getLineOrder($id, $paid = true);
        $buyer = $line_order->order->user;
        $product = $line_order->product;
        $seller = $product->user;

        $now = (new \Datetime)->format('Y-m-d H:i:s');
        if ($request->seller_to_deliver_at == 1) {
            $line_order->seller_to_deliver_at = $now;
            //Notification
            $data['message'] = "message.seller_to_deliver";
            $data['datas'] = [
                'seller'=> $seller->fullnameOrUsername,
                'nb_product'=> $line_order->quantity,
                'product_name'=> $line_order->libelle,
                'deliver'=> $line_order->deliver->fullnameOrUsername,
            ];
            $data['owner'] = $buyer->id;
            $data['type'] = Notification::TYPE_SELLER_TO_DELIVER;

            $recipient = $buyer;
            app()->setLocale($recipient->locale);
            $objet = __('shipping_confirmation');
            $message = __('email_seller_to_deliver');
        }
        if ($request->deliver_from_seller_at == 1) {
            $line_order->deliver_from_seller_at = $now;
            //Notification
            $data['message'] = "message.deliver_from_seller";
            $data['datas'] = [
                'seller'=> $seller->fullnameOrUsername,
                'nb_product'=> $line_order->quantity,
                'product_name'=> $line_order->libelle,
                'deliver'=> $line_order->deliver->fullnameOrUsername,
            ];
            $data['owner'] = $buyer->id;
            $data['type'] = Notification::TYPE_DELIVER_FROM_SELLER;

            $recipient = $buyer;
            app()->setLocale($recipient->locale);
            $objet = __('shipping_confirmation');
            $message = __('email_deliver_from_seller');
        }
        if ($request->deliver_to_buyer_at == 1) {
            $line_order->deliver_to_buyer_at = $now;
            //Notification
            $data['message'] = "message.deliver_to_buyer";
            $data['datas'] = [
                'buyer'=> $buyer->fullnameOrUsername,
                'nb_product'=> $line_order->quantity,
                'product_name'=> $line_order->libelle,
                'deliver'=> $line_order->deliver->fullnameOrUsername,
            ];
            $data['owner'] = $seller->id;
            $data['type'] = Notification::TYPE_DELIVER_TO_BUYER;

            $recipient = $seller;
            app()->setLocale($recipient->locale);
            $objet = __('shipping_confirmation');
            $message = __('email_deliver_to_buyer');
        }
        if ($request->buyer_from_deliver_at == 1) {
            $line_order->shipped_at = $now;
            $line_order->buyer_from_deliver_at = $now;
            //Seller payment
            $line_order->product->user->payback($line_order, Payment::PAYMENT_SELLER);
            //Deliver payment
            $line_order->deliver->payback($line_order, Payment::REFUND_PLEDGING);
            $line_order->deliver->payShipper($line_order);
            //Commission deduction
            $line_order->product->user->sellerPayCommission($line_order);
            $line_order->deliver->shippingPayCommission($line_order);
            //Notification
            $data['message'] = "message.buyer_from_deliver";
            $data['datas'] = [
                'buyer'=> $buyer->fullnameOrUsername,
                'nb_product'=> $line_order->quantity,
                'product_name'=> $line_order->libelle,
                'deliver'=> $line_order->deliver->fullnameOrUsername,
            ];
            $data['owner'] = $seller->id;
            $data['type'] = Notification::TYPE_BUYER_FROM_DELIVER;

            $recipient = $seller;
            app()->setLocale($recipient->locale);
            $objet = __('shipping_confirmation');
            $message = __('email_buyer_from_deliver');
        }
        if ($line_order->deliver != null) {
            $data['url'] = "/shipping-management/line-order/".$line_order->id;
            $this->saveNotification($data);

            $this->sendMail($recipient, $objet, $message);
        }
        
        if ($request->seller_to_buyer_at == 1) {
            $line_order->seller_to_buyer_at = $now;
        }
        if ($request->buyer_from_seller_at == 1) {
            $line_order->buyer_from_seller_at = $now;
            $line_order->shipped_at = $now;
            $seller = $line_order->product->user;
            //Seller payment
            $seller->payback($line_order, Payment::PAYMENT_SELLER);
            //Commission deduction
            $seller->sellerPayCommission($line_order);
            if (isset($line_order->fee) && $line_order->fee>0) {
                $seller->payShipper($line_order);
            }
        }
        if ($request->cancelled != null) {
            $line_order->cancelled_at = $now;
            $line_order->order->cancelled_at = $now;
            $line_order->order->user->payback($line_order, Payment::REFUND_ORDER_CANCELLED);
        }
        $line_order->save();
        $line_order->order->save();
        $line_order->refresh();
        $user = $this->getUpdatedUser($request->user()->id);
        return response()->json([
            'line_order' => $this->getLineOrder($id, $paid = true),
            'user' => $user,
            'status' => true,
        ]);
    }

    public function saveNotification($data)
    {
        $notification = new Notification();
        $notification->message = $data['message'];
        $notification->datas = $data['datas'];
        $notification->pi_users_id = $data['owner'];
        $notification->type = $data['type'];
        $notification->url = $data['url'];
        $notification->save();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
