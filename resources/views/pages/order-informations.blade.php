@extends('layouts.main')

@section('content')
    <div class="pt-5 col-8 m-auto p-5">
        <form class="order-form" action="/forward-to-pay" method="POST">
            @csrf
            <input class="form-control m-3" type="text" name="customer_name" placeholder="Name">
            <input class="form-control m-3" type="email" name="email" placeholder="Email">
            <input class="form-control m-3" type="text" name="city" placeholder="City">
            <input class="form-control m-3" type="text" name="zip" placeholder="Zip">
            <input class="form-control m-3" type="text" name="address" placeholder="Address">
            <input class="form-control m-3" type="text" name="phone" placeholder="Phone">
            <input class="form-control m-3" type="submit" value="Tovább a fizetéshez">
        </form>
    </div>
@endsection