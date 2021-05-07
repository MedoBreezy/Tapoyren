@extends('layouts.admin')


@section('content')
@if($errors->any())
<div>
    <b>{{ $errors->first() }}</b>
</div>
@endif

<form method="POST" action="{{ url('admin/instructor/add') }}">
    {{ csrf_field() }}
    <input type="text" name="name" class="form-control" placeholder="Full Name" required><br />
    <input type="text" name="email" class="form-control" placeholder="Email" required><br />
    <input type="password" name="password" class="form-control" placeholder="Password" required><br />
    <input type="password" name="password_confirmation" class="form-control" placeholder="Password Confirmation" required><br />
    <select name="gender" class="form-control" required>
        <option value="">Select Gender</option>
        <option value="male">Male</option>
        <option value="female">Female</option>
    </select>
    <br />
    <input type="date" name="birthDate" class="form-control" placeholder="Birth Date" required><br />
    <button type="submit" class="btn btn-primary btn-lg btn-block">Add Instructor</button>
</form>

@endsection