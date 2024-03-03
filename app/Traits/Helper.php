<?php

namespace App\Traits;

use App\Models\PiUser;
use App\Models\Category;
use App\Models\Payment;
use App\Models\FailedPayment;
use App\Models\Product;
use App\Models\LineOrder;
use App\Models\Shop;
use App\Models\Country;
use App\Models\Setting;
use App\Models\Agreement;
use App\Models\Reason;
use App\Models\Validation;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use App\Mail\Purchase;
use App\Mail\Notification as NotificationMail;
use App\Mail\ProductValidation;
use Illuminate\Support\Facades\Mail;

use App\Models\Item;

trait Helper
{
    public function getUpdatedUser($user_id, $username = '')
    {
        $user = PiUser::where(function ($q) use($user_id, $username){
                $q->where('id', $user_id)
                  ->orWhere(DB::raw('BINARY `username`'), $username);
            })
            ->with(['business_profile' => function($q){
                $q->with('business_type');
                $q->with('loyalty_card');
            }])
            ->whereNull('deleted_at')
            ->first();
        /*if (!is_null($user)) {
            $user = $user->makeVisible(['balance', 'token', 'tokenTotal', 'hasOnePendingWithdrawal', 'nbNotification',
                'nbNewMessages', 'isProfilSet', 'shop', 'wallet']);
        }*/

        return $user;
    }

    public function getLineOrder($line_order_id, $paid = true)
    {
        $line_order = LineOrder::/*where('noshipping', false)->*/where('id', $line_order_id)
            ->with(['product'=> function ($query) {
                $query->with('image')->with('country')->with('user');
            }])
            /*->whereHas('order', function ($query) use($paid) {
                $query->with('user');
                if ($paid !== -1) {
                    $query->where('paid', $paid);
                }
            })*/
            ->with(['order'=>function ($query) use($paid) {
                $query->with('user');
                if ($paid !== -1) {
                    $query->where('paid', $paid);
                }
            }])
            ->with('deliver')
            ->with('pre_order_user')
            ->with(['applications'=>function ($query) {
                //$query->where('pi_users_id', $user_applicant_id);
            }])
            ->with('application_selected')
            ->first()
        ;

        return $line_order;
    }

    public function getLineOrderTotalAmountToPay($line_order)
    {
        $amount = piket_multiply($line_order->price, $line_order->quantity);
        if ($line_order->is_pre_order && !is_null($line_order->application_selected)) {
            $amount = piket_add($amount, $line_order->application_selected->fee);
        }
        return $amount;
    }

    public function getUserTransactions_old($user_id)
    {
        $user = $this->getUpdatedUser($user_id);
        $payments = Payment::where(['origin' =>2, 'type'=>1])
            ->whereNotNull('confirmed_at')
            ->orderBy('id', "desc")
            ->where('pi_users_id', $user_id)
            ->orWhere(function ($q){
                $q
                ->whereNull('origin')
                ->orWhere('origin', '<>', 2)
                ;
            })
            ->whereNotNull('confirmed_at')
            ->orderBy('id', "desc")
            ->where('pi_users_id', $user_id)
            ->orWhere(function ($q) use($user){
                $q->where('from_wallet_id', $user->id);
            })
            ->orWhere(function ($q) use($user){
                $q->where('to_wallet_id', $user->id);
            })
            ->orWhere(function ($q) use($user){
                $q->where('wallet_id', $user->id);
            })
            /*->with(['user' =>function ($query) use($user){
                $query->where('uid', $user->uid)
                    ->orWhere(function ($q) use($user){
                        $q->where('from_wallet_id', $user->id);
                    })
                    ->orWhere(function ($q) use($user){
                        $q->where('to_wallet_id', $user->id);
                    })
                ;
            }])*/
        ->get();

        return $payments;
    }

    public function getUserTransactions($user_id, $type=-1, $confirmed=-1)
    {
        $user = $this->getUpdatedUser($user_id);
        $req = Payment::orderBy('id', "desc")
            ->whereNull('deleted_at')
            ->whereNull('cancelled_at')
            ->with('user')
            ->with(['line_order' => function ($q){
                $q->with(['product' => function ($qq){
                    $qq->with('user');
                }]);
                $q->with(['order' => function ($qq){
                    $qq->with('user');
                }]);
            }])
            ->with('payment')
            ->with('from_wallet')
            ->where(function ($q) use($user){
                if ($user) {
                    $q->where('pi_users_id', $user->id);
                    if ($user->wallet) {
                        $q->orWhere('from_wallet_id', $user->wallet->id)
                          ->orWhere('to_wallet_id', $user->wallet->id)
                          ->orWhere('wallet_id', $user->wallet->id);
                    }
                }
            });

        if ($type!=-1) {
            if ($type == 2 && $user) {//If withdraw
                $req->whereHas('user', function ($q) use ($user_id){
                    $q->where('id', $user_id);
                });
            }
            $req->where('type', $type);
        }
        if ($confirmed!==-1) {
            if ($confirmed === true) {
                $req->whereNotNull('confirmed_at');
            }elseif ($confirmed === false) {
                $req->whereNull('confirmed_at');
            }
        }
        $payments = $req->get();

        return $payments;
    }

    public function getCategoriesProducts($user_id)
    {
        $categories_ids = DB::table('categories')
            ->join('products', 'categories.id', '=', 'products.categories_id')
            ->join('pi_users', 'products.pi_users_id', '=', 'pi_users.id')
            ->select('categories.id')
            ->where("pi_users.id", $user_id)
            //->having("count(products.id)", ">", 0)
            ->groupBy("categories.id")
            ->get();
        $tab = [];
        foreach ($categories_ids as $key => $value) {
            array_push($tab, $value->id);
        }
        $categories = Category::whereIn('id', $tab)
            ->with(['products'=> function ($query) use ($user_id) {
                $query->where('pi_users_id', $user_id)
                ->whereHas('image')
                ->with('image')->with('country');
            }])->get();

        $nb = 6;
        $products = Product::with('user')->with('image')->with('comments')
            ->with('category')->orderBy('id', 'desc')
            //->whereHas('image')
            ->whereNull('deleted_at')
            ->where('pi_users_id', $user_id)
            ->take($nb)
            ->get();

        return [
            'products' => $products,
            'categories' => $categories,
        ];
    }

    public function getShops($request)
    {
        $verified = $request->verified_shops;
        $shops = Shop::with(['user'=> function ($query) {
                $query->with(['products'=> function ($query) {
                    $query->with('image');
                }]);
            }]);
        if ($verified == 1) {
            $shops = $shops->where('verified', 1);
        }
        $shops = $shops->get();
        return $shops;
    }

    public function total($cart)
    {
        if (is_null($cart) || count($cart) == 0) {
            return [
                'total' => 0,
                'cart' => []
            ];
        }
        $tab = [];
        $total = 0;
        foreach ($cart as $key => $line) {
            $item = Item::where('id', $line['id'])
                ->first()
            ;
            $data = ['item' => $item, 'qty' => $line['qty']];
            array_push($tab, $data);
            $current_total = piket_multiply($line['qty'], $item->price);
            $total = piket_add($total, $current_total);
        }

        return [
            'total' => $total,
            'cart' => $tab
        ];
    }

    public function getProductById($product_id)
    {
        $product = Product::with('user')
            ->with('image')
            ->with('category')
            ->with('last_validation')
            //->with('images')
            ->with('country')
            ->with(['comments' => function ($q){
                $q->orderBy('id', 'desc');
                $q->with('user');
            }])
            ->with(['line_orders' => function ($query){
                $query->orderBy('id', "desc")
                ->with(['order'=>function ($query){
                    $query->with('user');
                }]);
            }])->where('id', $product_id)
            ->first()
        ;
        return $product;
    }

    public function address_exists($address, $addresses)
    {
        if (empty($addresses)) {
            return false;
        }
        foreach ($addresses as $key => $addr) {
            if ($addr['name']==$address['name'] && $addr['address']==$address['address'] && $addr['city']==$address['city'] && $addr['country_name']==$address['country_name'] && $addr['country_code']==$address['country_code'] && $addr['phone_number']==$address['phone_number']) {
                return true;
            }
        }
        return false;
    }

    public function address_mail_exists($address, $addresses)
    {
        if (empty($addresses)) {
            return false;
        }
        foreach ($addresses as $key => $addr) {
            if ($addr['email']==$address['email']) {
                return true;
            }
        }
        return false;
    }

    public function getSetting($name)
    {
        $setting = Setting::where('name', $name)->first();
        if ($setting) {
            return $setting->value;
        }
        return null;
    }

    public function getSettingBy($name, $settings)
    {
        $setting = $settings->where('name', $name)->first();
        if ($setting) {
            return $setting->value;
        }
        return null;
    }

    public function getSettings()
    {
        return Setting::get();
    }

    public function getAgreements()
    {
        $locales = Agreement::select('locale')->get();
        $tab = [];
        foreach ($locales as $key => $val) {
            $tab['seller_agreements'][$val->locale] = Agreement::where('type', 'seller')->where('locale', $val->locale)->orderBy('order', 'asc')->get()->toArray();
            $tab['shipping_agreements'][$val->locale] = Agreement::where('type', 'shipping')->where('locale', $val->locale)->orderBy('order', 'asc')->get()->toArray();
            $tab['user_agreements'][$val->locale] = Agreement::where('type', 'user')->where('locale', $val->locale)->orderBy('order', 'asc')->get()->toArray();
        }
        return $tab;
    }

    public function getReasons()
    {
        $locales = Reason::select('locale')->get();
        $tab = [];
        foreach ($locales as $key => $val) {
            $tab[$val->locale] = Reason::where('locale', $val->locale)
                ->whereNull('deleted_at')
                ->get()->toArray();
        }
        return $tab;
    }

    public function product_validation($request, $product_id)
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required',
            'reasons' => 'required_if:status,rejected',
        ],[
            //'username.exists' => 'No account with username : '.$request->username,
        ]);
        if($validator->fails()){
            return response()->json([
                'status' => true,
                'message' => $validator->errors(),
            ], 422);
        }
        $product = $this->getProductById($product_id);
        if (is_null($product)) {
            return response()->json([
                'status' => false,
                'message' => 'Product doesnot exist',
            ]);
        }
        if ($product->status=='validated' && $request->status=='validated') {
            return response()->json([
                'status' => false,
                'message' => 'already_validated',
            ]);
        }
        if ($product->status=='rejected' && $request->status=='rejected') {
            return response()->json([
                'status' => false,
                'message' => 'already_rejected',
            ]);
        }
        Validation::create([
            'users_id' => $request->user()->id,
            'products_id' => $product->id,
            'status' => $request->status,
            'reasons' => $request->reasons,
            'comment' => $request->comment,
        ]);
        $product->update([
            'status' => $request->status,
            'validation_product_updated_at' => null,
        ]);
        //////////////NOTIFICATION
        $notification = new Notification();
        $notification->message = "message.notification.product_".$request->status;
        //$notification->type = Notification::TYPE_SELECTION;
        $datas['product_name'] = $product->libelle;
        $notification->datas = $datas;
        $notification->url = "/product/".$product->id;
        $notification->pi_users_id = $product->user->id;
        $notification->save();
        ///////////////SENDING EMAIL///////////////////////////
        $seller = $product->user;
        app()->setLocale($seller->locale);
        $objet = __('product_validation');
        $message = __('your_product_is_status', [
            'product_name' => '<strong>'.$product->libelle.'<strong>',
            'status' => '<strong>'.__($request->status).'<strong>',
        ]);
        $list_reason = [];
        if ($request->status == "rejected") {
            $list_reason = Reason::select("text")->where('locale', $seller->locale)->whereIn('code', $request->reasons)->get();
        }
        $this->sendProductValidationMail($seller, $objet, $message, $list_reason);
        return response()->json([
            'status' => true,
            'product' => $product,
        ]);
    }

    public function setEnglishCountryName()
    {
        $countries = Country::get();
        foreach ($countries as $key => $value) {
            Log::info('pays '.$value->translations);
            $translations = $value->translationsArray;
            $translations['en'] = $value->name;
            $value->translations = json_encode($translations,JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);;
            $value->save();
            //$value->update(['translations' => $translations]);
        }
        return response()->json(['success'=>'You have successfully upload file.']);
    }

    public function getPeriodCode($period)
    {
        if ($period == 1) {
            return 'minute';
        }
        if ($period == 2) {
            return 'hour';
        }
        if ($period == 3) {
            return 'day';
        }
        if ($period == 4) {
            return 'month';
        }
    }

    public function getWithdrawalFeePayment($withdraw)
    {
        $fee_payment = Payment::where('type', Payment::WITHDRAW_FEE)
            ->where('wallet_id', $withdraw->wallet_id)
            ->whereNull('deleted_at')
            ->whereNull('confirmed_at')
            ->whereNull('cancelled_at')
            ->orderBy('id', 'desc')
            ->first()
        ;
        if ($fee_payment->meta['withdraw_id'] != $withdraw->id) {
            return null;
        }
        return $fee_payment;
    }

    public function sendPurchaseNotifications($current_user, $line_order, $seller_tab=null)
    {
        if (!is_null($seller_tab)) {//When not preorder
            //////////BUYER NOTIFICATION/////////////
            $notification = new Notification();
            $notification->message = "message.purchase_notification";
            $notification->url = "/my-orders";
            $notification->pi_users_id = $current_user->id;
            $notification->save();
            //////////SELLER NOTIFICATION///////////
            foreach ($seller_tab as $key => $tab) {
                //$vendeur = $tab[0]->user;
                $vendeur = PiUser::find(intval($key));
                $notification = new Notification();
                $notification->message = "message.sale_notification";
                $notification->datas = [
                    'buyer'=> $current_user->fullnameOrUsername,
                    'nb_product'=> count($tab),
                    'product_name'=> $tab[0]->libelle,
                ];
                $notification->url = "/my-sales";
                $notification->pi_users_id = $vendeur->id;
                $notification->type = Notification::TYPE_SALE;
                $notification->save();
            }
        }
        if (!is_null($line_order)) {//When purchase on preorder
            //////////BUYER NOTIFICATION/////////////
            $notification = new Notification();
            $notification->message = "message.purchase_notification";
            $notification->url = "/my-orders";
            $notification->pi_users_id = $current_user->id;
            $notification->save(); 
            //////////DELIVER NOTIFICATION/////////////
            $deliver = $line_order->deliver;
            $notification = new Notification();
            $notification->message = "message.buyer_already_paid";
            $notification->url = "/my/shipping/apply";
            $notification->pi_users_id = $deliver->id;
            $notification->save();  
            //////////SELLER NOTIFICATION///////////
            $vendeur = $line_order->product->user;
            $notification = new Notification();
            $notification->message = "message.sale_notification";
            $notification->url = "/my-sales";
            $notification->datas = [
                'buyer'=> $current_user->fullnameOrUsername,
                'nb_product'=> $line_order->quantity,
                'product_name'=> $line_order->libelle,
            ];
            $notification->pi_users_id = $vendeur->id;
            $notification->type = Notification::TYPE_SALE;
            $notification->save();
        }
    }

    public function sendPurchaseEmail($current_user, $line_order, $seller_tab=null)
    {
        if (!is_null($seller_tab)) {//When not preorder
            //////////SELLER NOTIFICATION///////////
            foreach ($seller_tab as $key => $tab) {
                $vendeur = PiUser::find(intval($key));
                if (is_null($vendeur->email) || $vendeur->email =='') {
                    continue;
                }
                $line_orders = $tab;
                if (isset($vendeur->email) && $vendeur->email !='') {
                    Mail::to($vendeur)->send(new Purchase($vendeur, $line_orders));
                }
            }
        }
        if (!is_null($line_order)) {//When purchase on preorder
            //////////DELIVER NOTIFICATION/////////////
            //Mail::to($deliver)->send(new Purchase($vendeur, $liste_product));
            //////////SELLER NOTIFICATION///////////
            $vendeur = $line_order->product->user;
            if (isset($vendeur->email) && $vendeur->email !='') {
                Mail::to($vendeur)->send(new Purchase($vendeur, [$line_order]));
            }
        }
    }

    public function getPublicKey($txid)
    {
        $key = $this->getSetting('api_key');
        $pi_url = $this->getSetting('pi_url');

        $client_http = new \GuzzleHttp\Client(["verify"=>false]);
        $rep = $client_http->request('GET', $pi_url.'/transactions/'.$txid, [
            'headers' => [
                'Accept' => 'application/json',
                'Authorization' => 'Key '.$key
            ],
        ]);
        $rep = json_decode($rep->getBody());
        $public_key = $rep->source_account;
        return $public_key;
    }

    public function getPaymentLibelleByType($type)
    {
        $data = [
            '1' => "Dépôt",
            '4' => "Don",
            '5' => "Achat",
        ];
        return isset($data[$type])?$data[$type]:"";
    }

    public function save_failed_pi_payment($paymentId, $txid, $paymentDTO, $from, $error_message)
    {
        $user = $this->getUpdatedUser($paymentDTO->metadata->userId);
        $type = $paymentDTO->metadata->type;
        $amount = $paymentDTO->amount;
        $public_key = $this->getPublicKey($txid);
        $data = [
            'pi_users_id' => $user->id,
            'pi_users_username' => $user->username,
            'type' => $type,
            'origin' => $from,
            'amount' => $amount,
            'public_key' => $public_key,
            'txid' => $txid,
            'identifier' => $paymentId,
            'meta' => json_encode($paymentDTO),
            'error_message' => $error_message
        ];
        if ($type == 5 && isset($paymentDTO->metadata->is_pre_order)) {
            $data['line_order_id'] = $paymentDTO->metadata->line_order_id;
        }elseif($type == 5){
            $data['cart'] = $user->cart;
        }
        FailedPayment::create($data);

        $admins = PiUser::select('email')->where(function ($q){
                $q->where('username', 'djedje00')
                ->orWhere('username', 'Filano');
            })
            ->get();
        $emails = ["support@piketplace.com"];
        foreach ($admins as $key => $value) {
            if (!is_null($value->email) && $value->email!="") {
                $emails[] = $value->email;
            }
        }

        $objet = "Echec paiement";
        $message = "Un paiement échoué, ".$this->getPaymentLibelleByType($type).", type: ".$from.", txid: ".$txid.", paymentId: ".$paymentId;

        Mail::to($emails)->send(new NotificationMail($objet, $message));
    }

    public function sendMail($recipient, $objet, $message)
    {
        if (isset($recipient->email) && $recipient->email!='') {
            Mail::to($recipient)->send(new NotificationMail($objet, $message));
        }
    }

    public function sendProductValidationMail($recipient, $objet, $message, $list_reason = null)
    {
        if (isset($recipient->email) && $recipient->email!='') {
            Mail::to($recipient)->send(new ProductValidation($objet, $message, $list_reason));
        }
    }

    public function getMiningRemainingTime($user)
    {
        $mining_end_at = (new \Datetime($user->last_mining_at))->add(new \DateInterval('P1D'));
        $mining_end_at = strtotime($mining_end_at->format('Y-m-d H:i:s'));
        $now = time();
        $remaining_time = $mining_end_at-$now;//In seconds
        return $remaining_time*1000;//In milliseconds
    }

    public function autoCancelLineOrder($line_order)
    {
        $now = now();
        //$now = null;
        $order = $line_order->order;
        $product = $line_order->product;
        $seller = $product->user;
        $delivery_penalties = $seller->delivery_penalties;
        $seller->update(['delivery_penalties' => $delivery_penalties+1]);
        //Auto cancel order
        $line_order->update(['auto_cancelled_at' => $now]);
        $product->update([
            'active' => false,
            'auto_deactivated_at' => $now
        ]);
        $order->user->payback($line_order, Payment::REFUND_ORDER_AUTO_CANCELLED);
        $recipient = $product->user;
        $objet = __('product_deactivation');
        $data = ["product_name" => '<strong>'.$product->libelle.'</strong>'];
        $message = __('product_deactivated_due_to_unavailability', $data);
        $this->sendMail($recipient, $objet, $message);
    }
}