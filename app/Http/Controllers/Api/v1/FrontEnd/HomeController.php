<?php

namespace App\Http\Controllers\Api\v1\FrontEnd;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Models\Item;
use App\Models\Payment;
use App\Models\BusinessType;
use App\Models\PiUser;
use App\Models\Order;
use App\Models\LineOrder;
use App\Models\Setting;
use App\Models\Language;
use App\Models\Wallet;

use App\Models\UserStamp;
use App\Models\AwardedStamp;

use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

use App\Exceptions\InsufficientQuantityException;

//use App\Helper\Helper;
use App\Traits\Helper;
use App\PiSDK\PiNetwork;

use File;

use App\Models\BusinessProfile;

class HomeController extends Controller
{
    use Helper;

    private $basePath = 'app/public/';
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //$this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        app()->setLocale('fr');
        return view('home');
    }

    public function upload(Request $request)
    {
        $date = date('Y-m-d');
        $path = public_path('tmp/uploads/'.$date);

        if (!file_exists($path)) {
            mkdir($path, 0777, true);
        }
        foreach ($request->file as $key => $value) {
            $imageName = time().$key.'.'.$value->getClientOriginalExtension();

            $value->move(public_path('img'), $imageName);
        }

        /*$imageName = time().'.'.$request->file->getClientOriginalExtension();

        $request->file->move(public_path('img'), $imageName);*/

         

        return response()->json(['success'=>'You have successfully upload file.']);
    }

    public function privacy_policy()
    {
        return view('privacy_policy');
    }

    public function index_loading(Request $request)
    {
        $user = null;
        $settings = $this->getSettings();
        $data['currency'] = $this->getSettingBy('currency', $settings);
        $show_country = $this->getSettingBy('show_country', $settings);
        $data['show_country'] = $show_country==1?true:false;
        $data['show_index_slider'] = $this->getSettingBy('show_index_slider', $settings)==1?true:false;
        $data['business_types'] = BusinessType::whereNull('deleted_at')->get();
        $data_link = $this->getSettingBy('data_link', $settings);
        $data['data_link'] = json_decode($data_link);
        $data['languages'] = Language::where('active', true)->orderBy('order', 'asc')->get();
        $data['mining_activation'] = $this->getSettingBy('mining_activation', $settings)==1?true:false;
        $data['purchase_activation'] = $this->getSettingBy('purchase_activation', $settings)==1?true:false;
        $data['pibrowser_verification'] = $this->getSettingBy('pibrowser_verification', $settings)==1?true:false;
        $data['transfer_fee_pi_network'] = $this->getSettingBy('transfer_fee_pi_network', $settings);
        $data['transfer_fee_piket'] = $this->getSettingBy('transfer_fee_piket', $settings);
        $data['transfer_fee_piket_activation'] = $this->getSettingBy('transfer_fee_piket_activation', $settings)==1?true:false;
        if ($request->user()) {
            $data['user'] = $this->getUpdatedUser($request->user()->id);
        }

        //$business_profiles = $this->getBusinessProfiles();
        $business_profiles = BusinessProfile::whereNull('deleted_at')
            //->whereHas('loyalty_card')
            ->whereHas('user')
            ->with('business_type')->get();
        $data['business_profiles'] = $business_profiles;

        return response()->json($data);
    }

    public function getLanguages(Request $request)
    {
        $languages = Language::where('active', true)->get();
        return response()->json([
            'status' => true,
            'languages' => $languages,
        ]);
    }

    public function save_business_profile_photo(Request $request)
    {
        $directory = 'uploads/business-profile-photos';
        $file = $request->file("photo");
        $date = date('Y-m-d');
        $path = storage_path($this->basePath.$directory.'/'.$date);
        if (!file_exists($path)) {
            mkdir($path, 0777, true);
        }
        $name = uniqid() . '_' . trim($file->getClientOriginalName());
        //$user->{$requestname} = $date.'/'.$name;
        /////////////////////////////////////////////
        $img = \Image::make($file->getRealPath());
        if ($img->width() > 700) {
            $img->orientate();
            $img->resize(500,null, function ($constraint) { $constraint->aspectRatio(); });
        }
        $filesizeInBytes = $img->filesize();
        $filePath = $date.'/'.$name;
        $access_path = asset("storage/uploads/business-profile-photos/".$filePath);
        $img->save($path."/".$name,40);

        $user = $this->getUpdatedUser($request->user()->id);
        $business_profile = $user->business_profile;
        $business_profile_photos = $business_profile->business_profile_photos;
        $business_profile_photos[] = [
            'id' => uniqid(),
            'url' => $filePath,
            'url_http' => $access_path,
            //'path' => $path.'/'.$name,
        ];

        $business_profile->business_profile_photos = $business_profile_photos;
        $business_profile->save();

        return response()->json([
            'status' => true,
            'business_profile' => $business_profile,
            'user' => $this->getUpdatedUser($request->user()->id),
        ]);
    }

    public function delete_business_profile_photo(Request $request)
    {
        $id = $request->id;

        $user = $this->getUpdatedUser($request->user()->id);
        $business_profile = $user->business_profile;
        $business_profile_photos = $business_profile->business_profile_photos;
        $tab = [];
        if (!is_null($business_profile_photos)) {
            foreach ($business_profile_photos as $key => $value) {
                if ($value['id'] == $id) {
                    //unlink
                    if (isset($value['path']) && file_exists($value['path'])) {
                        unlink($value['path']);
                    }
                }else{
                    $tab[] = $value;
                }
            }
        }

        $business_profile->business_profile_photos = $tab;
        $business_profile->save();

        return response()->json([
            'status' => true,
            'business_profile' => $business_profile,
            'user' => $this->getUpdatedUser($request->user()->id),
        ]);
    }

    public function add_to_cart(Request $request)
    {
        $user = $this->getUpdatedUser($request->user()->id);
        $id = $request->id;
        $cart = is_null($user->cart)?[]:$user->cart;
        foreach ($cart as $key => $item) {
            if ($item['id'] == $id) {
                return response()->json([
                    'status' => false,
                    'message' => 'item_exists',
                    'user' => $this->getUpdatedUser($request->user()->id),
                ]);
            }
        }
        $cart[] = ['id'=>$id, 'qty'=>$request->qty];
        Log::info('add_to_cart '.$id);
        $user->update(['cart' => $cart]);

        return response()->json([
            'status' => true,
            'user' => $this->getUpdatedUser($request->user()->id),
        ]);
    }

    public function delete_from_cart(Request $request)
    {
        $user = $this->getUpdatedUser($request->user()->id);
        $id = $request->id;
        $cart = is_null($user->cart)?[]:$user->cart;
        $new_cart = [];
        foreach ($cart as $key => $item) {
            if ($item['id'] != $id) {
                $new_cart[] = $item;
            }
        }
        $user->update(['cart' => $new_cart]);

        return response()->json([
            'status' => true,
            'user' => $this->getUpdatedUser($request->user()->id),
        ]);
    }

    public function getCart(Request $request)
    {
        $user = $this->getUpdatedUser($request->user()->id);
        if (is_null($user->cart) || count($user->cart) == 0) {
            return response()->json([
                'status' => true,
                'cart' => [],
                'user' => $user,
            ]);
        }
        $cart = $user->cart;
        $data = $this->total($cart);

        return response()->json([
            'status' => true,
            'total' => $data['total'],
            'cart' => $data['cart'],
            'user' => $user,
        ]);
    }

    public function making_order(Request $request)
    {
        $user = $this->getUpdatedUser($request->user()->id);
        DB::beginTransaction();
        $now = now();
        try {
            $cart = $request->shopping_cart;
            $data_order = [
                'pi_users_id' => $user->id,
                'ordered_at' => $now,
            ];
            $order = Order::create($data_order);
            $total = 0;
            foreach ($cart as $key => $line) {
                $item = Item::where('id', $line['id'])->first();
                $data_line_order = [
                    'items_id' => $item->id,
                    'orders_id' => $order->id,
                    'name' => $item->name,
                    'price' => $item->price,
                    'quantity' => $line['qty'],
                    'ordered_at' => $now,
                ];
                $total = piket_multiply($item->price, $line['qty']);
                $line_order = LineOrder::create($data_line_order);
            }
            $order->update(['total' => $total]);
            DB::commit();
            return response()->json([
                'status' => true,
                'user' => $this->getUpdatedUser($request->user()->id),
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
                'user' => $this->getUpdatedUser($request->user()->id),
            ]);
        }
    }

    public function award_stamps(Request $request, $business_profiles_id, $pi_users_id)
    {
        $nb_stamps = $request->nb_stamps;
        $data = [
            'business_profiles_id' => $business_profiles_id,
            'pi_users_id' => $pi_users_id,
            'nb_stamps_awarded' => $nb_stamps,
        ];
        AwardedStamp::create($data);
        $user_stamps = UserStamp::where('business_profiles_id', $business_profiles_id)
            ->where('pi_users_id', $pi_users_id)
            ->first();
        if (is_null($user_stamps)) {
            $data['nb_stamps'] = $nb_stamps;
            $user_stamps = UserStamp::create($data);
        }else{
            $total_stamps = intval($nb_stamps)+$user_stamps->nb_stamps;
            $user_stamps->update(['nb_stamps' => $total_stamps]);
        }

        return response()->json([
            'status' => true,
            'user' => $this->getUpdatedUser($request->user()->id),
        ]);
    }
}
