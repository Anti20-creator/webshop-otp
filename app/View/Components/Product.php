<?php

namespace App\View\Components;

use Illuminate\View\Component;

class Product extends Component
{
    public $name;
    public $image;
    public $price;
    public $slug;
    public $quantity;
    public $created;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($name, $image, $price, $slug, $quantity, $created)
    {
        $this->name     = $name;
        $this->image    = $image;
        $this->price    = $price;
        $this->slug     = $slug;
        $this->quantity = $quantity;
        $this->created  = $created;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.product');
    }
}
