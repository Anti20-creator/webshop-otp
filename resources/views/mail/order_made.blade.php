<h1>
	Kedves {{$order['name']}}!
</h1>

<p>Sikeresen felvettük a rendelését! A rendelés állapotáról ezen azonosító segítségével kaphat információt a továbbiakban: {{$order['transaction_id']}}</p>

Rendelések:
<table class="mx-auto table table-striped">
	<thead>
		<td class="p-2">Termék neve</td>
		<td class="p-2">Rendelt mennyiség</td>
		<td class="p-2">Egységár</td>
	</thead>
	<tbody>
		@foreach(json_decode($order['items'], true) as $key => $item)
			<tr class="my-2">
				<td class="p-2">{{$item['item']['name']}}</td>
				<td class="p-2">{{$item['qty']}}</td>
				<td class="p-2">{{number_format($item['item']['price'], 0, ' ', ' ')}} Ft</td>
			</tr>
		@endforeach
	</tbody>
</table>