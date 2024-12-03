<?php

use Livewire\Livewire;
use App\Livewire\AddToCart;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Frontend\CartController;
use App\Http\Controllers\Frontend\HomeController;
use App\Http\Controllers\Frontend\AuthenticationController;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::group(
    [
        'prefix' => LaravelLocalization::setLocale(),
        'middleware' => ['localeSessionRedirect', 'localizationRedirect', 'localeViewPath']
    ],
    function () {
        Livewire::setUpdateRoute(function ($handle) {
            return Route::post('/custom/livewire/update', $handle);
        });
        Route::get('/', [HomeController::class, 'index'])->name('index');
        Route::get('/login', [AuthenticationController::class, 'loginView'])->name('login');
        Route::post('/login', [AuthenticationController::class, 'login'])->name('login');
        Route::get('/register', [AuthenticationController::class, 'showRegistrationForm'])->name('register');
        Route::post('/register', [AuthenticationController::class, 'register'])->name('register');
        Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
        Route::get('/products/{id}', [HomeController::class, 'showProduct'])->name('show.product');
        Route::post('/cart/update', [CartController::class, 'update'])->name('cart.update');
        Route::post('/cart/remove', [CartController::class, 'remove'])->name('cart.remove');

        Route::get('/brand/{id}', [HomeController::class, 'getProductsByBrand'])->name('brand');
        Route::get('/category/{id}', [HomeController::class, 'getProductsByCategory'])->name('category');
        Route::group(['middleware' => 'auth:contact'], function () {
            Route::get('/profile', [AuthenticationController::class, 'profile'])->name('profile');
            Route::get('/profile-transaction', [AuthenticationController::class, 'profileTransaction'])->name('profile.transaction');
            Route::get('/profile-edit', [AuthenticationController::class, 'profileEdit'])->name('profile.edit');
            Route::post('/profile-edit', [AuthenticationController::class, 'profileUpdate'])->name('profile.update');
            Route::get('/profile-password', [AuthenticationController::class, 'profilePasswordEdit'])->name('profile.password');
            Route::post('/profile-password', [AuthenticationController::class, 'profilePasswordUpdate'])->name('profile.password.update');
            Route::get('/add-to-cart/{productId}', AddToCart::class)->name('add-to-cart');
            Route::post('/checkout', [CartController::class, 'checkout'])->name('checkout');
            Route::post('/logout', [AuthenticationController::class, 'logout'])->name('logout');
            Route::post('/logout', [AuthenticationController::class, 'logout'])->name('logout');

    });

});

