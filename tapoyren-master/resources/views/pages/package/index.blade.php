@extends('layouts.tapoyren')


@section('content')

@include('pages.parts.header')

<main class="page-wrapper">

   <main class="package">

      <div class="head">
         <div class="package_details">
            <img src="{{ $package->thumbnail_url }}" />
            <h4>{{ $package->name }}</h4>
         </div>
      </div>

      @if($package->notSubscribed())
      <div class="content">
         <a class="primary" href="{{ url('package/'.$package->id.'/subscribe') }}">@tr('enroll')</a>
      </div>
      @endif

   </main>

   <div class="package_about">
      <h3 class="title">@tr('about')</h3>
      {!! $package->description !!}
   </div>

   <div class="package_courses">
      <h3 class="title">@tr('courses_in_package')</h3>

      <div class="courses">
         @foreach($package->courses as $course)
         @php
         $course = $course->course();
         @endphp
         <div class="course" style="cursor: pointer;" onclick="location = '{{ url("course/{$course->id}") }}'">
            <img src="{{ $course->thumbnail_url }}" class="thumbnail" />
            <div class="details">
               <span>{{ $course->title }}</span>
               <a href="{{ url('course/'.$course->id.'/favorite') }}" style="margin-left: auto">
                  <i class="material-icons"
                     style="{{ auth()->check() && auth()->user()->favoritedCourse($course->id) ? 'color: red !important;' :'color: #d8d8d8 !important;' }}">favorite</i>
               </a>
            </div>
         </div>
         @endforeach
      </div>

   </div>

   @include('pages.parts.footer')

</main>


@endsection


@endextends
