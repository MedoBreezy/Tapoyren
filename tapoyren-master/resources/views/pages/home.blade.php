@extends('layouts.tapoyren')


@section('content')

@include('pages.parts.header')

<main class="page-wrapper">

   <div class="hero">

      @if(auth()->check() && !auth()->user()->verified() && auth()->user()->canResendEmailVerification())
      <div class="email-verification">
         <h2>
            @tr('resend_email_details') <a href="{{ url('email/verification/resend') }}">@tr('resend_email')</a>
         </h2>
      </div>
      @endif

      <div class="particles"></div>

      <div class="content">
         <h1 class="text-primary text-bold">@tr('hero_title')</h1>
         <!-- <h2 class="text-bold">DEVELOPMENT</h2> -->
         <p>@tr('hero_description')</p>
      </div>

      <img src="{{ asset('public/design/assets/hero-splash.png') }}" class="splash" />

   </div>

   <div class="features">

      <div class="feature">
         <h3>@tr('feature_first_title')</h3>
         <p>@tr('feature_first_description')</p>
      </div>
      <div class="feature">
         <h3>@tr('feature_second_title')</h3>
         <p>@tr('feature_second_description')</p>
      </div>
      <div class="feature">
         <h3>@tr('feature_third_title')</h3>
         <p>@tr('feature_third_description')</p>
      </div>

   </div>

   <div class="top_courses">

      <h2>@tr('top_courses')</h2>

      <div class="popular_categories">
         @foreach(App\Category::where('parent_id','!=',null)->inRandomOrder()->take(4)->get() as $cat)
         <a href="{{ url('category/'.$cat->id) }}"
            class="category transitioned hover-opacity">{{ $cat->__('title') }}</a>
         @endforeach
      </div>

      <div class="courses hidden-below">

         @foreach($home_courses as $course)
         <div class="course" onclick="location = '{{ url('course/'.$course->id) }}'" style="cursor: pointer;">
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

         <div class="courses-load-more hidden">
            <h2 onclick="loadMoreCoursesMobile()" class="see_all_courses" style="text-transform: uppercase;">
               @tr('load_more')
            </h2>
         </div>

      </div>

      <div class="mt-40">
         <a href="{{ url('browse') }}">
            <h2 class="see_all_courses" style="text-transform: uppercase;">@tr('see_all_courses')</h2>
         </a>
      </div>

   </div>

   <div class="top_courses --packages">

      <h2>@tr('packages')</h2>

      <div class="courses">

         @foreach(App\Package::where('status','active')->inRandomOrder()->take(4)->get() as $package)
         <div class="course" onclick="location = '{{ url('package/'.$package->id) }}'" style="cursor: pointer;">
            <img src="{{ $package->thumbnail_url }}" class="thumbnail" />
            <div class="details">
               <span>{{ $package->name }}</span>
            </div>
         </div>
         @endforeach

      </div>

      <!-- <div class="mt-40">
         <a href="{{ url('browse') }}">
            <h2 class="see_all_courses" style="text-transform: uppercase;">@tr('see_all_courses')</h2>
         </a>
      </div> -->

   </div>

   @include('pages.parts.featured_categories')

   @include('pages.parts.footer')
</main>



@endsection

@push('footer')

<script>
   function loadMoreCoursesMobile() {
      document.querySelector('.top_courses .courses').classList.remove('hidden-below');
      document.querySelector('.courses-load-more').remove();
   }
</script>

@endpush
