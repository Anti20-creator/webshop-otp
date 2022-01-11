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
Route::get('/shop/{category}', [ShopController::class, 'categoryPage']);
Route::get('/product/{slug}', [ShopController::class, 'product']);
Route::get('/cart', [ShopController::class, 'cartPage']);
Route::get('/success-order', [ShopController::class, 'successOrderIndex']);
Route::get('/order-error', [ShopController::class, 'orderErrorIndex']);

Route::post('/addToCart', [ShopController::class, 'addToCart'])->name('addToCart');
Route::post('/removeFromCart', [ShopController::class, 'removeFromCart'])->name('removeFromCart');
Route::post('pay', [ShopController::class, 'startPayment'])->name('pay');
Route::get('/process-good-order', [ShopController::class, 'processGoodOrder']);
Route::get('/process-bad-order', [ShopController::class, 'processBadOrder']);
Route::post('/forward-to-pay', [ShopController::class, 'placeOrder']);
