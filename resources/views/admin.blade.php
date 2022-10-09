@extends('layouts.admin')


@section('content')

	@auth
		{{Auth::user()->email}}
	@endauth

	@guest
		<div class="col-md-4 col-10 mx-auto">
			<form class="mt-4" method="post" action="{{ route('login.perform') }}">
				@csrf
			  <!-- Email input -->
			  <div class="form-outline mb-4">
			    <label class="form-label" for="form2Example1">E-mail</label>
			    <input type="email" id="form2Example1" class="form-control" name="email" value="{{$errors->first('')}}" />
			  </div>

			  <!-- Password input -->
			  <div class="form-outline mb-4">
			    <label class="form-label" for="form2Example2">Jelszó</label>
			    <input type="password" id="form2Example2" class="form-control" name="password" />
			  </div>

			  	@if($errors->any())
					<h6 class="invalid-feedback d-block">{{$errors->first()}}</h6>
				@endif

			  <!-- Submit button -->
			  <button type="submit" class="btn btn-primary btn-block mb-4">Bejelentkezés</button>
			</form>
		</div>
	@endguest

@endsection