@extends('layouts.admin')


@section('content')

<h1>Add Student to Paid Course</h1>

@if($errors->any())
<div>
    <b>{{ $errors->first() }}</b>
</div>
@endif

<form method="POST" action="{{ url('admin/course/add_student') }}">
    {{ csrf_field() }}
    <select name="course_id" class="form-control" required>
        @foreach(App\Category::where('parent_id','!=',null)->get() as $subCat)
        <optgroup label="{{ $subCat->title }}">
            @foreach($subCat->courses as $course)
            <option value="{{ $course->id }}">{{ $course->title }}</option>
            @endforeach
        </optgroup>
        @endforeach
    </select>
    <br />
    <select name="subscription_type" class="form-control">
        <option value="">Select Subscription Type</option>
        <option value="monthly">Monthly</option>
        <option value="quarterly">Quarterly</option>
        <option value="semi_annually">Semi-Annually</option>
        <option value="annually">Annually</option>
    </select>
    <br />
    <select name="students[]" class="form-control" style="height: 400px" multiple>
        <option value="">Select Students</option>
        @foreach(App\User::all() as $user)
        <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
        @endforeach
    </select>
    <br />
    <button type="submit" class="btn btn-primary btn-lg btn-block">Add Students</button>
</form>

@endsection