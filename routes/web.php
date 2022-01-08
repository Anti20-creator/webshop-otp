<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\ShopController;
use Illuminate\Support\Facades\Route;

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

Route::get('/', [HomeController::class, 'index']);

Route::get('/shop', [ShopController::class, 'index']);
Route::get('/product/{slug}', [ShopController::class, 'product']);
Route::get('/cart', [ShopController::class, 'cartPage']);

Route::post('/addToCart', [ShopController::class, 'addToCart'])->name('addToCart');
Route::post('/removeFromCart', [ShopController::class, 'removeFromCart'])->name('removeFromCart');
Route::post('pay', [ShopController::class, 'startPayment'])->name('pay');
