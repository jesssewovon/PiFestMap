<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\v1\FrontEnd\BusinessProfileController;

use App\Http\Middleware\EnsureTokenIsValid;
use App\Http\Middleware\EnsurePiUserIsValid;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/


Route::group(['middleware' => ['settings']], function() {
	///////////START RESOURCES
	//Route::resource('products', '\App\Http\Controllers\Api\v1\ProductsController');
	Route::group(['middleware' => ['auth:sanctum', 'pi-user-valid', 'owner']], function() {
		Route::resource('business-profiles', '\App\Http\Controllers\Api\v1\FrontEnd\BusinessProfileController');
		Route::resource('items', '\App\Http\Controllers\Api\v1\FrontEnd\ItemController');
		Route::resource('loyalty-cards', '\App\Http\Controllers\Api\v1\FrontEnd\LoyaltyCardController');
		Route::post('/save-business-profile-photo', '\App\Http\Controllers\Api\v1\FrontEnd\HomeController@save_business_profile_photo');
		Route::post('/delete-business-profile-photo', '\App\Http\Controllers\Api\v1\FrontEnd\HomeController@delete_business_profile_photo');
		Route::post('/add-to-cart', '\App\Http\Controllers\Api\v1\FrontEnd\HomeController@add_to_cart');
		Route::post('/delete-from-cart', '\App\Http\Controllers\Api\v1\FrontEnd\HomeController@delete_from_cart');
		Route::get('/get-cart', '\App\Http\Controllers\Api\v1\FrontEnd\HomeController@getCart');
		Route::post('/making-order', '\App\Http\Controllers\Api\v1\FrontEnd\HomeController@making_order');
		Route::post('/award-stamps/{business_profiles_id}/{pi_users_id}', '\App\Http\Controllers\Api\v1\FrontEnd\HomeController@award_stamps');
		Route::post('/switchLocale', '\App\Http\Controllers\Api\v1\FrontEnd\AuthController@switchLocale')->name('switchLocale');
		Route::get('/connected-user-business-profiles', '\App\Http\Controllers\Api\v1\FrontEnd\BusinessProfileController@connected_user_business_profiles')->name('connected_user_business_profiles');
		///////////////////START PI PAYMENT BACKEND
		Route::post('/approve', '\App\Http\Controllers\Api\v1\FrontEnd\PaymentController@approve')->name('approve');
		Route::post('/complete', '\App\Http\Controllers\Api\v1\FrontEnd\PaymentController@complete')->name('complete');
		Route::post('/incomplete', '\App\Http\Controllers\Api\v1\FrontEnd\PaymentController@incomplete')->name('incomplete');
		Route::post('/cancel', '\App\Http\Controllers\Api\v1\FrontEnd\PaymentController@cancel')->name('cancel');
		///////////////////END PI PAYMENT BACKEND
	});
	///////////////NO MIDDLEWARE
});
Route::post('/signin', '\App\Http\Controllers\Api\v1\FrontEnd\AuthController@signin')->name('signin');
Route::get('/index-loading', '\App\Http\Controllers\Api\v1\FrontEnd\HomeController@index_loading');