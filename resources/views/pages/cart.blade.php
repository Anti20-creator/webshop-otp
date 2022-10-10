@extends('layouts.main')

@section('content')
    <div class="cart">
        <div class="col-10 row m-auto">
            <div class="col-6 p-3">
                @foreach ($cart->items as $item)
                    <div class="col-12 d-flex">
                       <div class="col p-2">
                            <a href="/product/{{$item['item']['slug']}}">
                                    <img class="w-100" src="{{'/uploads/'.json_decode($item['item']->images)[0]}}" />
                            </a>
                        </div>
                        <div class="col d-flex p-2 flex-column justify-content-center">
                            <a href="/product/{{$item['item']['slug']}}">
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
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="col-6 p-3 d-flex flex-column justify-content-center position-relative" style="max-height: 90vh;">
                <div class="position-fixed" style="width: inherit;">
                    <div class="p-4 m-4">
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
        </div>
        @if($errors->any())
            <div class="position-fixed bottom-0 end-0 p-3">
                <div class="toast" style="display: block;" role="alert" aria-live="assertive" aria-atomic="true">
                  <div class="toast-header">
                    <strong class="me-auto">Sikertelen rendelés</strong>
                    <button onclick="document.querySelector('.toast').remove()" type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Bezárás"></button>
                  </div>
                  <div class="toast-body">
                    <b>{{$errors->first()}}</b>
                  </div>
                </div>
            </div>
        @endif
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