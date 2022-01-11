@extends('layouts.main')

@section('content')
    <div>
        <div class="col-10 row m-auto">
            <div class="col-6 p-3">
                @foreach ($cart->items as $item)
                    <div class="col-12 d-flex">
                        <div class="col p-2">
                            <img class="w-100" src="{{json_decode($item['item']->images)[0]}}" />
                        </div>
                        <div class="col d-flex p-2 flex-column justify-content-center">
                            <p class="m-0 text-uppercase">{{$item['item']->name}} ({{ $item['size'] }})</p>
                            <p class="m-0">{{$item['item']->price}} Ft</p>
                            <br>
                            <div class="d-flex">
                                <form class="cart-forms remove" action="{{ route('removeFromCart') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="product_id" value="{{$item['item']->id}}">
                                    <input type="hidden" name="size" value="{{$item['size']}}">
                                    <input type="hidden" name="size_id" value="{{$item['variantId']}}">
                                    <button>-</button>
                                </form>
                                <input class="cart-input" id="counter-{{$item['variantId']}}" value="{{$item['qty']}}">
                                <form class="cart-forms add" action="{{ route('addToCart') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="product_id" value="{{$item['item']->id}}">
                                    <input type="hidden" name="size" value="{{$item['size']}}">
                                    <input type="hidden" name="size_id" value="{{$item['variantId']}}">
                                    <button>+</button>
                                </form>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="col-6 p-3">
                <h5 class="text-uppercase font-weight-normal">Összesítés</h5>
                <div class="cart-box p-4">
                    <p class="m-0">Fizetendő összesen:</p>
                    @livewire('cart-price')
                    <form class="mt-3 cart-form" action="/pay" method="POST">
                        @csrf
                        <input class="form-control m-0" type="text" placeholder="Kupon kód">
                        <input class="form-control m-0" type="submit" value="Fizetés">
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        $('.remove').submit(function(e) {
            e.preventDefault();

            $.ajax({
            type: 'POST',
            url: '../removeFromCart',
            data: $(e.currentTarget).serialize(), // serializes the form's elements.
            success: function(data)
            {
                $(e.currentTarget).siblings().find('.cart-input').prevObject[0].value = data
                Livewire.emit('updateCart')
            }
            });
        });

        $('.add').submit(function(e) {
            e.preventDefault();

            $.ajax({
            type: 'POST',
            url: '../addToCart',
            data: $(e.currentTarget).serialize(), // serializes the form's elements.
            success: function(data)
            {
                $(e.currentTarget).siblings().next('.cart-input')[0].value = data
                Livewire.emit('updateCart')
            }
            });
        });
    </script>
@endsection