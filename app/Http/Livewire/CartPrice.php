<?php

namespace App\Http\Livewire;

use Livewire\Component;

class CartPrice extends Component
{
    protected $listeners = ['updateCart' => 'render'];
    
    public function render()
    {
        return view('livewire.cart-price');
    }
}
