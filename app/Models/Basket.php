<?php

namespace App\Models;

use Illuminate\Http\Request;
use Illuminate\Contracts\Session\Session;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Basket extends Model
{
    
    public $items = [];
    public $totalQty = 0;
    public $totalPrice = 0;

    public function __construct($oldCart) {

        if($oldCart) {
            $this->items = $oldCart->items;
            $this->totalQty = $oldCart->totalQty;
            $this->totalPrice = $oldCart->totalPrice;
            $this->test = $oldCart->test;
        }else{
            $this->items = [];
            $this->totalQty = 0;
            $this->totalPrice = 0;
        }

    }

    public function add(Request $request) {
        $size = $request->input('size');
        $id = $request->input('product_id');
        $item = Product::where('id', $id)->first();
        $variantId = Size::where('product_id', $id)->where('name', $size)->first()->id;


        $storedItem = ['qty' => 0, 'price' => $item->price, 'item' => $item, 'size' => $size, 'variantId' => $variantId];

        if($this->items && array_key_exists($variantId, $this->items)) {
            $storedItem = $this->items[$variantId];
        }

        $storedItem['qty']++;
        $storedItem['price'] = $item->price * $storedItem['qty'];
        $this->items[$variantId] = $storedItem;
        $this->totalQty++;
        $this->totalPrice += $item->price;

        return $storedItem['qty'];
    }


    public function remove(Request $request) {
        $variantId = $request->input('size_id');
        $size = $request->input('size');
        $id = $request->input('product_id');
        $item = Product::all()->where('id', $id)->first();

        $storedItem = null;
        if($this->items && array_key_exists($variantId, $this->items)) {
            $storedItem = $this->items[$variantId];
        }

        if($storedItem) {
            if($storedItem['qty'] > 1) {
                $storedItem['qty']--;
                $storedItem['price'] = $item->price * $storedItem['qty'];
                $this->items[$variantId] = $storedItem;
                $this->totalQty--;
                $this->totalPrice -= $item->price;
                return $storedItem['qty'];
            }else{
                unset($this->items[$variantId]);
                $this->totalQty--;
                $this->totalPrice -= $item->price;
                return 0;
            }
        }else{
            return 0;
        }
    }

    /*public function remove($item, $id) {

        if($this->items && array_key_exists($id, $this->items)) {
            $selectedItem = $this->items[$id];
        }
        $selectedItem['qty']--;
        $selectedItem['price'] = $item->price * $selectedItem['qty'];
        $this->items[$id] = $selectedItem;
        $this->totalQty--;
        $this->totalPrice -= $item->price;

        return $selectedItem;
    }*/
}
