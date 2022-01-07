@extends('layouts.main')

@section('title')
    Shop
@endsection

@section('content')
    <div class="container-fluid">
        <div class="col-12">
            <div class="row">
                @foreach ($products as $product)
                    <?php
                        $img = json_decode($product['images'])[0]; 
                        $quantity = \App\Models\Size::all()->where('product_id', $product['id'])->sum('quantity');
                    ?>
                    <x-product 
                        name="{{$product['name']}}"
                        price="{{$product['price']}}"
                        image="{{$img}}"
                        slug="{{$product['slug']}}"
                        quantity="{{$quantity}}"
                        created="{{$product['created_at']}}"
                    ></x-product>
                @endforeach

            </div>
        </div>
    </div>
@endsection