@extends('layouts.admin')

@section('content')

	<div class="mt-4 mx-2">
		
		<h2>Rendelések</h2>
		
		<table class="table table-striped">
			<thead>
				<td>Név</td>
				<td>Összérték</td>
				<td>Összmennyiség</td>
				<td>Kifizetve</td>
				<td>Státusz</td>
				<td></td>
				<td></td>
			</thead>
			<tbody>
				@foreach ($orders as $key => $order)
					<?php 
						$price = 0;
						$quantity = 0;

						$items = json_decode($order['items'], true);
						foreach($items as $item) {
							$price += $item['price'];
							$quantity += $item['qty'];
						}

					?>
					<tr>
						<td>{{$order['name']}}</td>
						<td>{{ number_format($price, 0, ' ', ' ') }} Ft</td>
						<td>{{$quantity}}</td>
						<td>{{$order['transaction_id'] != null ? 'Igen' : 'Nem'}}</td>
						<td>
							@if($order['order-status'] == 'none')
								<button class="btn btn-warning">
									Függőben
								</button>
							@endif
							@if($order['order-status'] == 'shipping')
								<button class="btn btn-primary">
									Szállítás alatt
								</button>
							@endif
							@if($order['order-status'] == 'done')
								<button class="btn btn-secondary">
									Teljesítve
								</button>
							@endif
						</td>
  						<td>
  							<button class="btn btn-danger">
  								Törlés
  							</button>
  						</td>
						<td>
							<button class="btn btn-primary" type="button" data-bs-toggle="collapse" data-bs-target="#collapse-{{$key}}" aria-expanded="false" aria-controls="collapseExample">
    							Részletek
  							</button>
  						</td>
				  	</tr>
				  	<tr class="collapse" id="collapse-{{$key}}">
				  		<td class="" colspan="6">
			            	<div>
			            		<table class="mx-auto table table-striped">
			            			<thead>
			            				<td class="p-2">Termék neve</td>
			            				<td class="p-2">Rendelt mennyiség</td>
			            				<td class="p-2">Egységár</td>
			            			</thead>
			            			<tbody>
			            				@foreach($items as $key => $item)
			            					<tr class="my-2">
			            						<td class="p-2">{{$item['item']['name']}}</td>
			            						<td class="p-2">{{$item['qty']}}</td>
			            						<td class="p-2">{{ number_format($item['item']['price'], 0, ' ', ' ') }} Ft</td>
			            					</tr>
			            				@endforeach
			            			</tbody>
			            		</table>

			            		<div class="row">
				            		<div class="col-md-4 col-10 mx-auto">
					            		<form method="post" action="{{ '/admin/orders/edit-shipping-id/'.$order['id'] }}">
					            			@csrf
					            			<input type="hidden" name="order-id" value="{{$order['id']}}">
					            			<input value="{{$order['shipping-id']}}" class="form-control my-2" type="text" name="shipping-id" placeholder="Szállítási azonosító">

					            			<input type="submit" class="form-control btn-primary" value="Mentés">
					            		</form>
				            		</div>

				            		<div class="col-md-4 col-10 mx-auto">
					            		<form method="post" action="{{ '/admin/orders/edit-status/'.$order['id'] }}">
					            			@csrf
					            			<input type="hidden" name="order-id" value="{{$order['id']}}">
					            			<select name="order-status" class="form-control my-2">
					            				<option @if($order['order-status'] == "none") {{"selected"}} @endif value="none">Függőben</option>
					            				<option @if($order['order-status'] == "shipping") {{"selected"}} @endif value="shipping">Szállítás alatt</option>
					            				<option @if($order['order-status'] == "done") {{"selected"}} @endif value="done">Teljesítve</option>
					            			</select>

					            			<input type="submit" class="form-control btn-primary" value="Mentés">
					            		</form>
				            		</div>
			            			
			            		</div>
			            	</div>
			            </td>
					</tr>
			  	@endforeach
			</tbody>
		</table>


	</div>

@endsection