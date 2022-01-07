<?php

namespace App\Http\Controllers;

use App\Models\Basket;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

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

    public function addToCart(Request $request) {
        $oldCart = Session::has('cart') ? Session::get('cart') : null;
        $cart = new Basket($oldCart);
        $result = $cart->add($request);
        //$result = $cart->add($addedProduct, $addedProduct['id']);
        //$addedProduct = Product::where('id', $request['id'])->first();
        Session::put('cart', $cart);
    }
}
