<?php

namespace App\Http\Livewire;

use Livewire\Component;

class AddToCart extends Component
{
    public $id;

    public function add()
    {
        $cart = 
    }

    public function mount($id)
    {
        $this->id = $id;
    }

    public function render()
    {
        return view('livewire.add-to-cart');
    }
}

?>