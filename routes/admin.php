<?php

use App\Http\Controllers\UsersController;
use Illuminate\Support\Facades\Route;
use Illuminate\Routing\Router;
use Illuminate\Http\Request;
use App\Http\Controllers\dashboard\WelcomeController;
use App\Http\Controllers\dashboard\UserController;
use App\Http\Controllers\dashboard\CategoryController;
use App\Http\Controllers\dashboard\ProductController;
use App\Http\Controllers\dashboard\ClientController;
use App\Http\Controllers\dashboard\client\OrderController;


/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
|
| Here is where you can register admin routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "admin" middleware group. Now create something great!
|
*/

Route::group(['prefix' => LaravelLocalization::setLocale(), 'middleware' => [ 'localeSessionRedirect', 'localizationRedirect', 'localeViewPath' ]], function(){ 
        
        Route::prefix('dashboard')->name('dashboard.')->middleware(['auth'])->group(function() {

            Route::get('/', [WelcomeController::class, 'index'])->name('welcome');

            // Users Routes
            
            Route::resource('users', UserController::class)->except(['show']);

            // Clients Routes
            
            Route::resource('clients', ClientController::class)->except(['show']);
            Route::resource('clients.orders', OrderController::class)->except(['show']);

            // Category Routes
            
            Route::resource('categories', CategoryController::class)->except(['show']);

            // Product Routes
            
            Route::resource('products', ProductController::class)->except(['show']);

            // orders Routes
            
            Route::resource('orders', App\Http\Controllers\dashboard\OrderController::class)->except(['show']);
            Route::get('/orders/{order}/products', [App\Http\Controllers\dashboard\OrderController::class, 'products'])->name('orders.products');
        
        });

});



