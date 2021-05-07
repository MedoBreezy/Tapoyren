@extends('layouts.admin')


@section('content')
<div>

   <h1>Lesson: {{ $lesson->title }}</h1>
   <hr />
   <div id="react-lesson-resource-add" data-course-id="{{ $lesson->section->course_id }}"
      data-token="{{ auth()->user()->api_token }}" data-lesson-id="{{ $lesson->id }}"></div>

</div>

@endsection
