@extends('layouts.tapoyren')


@section('content')

@include('pages.parts.header')

<main class="page-wrapper">

   <div class="instructor_about">
      <div class="details">
         <div class="instructor_avatar">
            <div class="img_wrapper">
               <img src="{{ $instructor->avatar_url }}" />
            </div>
         </div>
         <div class="instructor_details">
            <h2>{{ $instructor->name }}</h2>
            <h4>@tr('instructor')</h4>
         </div>
         <!-- <div class="social_links">
            <a href="#"><i class="material-icons">public</i></a>
            <a href="#"><i class="material-icons">public</i></a>
            <a href="#"><i class="material-icons">public</i></a>
            <a href="#"><i class="material-icons">public</i></a>
            <a href="#"><i class="material-icons">public</i></a>
         </div>
         <div class="buttons">
            <button class="primary">
               FOLLOW
            </button>
            <button class="grey">
               SEND MESSAGE
            </button>
         </div> -->
      </div>

      <div class="navigation">
         <a href="#" data-name="about" class="active" onclick="showTab('about')">@tr('about')</a>
         <a href="#" data-name="courses" onclick="showTab('courses')">@tr('courses')</a>
      </div>
   </div>

   <div class="instructor_tabs tabs">
      <div class="tab tab-about active">
         <h3 class="tab_title">@tr('about_instructor')</h3>
         @if($instructor->bio!=null)
         {!! $instructor->bio !!}
         @endif
      </div>
      <div class="tab tab-courses">
         <h3 class="tab_title">@tr('courses')</h3>
         <div class="search_courses">
            @foreach($instructor->courses as $course)
            <div class="course" style="cursor: pointer;"
               onclick="location = '{{ url("course/{$course->id}") }}'">
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
   </div>

   @include('pages.parts.footer')
</main>



@endsection

@push('footer')

<script>
   function showTab(tabName) {
      var navigation = document.querySelectorAll('.instructor_about .navigation a');
      var tabs = document.querySelectorAll('.instructor_tabs .tab');

      navigation.forEach(nav => {
         if (nav.dataset.name !== tabName) nav.classList.remove('active');
         else nav.classList.add('active');
      });

      tabs.forEach(tab => {
         if (!tab.classList.contains('tab-' + tabName)) tab.classList.remove('active');
         else tab.classList.add('active');
      });

   }
</script>

@endpush
