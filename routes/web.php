<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

/*Route::get('/', function () {
    return view('welcome');
});*/

Route::get('/{any}', function (Request $request) {
	$referral_code = $request->referral;
	//dd(floatval("21,33"), $request->referral);
    return view('app', compact('referral_code'));
})->where('any', '^(?!privacy-policy).*$');

/*Route::get('/privacy-policy', function () {
    return view('privacy-policy');
});*/