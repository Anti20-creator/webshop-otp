<?php

namespace App\Http\Livewire;

use Illuminate\Support\Facades\Session;
use Livewire\Component;

class CartCounter extends Component
{
    public $cartCount;
    protected $listeners = ['updateCart' => 'render'];
    
    public function render()
    {
        $this->cartCount = Session::has('cart') ? Session::get('cart')->totalQty : 0;
        return view('livewire.cart-counter');
    }
}
