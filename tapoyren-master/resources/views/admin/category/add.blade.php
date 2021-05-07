@extends('layouts.admin')


@section('content')
@if($errors->any())
<div>
   <b>{{ $errors->first() }}</b>
</div>
@endif

<form method="POST" action="{{ url('admin/category/add') }}">
   {{ csrf_field() }}
   @foreach(App\Language::all() as $lang)
   <input type="text" name="title_{{ $lang->slug }}" class="form-control" placeholder="Title ({{ $lang->title }})" required><br />
   @endforeach
   <select name="parent_id" class="form-control">
      <option value="">Select Parent Category</option>
      @foreach(App\Category::where('parent_id',null)->get() as $category)
      <option value="{{ $category->id }}">{{ $category->__('title') }}</option>
      @endforeach
   </select>
   <br />
   <button type="submit" class="btn btn-primary btn-lg btn-block">Add Category</button>
</form>

@endsection