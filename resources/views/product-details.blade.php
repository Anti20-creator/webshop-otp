@extends('layouts.main')

@section('content')
<div class="container">
    <div class="col-12">
        <div class="row product-details">
            <div class="col-md-6 p-4">
                <?php $img = json_decode($product['images'])[0]; ?>
                <?php $images = json_decode($product['images']); ?>
                <div style="--swiper-navigation-color: #fff; --swiper-pagination-color: #fff" class="swiper mySwiper2">
                    <div class="swiper-wrapper">
                        @foreach ($images as $image)
                        <div class="swiper-slide">
                            <img src="{{'/uploads/'.$image}}" class="w-100">
                        </div>
                        @endforeach
                    </div>
                    <div class="swiper-button-next"></div>
                    <div class="swiper-button-prev"></div>
                </div>
                <div thumbsSlider="" class="swiper mySwiper mt-2">
                    <div class="swiper-wrapper">
                        @foreach ($images as $image)
                        <div class="swiper-slide">
                            <img src="{{'/uploads/'.$image}}" class="w-100">
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            <div class="col-md-6 p-4">
                <h2> {{$product['name']}}</h2>
                <p class="mt-3"> {{ number_format($product['price'], 0, ' ', ' ') }} Ft </p>
                <hr>
                <p> {{$product['description']}} </p>
                <hr>

                <form id="buyForm" action="{{ route('addToCart') }}" method="POST">
                    @csrf
                    <select name="size">
                        @foreach ($sizes as $size)
                        	@if ($size->quantity > 0)
                        		<option value="{{$size->name}}">{{$size->name}}</option>
                        	@else
                        		<option disabled value="{{$size->name}}">{{$size->name}} - (elfogyott)</option>
                        	@endif
                        @endforeach
                    </select>

                    <p class="text-left mt-3"><span class="fw-bold">Készleten:</span> <span id="dynamic-quantity">{{$sizes[0]->quantity}}</span>db</p>

                    <input name="product_id" type="text" hidden value="{{ $product['id'] }}">
                    <button class="add-to-cart mt-3 w-100" id="submit-btn">
                        Add to cart
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
var swiper = new Swiper(".mySwiper", {
    spaceBetween: 10,
    slidesPerView: 4,
    freeMode: true,
    watchSlidesProgress: true,
});
var swiper2 = new Swiper(".mySwiper2", {
    loop: true,
    spaceBetween: 10,
    navigation: {
        nextEl: ".swiper-button-next",
        prevEl: ".swiper-button-prev",
    },
    thumbs: {
        swiper: swiper,
    },
});
$('#buyForm').submit(function(e) {
    e.preventDefault();

    const data = $('#buyForm').serialize();

    $.ajax({
        type: 'POST',
        url: '../addToCart',
        data: data, // serializes the form's elements.
        success: function(data) {
            console.log(data); // show response from the php script.
            Livewire.emit('updateCart')
        }
    });
})

const sizes = <?php echo $sizes; ?>

$('select').on('change', function(e) {
    e.preventDefault()

    const quantity = sizes.find(s => s.name === e.target.value).quantity
    document.querySelector('#dynamic-quantity').innerHTML = quantity
    if(quantity < 1) {
        document.querySelector('#submit-btn').setAttribute('disabled', '')
    }else{
        document.querySelector('#submit-btn').removeAttribute('disabled')
    }

})
</script>
@endsection
