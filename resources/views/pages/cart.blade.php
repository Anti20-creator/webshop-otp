@extends('layouts.main')

@section('content')
    <div>
        <div class="col-12">
            <div class="col-6">
                @foreach ($cart->items as $item)
                    <p>{{$item['item']->name}} - {{ $item['qty'] }}</p>
                @endforeach
            </div>
            <div class="col-6">
                <p>{{ $cart->totalPrice }} Ft</p>
            </div>
        </div>
    </div>
@endsection