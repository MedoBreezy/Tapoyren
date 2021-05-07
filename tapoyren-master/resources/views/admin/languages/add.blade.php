@extends('layouts.admin')


@section('content')
@if($errors->any())
<div>
    <b>{{ $errors->first() }}</b>
</div>
@endif

<form method="POST" action="{{ url('admin/language/add') }}">
    {{ csrf_field() }}
    <input type="text" name="title" class="form-control" placeholder="Title" required><br />
    <input type="text" name="slug" class="form-control" placeholder="Slug (ex. az, en, ru)" required><br />
    <button type="submit" class="btn btn-primary btn-lg btn-block">Add Language</button>
</form>

@endsection