@extends('layouts.admin')

@section('content')

	<div class="mt-4 container">
		
		<h2>{{$product['name']}} szerkesztése</h2>

		<p>{{$errors->first()}}</p>

		<form method="post" action="{{'/admin/products/edit/'.$product['id'] }}" enctype="multipart/form-data">

			@csrf

			<input class="form-control my-3" type="text" name="product" placeholder="Termék neve" value="{{$product['name']}}">

			<input class="form-control my-3" type="text" name="slug" placeholder="Termék URL" value="{{$product['slug']}}">

			<textarea class="form-control my-3" name="description" placeholder="Termék leírása">{{$product['description']}}</textarea>

			<input class="form-control my-3" type="number" name="price" placeholder="Termék ára" value="{{$product['price']}}" min="0">

			<label>Kategóriák: </label>
			<?php $product_categories = $product->categories->pluck('name')->toArray(); ?>
			<select class="selectpicker" multiple data-live-search="true" name="categories[]">
			  @foreach ($categories as $category)
			  	<option @if(in_array($category["name"], $product_categories)) {{'selected'}} @endif value="{{$category['id']}}">{{$category['name']}}</option>
			  @endforeach
			</select>

			<p class="m-0 mt-3">Termékfotók: </p>

			<div class="old-images d-flex">
				@foreach(json_decode($product->images) as $key => $image)
					<div class="old-image-wrapper col-md-2 col-4 p-2 text-center" id="old-image-{{$key}}">
						<img class="w-100" src="/uploads/thumb_{{$image}}">
						<button class="btn btn-danger mt-2" onclick="event.preventDefault(); removeImage({{$key}})">Törlés</button>
					</div>
				@endforeach
			</div>

			<input type="hidden" name="removedimages" id="removedimages">

			<input id="files" type="file" name="image[]" multiple class="form-control my-3" accept="image/*">

			<div id="preview" class="d-flex"></div>

			<label>Termékváltozatok: </label>
			<div id="variants">
				@foreach($product->sizes as $key => $size)
					<div class="d-flex my-3" id="row-{{$key}}">
						<input readonly type="text" placeholder="Változat neve" class="form-control flex-grow-1" name="variantname[]" value="{{$size['name']}}" />
						<label>Raktár készlet ({{$size['quantity']}}) növelése/csökkentése:</label>
						<input type="number" placeholder="Mennyiség" class="form-control flex-grow-1" name="variantquantity[]" value="{{$size}}" />
						<button onclick="event.preventDefault(); deleteRow({{$key}})" class="btn-danger btn form-control">Törlés</button>
					</div>
				@endforeach
			</div>
			<button id="new-variant" class="form-control btn-secondary btn">Új hozzáadása</button>

			<input type="submit" class="form-control my-3 btn btn-primary">

		</form>

	</div>


	<script type="text/javascript">

		const removedImages = new Set()

		const removeImage = (key) => {
			if(document.querySelector('#old-image-' + key + ' img').style.opacity != 0.2) {
				document.querySelector('#old-image-' + key + ' img').style.opacity = '0.2'
				document.querySelector('#old-image-' + key + ' button').innerHTML = 'Visszavonás'
				removedImages.add(key)
			}else{
				document.querySelector('#old-image-' + key + ' img').style.opacity = '1'
				document.querySelector('#old-image-' + key + ' button').innerHTML = 'Törlés'
				removedImages.delete(key)
			}
			document.querySelector('#removedimages').value = Array.from(removedImages.keys()).join(';')
		}


		const preview = (file) => {
		  const img = document.createElement("img");
		  img.src = URL.createObjectURL(file);  // Object Blob
		  img.alt = file.name;
		  img.setAttribute('class', 'col-md-2 col-4 p-2')
		  document.querySelector('#preview').append(img);
		};

		document.querySelector("#files").addEventListener("change", (ev) => {
		  document.querySelector('#preview').innerHTML = ''

		  if (!ev.target.files) return;
		  [...ev.target.files].forEach(preview);
		});

		let newIndex = document.querySelectorAll('#variants > .d-flex').length

		document.querySelector('#new-variant').addEventListener('click', (e) => {

			e.preventDefault()
			const variants = document.querySelector('#variants')

			const row = document.createElement('div')
			row.setAttribute('class', 'd-flex my-3')
			row.setAttribute('id', 'row-' + newIndex)

			row.innerHTML = `
				<input type="text" placeholder="Változat neve" class="form-control flex-grow-1" name="variantname[]" />
				<input type="number" min="0" placeholder="Változat mennyisége" class="form-control flex-grow-1" name="variantquantity[]" />
				<button onclick="event.preventDefault(); deleteRow(${newIndex})" class="btn-danger btn form-control">Törlés</button>
			`

			variants.append(row)
			newIndex++;

		})

		const deleteRow = (idx) => {
			document.querySelector('#row-' + idx).remove()
		}

	</script>

@endsection