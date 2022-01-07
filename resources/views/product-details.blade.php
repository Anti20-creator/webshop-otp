@extends('layouts.main')

@section('content')
    <div class="container">
        <div class="col-12">
            <div class="row product-details">
                <div class="col-md-6 p-4">
                    <?php $img = json_decode($product['images'])[0]; ?>
                    <?php $images = json_decode($product['images']); array_shift($images); ?>
                    <img src="{{$img}}" alt="" class="w-100">
                    <div class="d-flex more-images">
                        @foreach ($images as $image)
                            <div class="m-2 w-25" style="background-image:url({{$image}});">
                            </div>
                        @endforeach
                    </div>
                </div>
                <div class="col-md-6 p-4">
                    <h5> {{$category}}/ </h5>
                    <h2> {{$product['name']}} </h2>
                    <p class="mt-3"> {{ number_format($product['price'], 0, ' ', ' ') }} Ft </p>
                    <hr>
                    <p> {{$product['description']}} </p>
                    <hr>

                    <form id="buyForm" action="{{ route('addToCart') }}" method="POST">
                        @csrf
                        <input name="size_id" type="text" hidden value="{{ '1' }}">
                        <input name="size" type="text" hidden value="{{ 'XS' }}">
                        <input name="product_id" type="text" hidden value="{{ $product['id'] }}">
                        <button class="add-to-cart mt-3 w-100">
                            Add to cart
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        $('#buyForm').submit(function(e) {
            e.preventDefault();

            $.ajax({
            type: 'POST',
            url: '../addToCart',
            data: $('#buyForm').serialize(), // serializes the form's elements.
            success: function(data)
            {
                console.log(data); // show response from the php script.
                Livewire.emit('updateCart')
            }
            });
        })
    </script>
@endsection