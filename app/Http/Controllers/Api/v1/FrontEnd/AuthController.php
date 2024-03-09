<?php

namespace App\Http\Controllers\Api\v1\FrontEnd;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\PiUser;
use App\Models\PiUsersAccessToken;
use App\Models\Notification;
use App\Models\Language;

use App\Traits\AuthTrait;
use App\Traits\Helper;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password as PasswordRules;

use Illuminate\Support\Str;

class AuthController extends Controller
{
    use AuthTrait, Helper;

    public function signin(Request $request)
    {
        try {
        	$authResult = $request->authResult;
            $isValid = $this->isPiTokenValid($authResult['accessToken']);
            if (!$isValid) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Access token invalid'
                ]);
            }
            $uid = $authResult['user']['uid'];
            $username = $authResult['user']['username'];
            $user = PiUser::where('username', $username)->whereNull('deleted_at')->first();
            $userData = [
                'state' => 'old',
                'message' => 'Already exists',
            ];
            if (is_null($user)) {
                $this->createUser($authResult);
                $userData['state'] = 'new';
                $userData['message'] = 'Add successfully';
            }
            $user = $this->getUpdatedUser($id = '', $username);
            if ($userData['state'] == 'old') {
                $user->update(['updated_at' => now()]);
            }
            //$user->createWallet();
            if (!is_null($user->deleted_at)) {
                return response()->json([
                    'status' => 'deleted',
                    'message' => "deleted",
                ], 401);
            }
            if ($user->active == false) {
                return response()->json([
                    'status' => 'deactivated',
                    'message' => "deactivated",
                ], 401);
            }
            $permissions = [];
            if (!is_null($user->permissions)) {
                if (is_array($user->permissions)) {
                    $permissions = $user->permissions;
                }else{
                    $permissions = json_decode($user->permissions);
                }
            }
            array_push($permissions, 'role:user');
            $userData['data_cart'] = $this->total($user->cart);
            Auth::guard('pi_users')->login($user, $remember = true);
            $userData['token'] =  $user->createToken('PiketplaceApp', $permissions)->plainTextToken;
            //Log::info('tookkeenn : '.$userData['token']);
            ////////////////////////SET EXPIRES DATE////////////////////////
            $date_valid = $authResult['user']['credentials']['valid_until']['iso8601'];
            list($date, $hour) = explode('T', $date_valid);
            $hour = str_replace('Z', '', $hour);
            $expires_at = new \Datetime($date.' '.$hour);
            DB::table('personal_access_tokens')
                ->where('tokenable_id', $user->id)
                ->where('name', 'PiketplaceApp')
                ->latest()->take(1)->update(['expires_at' => $expires_at])
            ;
            /////////////////////////////////////////////////////////////////
            $userData['status'] = 'success';
            $userData['user'] = $user;/*
            $userData['agreements'] = $this->getAgreements();
            $userData['reasons'] = $this->getReasons();

            $settings = $this->getSettings();
            $userData['purchase_activation'] = $this->getSettingBy('purchase_activation', $settings)==1?true:false;
            $userData['mining_activation'] = $this->getSettingBy('mining_activation', $settings)==1?true:false;
            $userData['delivery_penalties_limit'] = $this->getSettingBy('delivery_penalties_limit', $settings);
            $userData['languages'] = Language::where('active', true)->orderBy('order', 'asc')->get();*/

            return response()->json($userData);
        } catch (Exception $e) {
            Log::info('auth error : '.$e->getMessage());
        }
    }

    public function signout(Request $request)
    {
        //$customAccessToken = $request->header('Authorization');
        //Auth::guard('pi_users')->logout();
        //$request->session()->invalidate();

        //$request->user()->tokens()->delete();

        $request->user()->currentAccessToken()->delete();

        return response()->json(['status' => 'success']);
    }

    public function createUser($authResult)
    {
        $uid = $authResult['user']['uid'];
        $username = $authResult['user']['username'];
        $accessToken = $authResult['accessToken'];

        $user = new PiUser();
        $user->uid = $uid;
        $user->username = $username;
        $user->referred_by = $authResult['referred_by'];
        /*$user->cart = "[]";
        $user->addresses = "[]";*/
        $user->last_notification_page_refresh = (new \Datetime())->format('Y-m-d H:i:s');
        $user->locale = 'en';
        $user->save();
        $user = $this->getUpdatedUser($id = '', $username);

        $notification = new Notification();
        $notification->message = "message.welcome";
        $notification->url = "";
        $notification->pi_users_id = $user->id;
        $notification->save();
        $user->refresh();
        $user->createWallet();

        return $user;
    }

    public function switchLocale(Request $request)
    {
        $lang = $request->lang;
        $user_id = $request->user()->id;

        $user = $this->getUpdatedUser($user_id);
        
        $user->locale = $lang;
        $user->save();
        $user->refresh();

        return response()->json([
            'status' => 'success',
            'user' => $user
        ]);
    }

    public function test(Request $request)
    {
        return response()->json([
            'status' => 'success'
        ]);
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required|exists:pi_users,username',
            //'email' => 'required|email|unique:users,email',
            'password' => [
                'required',
                PasswordRules::min(8)
                    ->letters()
                    ->mixedCase()
                    ->numbers()
                    ->symbols()
                    ->uncompromised()
            ],
            'password_confirmation' => 'required|same:password'
        ],[
            //'email.unique' => 'Email exists',
            'username.exists' => 'No account with username : '.$request->username,
        ]);
        if($validator->fails()){
            return response()->json([
                'status' => true,
                'message' => $validator->errors(),
            ], 422);
        }

        $input = $request->all();

        $password = bcrypt($input['password']);
        $user = $this->getUpdatedUser('', $input['username']);

        if ($user->is_partner) {
            return response()->json([
                'status' => true,
                'message' => 'Partner account exists',
            ]);
        }
        try {
            $user->update(['password' => $password, 'is_partner' => true]);
            $success['token'] =  $user->createToken('PiketplaceApp Partner', ['role-partner'])->plainTextToken;
            $success['user'] = $user;
            $success['status'] = true;
            
            return response()->json($success);

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'An error occured',
            ]);
        }
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required|exists:pi_users,username',
            'password' => 'required',
        ],[
            'username.exists' => 'No account with username : '.$request->username,
        ]);
        if($validator->fails()){
            return response()->json([
                'status' => true,
                'message' => $validator->errors(),
            ], 422);
        }

        if(Auth::guard('pi_users')->attempt(['username' => $request->username, 'password' => $request->password])){

            $user = Auth::guard('pi_users')->user();

            $success['user'] =  $user;
            $success['status'] =  true;
            $success['token'] =  $user->createToken('PiketplaceApp Partner', ['role-partner'])->plainTextToken;
            return response()->json($success);
        }
        else{
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized',
            ], 401);
        }
    }

    public function changePassword(Request $request){
        $validator = Validator::make($request->all(), [
            'password' => [
                'required',
                'confirmed',
                PasswordRules::min(8)
                    ->letters()
                    ->mixedCase()
                    ->numbers()
                    ->symbols()
                    ->uncompromised()
            ],
            //'old_password' => 'required'
        ], [
            'letters' => "letters_required"
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors(),
            ], 200);
            //return response()->json($validator->errors()->toJson(), 400);
        }
        $user = auth()->user();
        /*if (!Hash::check($request->old_password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => "Ancien mot de passe incorrect",
            ]);
        }*/

        $user->forceFill([
            'password' => Hash::make($request->password),
            'remember_token'=> Str::random(60),

        ])->save();
        return response()->json([
            'status' => true,
            'data' => $user,
        ]);
    }
}
