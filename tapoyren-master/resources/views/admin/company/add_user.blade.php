@extends('layouts.admin')


@section('content')
@if($errors->any())
<div>
    <b>{{ $errors->first() }}</b>
</div>
@endif

<form method="POST" action="{{ url('admin/company/add_user') }}">
    {{ csrf_field() }}
    <select name="company_id" class="form-control">
        <option value="">Select Company</option>
        @foreach(App\Company::all() as $company)
        <option value="{{ $company->id }}">{{ $company->title }}</option>
        @endforeach
    </select>
    <br/>
    <select name="users[]" class="form-control" multiple>
        <option value="">Select Parent Category</option>
        @foreach(App\User::all() as $user)
        <option value="{{ $user->id }}">{{ $user->name }}</option>
        @endforeach
    </select>
    <br />
    <button type="submit" class="btn btn-primary btn-lg btn-block">Add User</button>
</form>

@endsection