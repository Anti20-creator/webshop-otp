<?php

namespace App\Http\Livewire;

use Illuminate\Support\Facades\Session;
use Livewire\Component;

class CartPrice extends Component
{
    protected $listeners = ['updateCart' => 'render'];
    
    public function render()
    {
        $price = Session::has('cart') ? Session::get('cart')->totalPrice : 0;
        return view('livewire.cart-price', compact('price'));
    }
}
