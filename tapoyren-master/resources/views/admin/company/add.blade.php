@extends('layouts.admin')

@section('content')
@if($errors->any())
<div>
    <b>{{ $errors->first() }}</b>
</div>
@endif

<form method="POST" action="{{ url('admin/company/add') }}">
    {{ csrf_field() }}
    <input type="text" name="title" class="form-control" placeholder="Company Name" required><br />
    <select name="owner_id" class="form-control">
        <option value="">Select Owner</option>
        @foreach(App\User::all() as $user)
        <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email}})</option>
        @endforeach
    </select>
    <br />
    <button type="submit" class="btn btn-primary btn-lg btn-block">Add Company</button>
</form>

@endsection
