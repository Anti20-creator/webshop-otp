@extends('layouts.admin')

@section('content')

	<div class="mt-4 container">
		
		<h2>Termékek</h2>

	  	<a href="/admin/products/add" class="btn btn-primary btn-block my-4">Hozzáadás</a>	
		
		<table class="table table-hover table-striped table-dark">
			<thead>
				<td>Név</td>
				<td>Ár</td>
				<td>Méretek</td>
				<td>Kedvezményezett</td>
			</thead>
			<tbody>
				@foreach ($products as $product)
					<tr>
						<td>{{$product['name']}}</td>
						<td>{{ number_format($product['price'], 0, ' ', ' ') }} Ft</td>
						<td>{{$product->sizes->implode('name', ', ')}}</td>
						<td>{{$product['discount'] == null ? 'Nincs' : $product['discount']}}</td>
				  	</tr>
			  	@endforeach
			</tbody>
		</table>

		<p>
			<span>asd</span>
		</p>

	</div>

@endsection