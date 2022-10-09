@extends('layouts.admin')

@section('content')

	<div class="mt-4 container">
		
		<h2>Új termék hozzáadása</h2>

		<form method="post" action="{{ route('admin.createProduct') }}" enctype="multipart/form-data">

			@csrf

			<input class="form-control my-3" type="text" name="product" placeholder="Termék neve">

			<input class="form-control my-3" type="text" name="slug" placeholder="Termék URL">

			<textarea class="form-control my-3" name="description" placeholder="Termék leírása"></textarea>

			<input class="form-control my-3" type="number" name="price" placeholder="Termék ára" min="0">

			<label>Kategóriák: </label>
			<select class="selectpicker" multiple data-live-search="true" name="categories[]">
			  @foreach ($categories as $category)
			  	<option value="{{$category['id']}}">{{$category['name']}}</option>
			  @endforeach
			</select>

			<p class="m-0 mt-3">Termékfotók: </p>

			<input id="files" type="file" name="image[]" multiple class="form-control my-3" accept="image/*">

			<div id="preview" class="d-flex"></div>

			<label>Termékváltozatok: </label>
			<div id="variants"></div>
			<button id="new-variant" class="form-control btn-secondary btn">Új hozzáadása</button>


			<input type="submit" class="form-control my-3 btn btn-primary">

		</form>

	</div>


	<script type="text/javascript">
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

		let newIndex = 0;

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