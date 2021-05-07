@extends('layouts.admin')

@section('content')
@if($errors->any())
<div>
    <b>{{ $errors->first() }}</b>
</div>
@endif

<form method="POST" action="{{ url('admin/coupon/add') }}">
    {{ csrf_field() }}
    <input type="text" name="start" class="form-control" placeholder="Başlıq" required><br />
    <input type="number" name="count" class="form-control" placeholder="Say" required><br />
    <input type="number" name="discount" class="form-control" placeholder="Endirim faizi %" required><br />

    <br />
    <button type="submit" class="btn btn-primary btn-lg btn-block">Add</button>
</form>

@endsection
