@extends('layouts.admin')


@section('content')

<h1>Add Package</h1>

@if($errors->any())
<div>
   <b>{{ $errors->first() }}</b>
</div>
@endif

<form method="POST" action="{{ url('admin/package/add') }}" enctype="multipart/form-data">
   {{ csrf_field() }}
   <input type="text" name="name" class="form-control" placeholder="Package Name" required><br />

   <div id="react-package-description"></div>

   <br />
   <select name="courses[]" multiple class="form-control" style="height: 400px" required>
      @foreach(App\Category::where('parent_id','!=',null)->get() as $subCat)
      <optgroup label="{{ $subCat->title }}">
         @foreach($subCat->courses->where('status','active') as $course)
         <option value="{{ $course->id }}">{{ $course->title }}</option>
         @endforeach
      </optgroup>
      @endforeach
   </select>
   <br />

   <input type="text" name="price_monthly" class="form-control" placeholder="Monthly Price" required><br />
   <input type="text" name="price_quarterly" class="form-control" placeholder="Quarterly Price" required><br />
   <input type="text" name="price_semiannually" class="form-control" placeholder="Semi-Annually Price" required><br />
   <input type="text" name="price_annually" class="form-control" placeholder="Annually Price" required><br />

   <br />


   Thumbnail:
   <input type="file" accept="image/png, image/jpeg, image/jpg" name="thumbnail" class="form-control"
      placeholder="About"><br />

   <button type="submit" class="btn btn-primary btn-lg btn-block">Add Package</button>
</form>

@endsection
