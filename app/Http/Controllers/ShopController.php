<?php

namespace App\Http\Controllers;

use App\Models\Basket;
use App\Models\Category;
use App\Models\Order;
use App\Models\Size;
use App\Models\ProductCategory;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use SimplePayStart;

class ShopController extends Controller
{
    public function index()
    {
        $products = Product::paginate(12);
        return view('shop', compact('products'));
    }

    public function product($slug)
    {
        $product = Product::where('slug', $slug)->first();
        if(!$product) abort(404);

        $sizes = Size::where('product_id', $product['id'])->orderBy('name', 'DESC')->get();

        return view('product-details', compact('product', 'sizes'));
    }

    public function categoryPage($category)
    {
        $category_id = Category::where('name', $category)->first()->id;
        $product_ids = ProductCategory::where('category_id', $category_id)->pluck('product_id');
        $products = Product::whereIn('id', $product_ids)->paginate(12);

        return view('pages.category', compact('products'));
    }

    public function cartPage()
    {
        $oldCart = Session::has('cart') ? Session::get('cart') : null;
        $cart = new Basket($oldCart);

        return view('pages.cart', compact('cart'));
    }

    public function addToCart(Request $request) {
        $oldCart = Session::has('cart') ? Session::get('cart') : null;
        $cart = new Basket($oldCart);
        $result = $cart->add($request);

        Session::put('cart', $cart);
        return $result;
    }

    public function removeFromCart(Request $request) {
        $oldCart = Session::has('cart') ? Session::get('cart') : null;
        $cart = new Basket($oldCart);
        $result = $cart->remove($request);

        Session::put('cart', $cart);
        return $result;
    }

    public function successOrderIndex()
    {
        if (count(explode('process-good-order', url()->previous())) > 1) {
            return view('pages.success-order');
        }
        return abort(404);
    }
    
    public function processGoodOrder()
    {
        $items = Session::get('cart')->items;
        $data = Session::get('customer');
        DB::table('orders')->insert([
            'name' => $data['name'],
            'email' => $data['email'],
            'city' => $data['city'],
            'zip' => $data['zip'],
            'address' => $data['address'],
            'phone' => $data['phone'],
            'transaction_id' => $data['transaction_id'],
            'order_ref' => $data['order_ref'],
            'items' => json_encode($items)
        ]);

        Session::put('cart', null);
        return redirect('/success-order');
    }

    public function processBadOrder()
    {
        return redirect('/success-order');
    }

    public function orderErrorIndex()
    {
        return view('pages.order-error');
    }

    public function placeOrder(Request $request)
    {
        require_once 'src/config.php';
        require_once 'src/SimplePayV21.php';

        $trx = new SimplePayStart;

        $currency = 'HUF';
        $trx->addData('currency', $currency);

        $trx->addConfig($config);


        $cart = Session::has('cart') ? Session::get('cart') : null;

        if(!$cart) return redirect('/shop');
        //ORDER PRICE/TOTAL
        //-----------------------------------------------------------------------------------------
        $trx->addData('total', $cart->totalPrice);


        //ORDER ITEMS
        //-----------------------------------------------------------------------------------------
        $items_in_db = Size::whereIn('id', array_keys($cart->items))->get();
        foreach($items_in_db as $item) {
            if($item['quantity'] < $cart->items[$item['id']]['qty']) {
                dd('nincs elég elem!');
                //return redirect('/shop');
            }
        }
        DB::transaction(function() use ($cart) {
            foreach($cart->items as $item) {
                $db_item = Size::where('id', $item['variantId'])->first();
                $db_item->timestamps = false;
                $db_item->quantity -= $item['qty'];
                $db_item->save();
            }
        });
        foreach ($cart->items as $item) {
            $trx->addItems(
                array(
                    'ref' => $item['item']['id'],
                    'title' => $item['item']['name']." (".$item['size'].")",
                    //'desc' => $item['item']['description'],
                    'amount' => $item['qty'],
                    'price' => $item['item']['price']
                )
            );
        }

        // OPTIONAL DATA INPUT ON PAYMENT PAGE
        //-----------------------------------------------------------------------------------------
        //$trx->addData('maySelectEmail', true);
        //$trx->addData('maySelectInvoice', true);
        //$trx->addData('maySelectDelivery', ['HU']);


        // ORDER REFERENCE NUMBER
        // uniq oreder reference number in the merchant system
        //-----------------------------------------------------------------------------------------
        $trx->addData('orderRef', str_replace(array('.', ':', '/'), "", @$_SERVER['SERVER_ADDR']) . @date("U", time()) . rand(1000, 9999));


        // LANGUAGE
        // HU, EN, DE, etc.
        //-----------------------------------------------------------------------------------------
        $trx->addData('language', 'HU');


        // TIMEOUT
        // 2018-09-15T11:25:37+02:00
        //-----------------------------------------------------------------------------------------
        $timeoutInSec = 600;
        $timeout = @date("c", time() + $timeoutInSec);
        $trx->addData('timeout', $timeout);


        // METHODS
        // CARD or WIRE
        //-----------------------------------------------------------------------------------------
        $trx->addData('methods', array('CARD'));


        // REDIRECT URLs
        //-----------------------------------------------------------------------------------------

        // common URL for all result
        //$trx->addData('url', $config['URL']);

        // uniq URL for every result type
        $trx->addGroupData('urls', 'success', $config['URLS_SUCCESS']);
        $trx->addGroupData('urls', 'fail', $config['URLS_FAIL']);
        $trx->addGroupData('urls', 'cancel', $config['URLS_CANCEL']);
        $trx->addGroupData('urls', 'timeout', $config['URLS_TIMEOUT']);


        // Redirect from Simple app to merchant app
        //-----------------------------------------------------------------------------------------
        //$trx->addGroupData('mobilApp', 'simpleAppBackUrl', 'myAppS01234://payment/123456789');


        /*// INVOICE DATA
        //-----------------------------------------------------------------------------------------
        $trx->addGroupData('invoice', 'name', 'SimplePay V2 Tester');
        //$trx->addGroupData('invoice', 'company', '');
        $trx->addGroupData('invoice', 'country', 'hu');
        $trx->addGroupData('invoice', 'state', 'Budapest');
        $trx->addGroupData('invoice', 'city', 'Budapest');
        $trx->addGroupData('invoice', 'zip', '1111');
        $trx->addGroupData('invoice', 'address', 'Address 1');
        //$trx->addGroupData('invoice', 'address2', 'Address 2');
        //$trx->addGroupData('invoice', 'phone', '06201234567');*/


        //payment starter element
        // auto: (immediate redirect)
        // button: (default setting)
        // link: link to payment page
        //-----------------------------------------------------------------------------------------
        $trx->formDetails['element'] = 'auto';

        // SHIPPING COST
        //-----------------------------------------------------------------------------------------
        $trx->addData('shippingCost', 0);


        // DISCOUNT
        //-----------------------------------------------------------------------------------------
        $trx->addData('discount', 0);

        // CUSTOMER
        // customer's name
        //-----------------------------------------------------------------------------------------
        $trx->addData('customer', $request->input('customer_name'));


        // customer's registration mehod
        // 01: guest
        // 02: registered
        // 05: third party
        //-----------------------------------------------------------------------------------------
        $trx->addData('threeDSReqAuthMethod', '01');

        // EMAIL
        // customer's email
        //-----------------------------------------------------------------------------------------
        $trx->addData('customerEmail', $request->input('email'));

        // DELIVERY DATA
        //-----------------------------------------------------------------------------------------
        $trx->addGroupData('delivery', 'name', $request->input('customer_name'));
        $trx->addGroupData('delivery', 'country', 'hu');
        $trx->addGroupData('delivery', 'city', $request->input('city'));
        $trx->addGroupData('delivery', 'zip', $request->input('zip'));
        $trx->addGroupData('delivery', 'address', $request->input('address'));
        $trx->addGroupData('delivery', 'phone', $request->input('phone'));

        Session::remove('customer');
        
        //create transaction in SimplePay system
        //-----------------------------------------------------------------------------------------
        $trx->runStart();
        
        Session::put('customer', array(
            'name' => $request->input('customer_name'),
            'email' => $request->input('email'),
            'city' => $request->input('city'),
            'zip' => $request->input('zip'),
            'address' => $request->input('address'),
            'phone' => $request->input('phone'),
            'transaction_id' => $trx->getReturnData()['transactionId'],
            'order_ref' => $trx->getReturnData()['orderRef']
        ));

        //create html form for payment using by the created transaction
        //-----------------------------------------------------------------------------------------
        $trx->getHtmlForm();
        //$form = $trx->returnData['form'];
        
        $link = explode('"', explode('action="', $trx->returnData['form'])[1])[0];
        return redirect($link);
    }

    public function startPayment()
    {

        /*// test data
        //-----------------------------------------------------------------------------------------
        print "API REQUEST";
        print "<pre>";
        print_r($trx->getTransactionBase());
        print "</pre>";

        print "API RESULT";
        print "<pre>";
        print_r($trx->getReturnData());
        print "</pre>";*/

        return view('pages.order-informations');
    }

    public function erase()
    {
        Size::truncate();
    }
}