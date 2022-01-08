<?php

namespace App\Http\Controllers;

use App\Models\Basket;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
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
        $product = Product::all()->where('slug', $slug)->first();
        if(!$product) abort(404);

        $category = Category::all()->where('id', $product['category_id'])->first()['name'];

        return view('product-details', compact('product', 'category'));
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

    public function startPayment()
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
        foreach ($cart->items as $item) {
            $trx->addItems(
                array(
                    'ref' => $item['item']['id'],
                    'title' => $item['item']['name'],
                    //'desc' => $item['item']['description'],
                    'amount' => $item['qty'],
                    'price' => $item['item']['price']
                )
            );
        }
        /*
        $trx->addItems(
            array(
                'ref' => 'Product ID 1',
                'title' => 'Product name 1',
                'desc' => 'Product description 1',
                'amount' => '1',
                'price' => '5',
                'tax' => '0',
                )
        );

        $trx->addItems(
            array(
                'ref' => 'Product ID 2',
                'title' => 'Product name 2',
                'desc' => 'Product description 2',
                'amount' => '1',
                'price' => '2',
                'tax' => '0',
                )
        );*/


        // OPTIONAL DATA INPUT ON PAYMENT PAGE
        //-----------------------------------------------------------------------------------------
        //$trx->addData('maySelectEmail', true);
        //$trx->addData('maySelectInvoice', true);
        //$trx->addData('maySelectDelivery', ['HU']);


        // SHIPPING COST
        //-----------------------------------------------------------------------------------------
        //$trx->addData('shippingCost', 20);


        // DISCOUNT
        //-----------------------------------------------------------------------------------------
        //$trx->addData('discount', 10);


        // ORDER REFERENCE NUMBER
        // uniq oreder reference number in the merchant system
        //-----------------------------------------------------------------------------------------
        $trx->addData('orderRef', str_replace(array('.', ':', '/'), "", @$_SERVER['SERVER_ADDR']) . @date("U", time()) . rand(1000, 9999));


        // CUSTOMER
        // customer's name
        //-----------------------------------------------------------------------------------------
        //$trx->addData('customer', 'v2 SimplePay Teszt');


        // customer's registration mehod
        // 01: guest
        // 02: registered
        // 05: third party
        //-----------------------------------------------------------------------------------------
        $trx->addData('threeDSReqAuthMethod', '02');


        // EMAIL
        // customer's email
        //-----------------------------------------------------------------------------------------
        $trx->addData('customerEmail', 'sdk_test@otpmobil.com');


        // LANGUAGE
        // HU, EN, DE, etc.
        //-----------------------------------------------------------------------------------------
        $trx->addData('language', 'HU');


        // TWO STEP
        // true, or false
        // If this field does not exist is equal false value
        // Possibility of two step needs IT support setting
        //-----------------------------------------------------------------------------------------
        /*
        if (isset($_REQUEST['twoStep'])) {
            $trx->addData('twoStep', true);
        }
        */

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
        $trx->addData('url', $config['URL']);

        // uniq URL for every result type
        /*
            $trx->addGroupData('urls', 'success', $config['URLS_SUCCESS']);
            $trx->addGroupData('urls', 'fail', $config['URLS_FAIL']);
            $trx->addGroupData('urls', 'cancel', $config['URLS_CANCEL']);
            $trx->addGroupData('urls', 'timeout', $config['URLS_TIMEOUT']);
        */


        // Redirect from Simple app to merchant app
        //-----------------------------------------------------------------------------------------
        //$trx->addGroupData('mobilApp', 'simpleAppBackUrl', 'myAppS01234://payment/123456789');


        // INVOICE DATA
        //-----------------------------------------------------------------------------------------
        $trx->addGroupData('invoice', 'name', 'SimplePay V2 Tester');
        //$trx->addGroupData('invoice', 'company', '');
        $trx->addGroupData('invoice', 'country', 'hu');
        $trx->addGroupData('invoice', 'state', 'Budapest');
        $trx->addGroupData('invoice', 'city', 'Budapest');
        $trx->addGroupData('invoice', 'zip', '1111');
        $trx->addGroupData('invoice', 'address', 'Address 1');
        //$trx->addGroupData('invoice', 'address2', 'Address 2');
        //$trx->addGroupData('invoice', 'phone', '06201234567');


        // DELIVERY DATA
        //-----------------------------------------------------------------------------------------
        /*
        $trx->addGroupData('delivery', 'name', 'SimplePay V2 Tester');
        $trx->addGroupData('delivery', 'company', '');
        $trx->addGroupData('delivery', 'country', 'hu');
        $trx->addGroupData('delivery', 'state', 'Budapest');
        $trx->addGroupData('delivery', 'city', 'Budapest');
        $trx->addGroupData('delivery', 'zip', '1111');
        $trx->addGroupData('delivery', 'address', 'Address 1');
        $trx->addGroupData('delivery', 'address2', '');
        $trx->addGroupData('delivery', 'phone', '06203164978');
        */


        //payment starter element
        // auto: (immediate redirect)
        // button: (default setting)
        // link: link to payment page
        //-----------------------------------------------------------------------------------------
        $trx->formDetails['element'] = 'button';


        //create transaction in SimplePay system
        //-----------------------------------------------------------------------------------------
        $trx->runStart();


        //create html form for payment using by the created transaction
        //-----------------------------------------------------------------------------------------
        $trx->getHtmlForm();


        //print form
        //-----------------------------------------------------------------------------------------
        print $trx->returnData['form'];


        // test data
        //-----------------------------------------------------------------------------------------
        print "API REQUEST";
        print "<pre>";
        print_r($trx->getTransactionBase());
        print "</pre>";

        print "API RESULT";
        print "<pre>";
        print_r($trx->getReturnData());
        print "</pre>";
    }
}
