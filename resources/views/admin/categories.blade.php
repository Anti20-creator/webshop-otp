@extends('layouts.admin')


@section('content')

	<div class="mt-4 container">
		
		<h2>Kategóriák</h2>

		<form class="mt-4 d-flex" method="post" action="{{ route('admin.createCategory') }}" enctype="multiple/form-data">
			@csrf
			<div class="form-outline flex-grow-1 mr-3">
			    <input placeholder="Kategória neve" type="text" id="form2Example1" class="form-control" name="category" value="{{$errors->first('category')}}" />
		  	</div>

		  	<button type="submit" class="btn btn-primary btn-block ms-3">Hozzáadás</button>	
		</form>
	  	@if($errors->any())
				<h6 class="invalid-feedback d-block">{{$errors->first()}}</h6>
		@endif	
		<ul class="list-group mt-4">
			@for ($i = 0; $i < count($categories); ++$i)
			<li class="list-group-item d-flex justify-content-between align-items-start btn" data-bs-toggle="collapse" data-bs-target="#collapse-{{$i}}">
			    <div class="ms-2 me-auto">
			      <div class="fw-bold">
			      	{{$categories[$i]['name']}}
			      </div>
			    </div>
			    <span class="badge bg-primary rounded-pill">{{$counts[$categories[$i]['id']] ?? 0}}</span>
		    </li>
		    <li class="list-group-item collapse" id="collapse-{{$i}}">
		    	@foreach($categories[$i]->products as $product)
		    		<p>
		    			{{$product['name']}}
		    		</p>
		    	@endforeach
		    </li>	
		  	@endfor
		</ul>
	</div>

@endsection