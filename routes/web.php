<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\AdminController;
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
Route::get('/erase', [ShopController::class, 'erase']);

Route::get('/admin', [AdminController::class, 'index']);
Route::get('/mail', [ShopController::class, 'mail']);

Route::group(['middleware' => ['guest']], function() {

	Route::post('/login', [AdminController::class, 'login'])->name('login.perform');

});

Route::group(['middleware' => ['auth']], function() {

	Route::get('/admin/categories', [AdminController::class, 'categoriesAdminPage']);
	Route::get('/admin/products', [AdminController::class, 'productsAdminPage']);
	Route::get('/admin/products/add', [AdminController::class, 'productsAddAdminPage']);
	Route::get('/admin/products/edit/{id}', [AdminController::class, 'productsEditAdminPage']);
	Route::get('/admin/orders', [AdminController::class, 'ordersAdminPage']);

	Route::post('/admin/categories/create', [AdminController::class, 'createCategory'])->name('admin.createCategory');
	Route::post('/admin/products/create', [AdminController::class, 'createProduct'])->name('admin.createProduct');
	Route::post('/admin/products/edit/{id}', [AdminController::class, 'editProduct']);
	Route::post('/admin/orders/edit-status/{id}', [AdminController::class, 'editOrderStatus']);
	Route::post('/admin/orders/edit-shipping-id/{id}', [AdminController::class, 'editOrderShippingId']);

});