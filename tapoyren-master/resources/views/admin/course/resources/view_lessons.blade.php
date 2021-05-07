@extends('layouts.admin')


@section('content')
<div>

   <h1>Course: {{ $course->title }}</h1>
   <hr />

   @foreach($course->sections as $section)

   <div>
      <h4>{{ $section->title }}</h4>
      <div style="padding-left:40px; opacity: 0.7">
         @foreach($section->videos as $video)
         <a style="display: block;" href="{{ url("admin/course/{$course->id}/lesson/{$video->id}/resource/add") }}">
            {{ $video->title }}
         </a>
         @endforeach
      </div>
   </div>

   @endforeach

</div>

@endsection
