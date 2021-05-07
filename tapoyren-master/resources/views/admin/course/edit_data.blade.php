@extends('layouts.admin')


@section('content')
<div id="react-edit-course-data" data-course-id="{{ $course->id }}" data-token="{{ auth()->user()->api_token }}"></div>

@endsection
