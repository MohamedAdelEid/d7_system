<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::group(['prefix' => 'users'], function(){
    Route::post('login', 'App\Http\Controllers\Api\user\authentication\LoginController@login');
    Route::post('register', 'App\Http\Controllers\Api\user\authentication\registrationController@register');

    Route::group(['middleware' => 'checkJWTTokenMiddelware:user_api'], function(){
        Route::post('logout', 'App\Http\Controllers\Api\user\authentication\LoginController@logout');

        Route::group(['prefix' => 'profile'], function(){
            Route::get('/', 'App\Http\Controllers\Api\user\authentication\profileController@show');
            Route::post('update', 'App\Http\Controllers\Api\user\authentication\profileController@update');
            Route::post('changePassword', 'App\Http\Controllers\Api\user\authentication\profileController@changePassword');
            Route::post('changeImage', 'App\Http\Controllers\Api\user\authentication\profileController@changeImage');
        });
    });
});

Route::group(['prefix' => 'contacts'], function(){
    Route::get('products', 'App\Http\Controllers\Api\contact\ProductController@allProducts');
    Route::get('show/{id}/product', 'App\Http\Controllers\Api\contact\ProductController@showProduct');
    Route::get('brand/{id}/products', 'App\Http\Controllers\Api\contact\ProductController@getProductsByBrand');
    Route::get('branch/{id}/products', 'App\Http\Controllers\Api\contact\ProductController@getProductsByBranch');
    Route::post('login', 'App\Http\Controllers\Api\contact\AuthenticationController@login');
    Route::post('register', 'App\Http\Controllers\Api\contact\authentication\RegistrationContactController@register');
    Route::group(['checkJWTTokenMiddelware:contact_api'], function(){
        Route::post('logout', 'App\Http\Controllers\Api\contact\authentication\LoginContactController@logout');
        Route::post('checkout', 'App\Http\Controllers\Api\contact\SellController@checkOut');
        Route::post('update/order', 'App\Http\Controllers\Api\contact\SellController@updateOrder');
        Route::post('canceled/order', 'App\Http\Controllers\Api\contact\SellController@canceledOrder');
        Route::group(['prefix' => 'profile'], function(){
            Route::get('/', 'App\Http\Controllers\Api\contact\authentication\ProfileContactController@show');
            Route::post('update', 'App\Http\Controllers\Api\contact\authentication\ProfileContactController@update');
        });
    });
});
