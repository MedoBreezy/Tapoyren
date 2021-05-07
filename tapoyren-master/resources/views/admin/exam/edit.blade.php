@extends('layouts.admin')


@section('content')
<div id="react-add-exam" data-course-id="{{ $course->id }}" data-exam-id="{{ $exam->id }}" data-token="{{ auth()->user()->api_token }}"></div>

@endsection