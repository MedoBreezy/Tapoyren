@extends('layouts.admin')


@section('content')

<h1>Update Package</h1>

@if($errors->any())
<div>
   <b>{{ $errors->first() }}</b>
</div>
@endif

<form method="POST" action="{{ url('admin/package/'.$package->id.'/update') }}" enctype="multipart/form-data">
   {{ csrf_field() }}
   <input type="text" name="name" class="form-control" placeholder="Package Name" value="{{ $package->name }}"
      required><br />
   <div id="react-package-description" data-value="{{ $package->description }}"></div>
   <br />
   <select name="courses[]" multiple class="form-control" style="height: 400px" required>
      @foreach(App\Category::where('parent_id','!=',null)->get() as $subCat)
      <optgroup label="{{ $subCat->title }}">
         @foreach($subCat->courses->where('status','active') as $course)
         <option value="{{ $course->id }}" {{ in_array($course->id,$courseIds) ? 'selected' : '' }}>{{ $course->title }}
         </option>
         @endforeach
      </optgroup>
      @endforeach
   </select>
   <br />

   <input type="text" name="price_monthly" class="form-control" placeholder="Monthly Price"
      value="{{ $package->price_monthly }}" required><br />
   <input type="text" name="price_quarterly" class="form-control" placeholder="Quarterly Price"
      value="{{ $package->price_quarterly }}" required><br />
   <input type="text" name="price_semiannually" class="form-control" placeholder="Semi-Annually Price"
      value="{{ $package->price_semiannually }}" required><br />
   <input type="text" name="price_annually" class="form-control" placeholder="Annually Price"
      value="{{ $package->price_annually }}" required><br />

   <br />


   @if($package->thumbnail_url!==null)
   <img src="{{ $package->thumbnail_url }}" style="width: 60px; height: 60px; object-fit: contain;" />
   <br />
   @endif
   Thumbnail:
   <input type="file" accept="image/png, image/jpeg, image/jpg" name="thumbnail" class="form-control"
      placeholder="About"><br />

   <button type="submit" class="btn btn-primary btn-lg btn-block">Update Package</button>
</form>

@endsection
