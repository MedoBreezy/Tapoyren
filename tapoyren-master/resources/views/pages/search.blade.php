@extends('layouts.tapoyren')


@section('content')

@include('pages.parts.header')

<main class="page-wrapper">

   <div class="page-title">
      <h1>@tr('courses')</h1>
   </div>

   <div class="filters_wrapper">

      <div class="filters">

         <form class="filter filter_search" method="GET" action="{{ request()->getRequestUri() }}">
            <input type="text" name="query" placeholder="@tr('search_description')"
               value="{{ request()->input('query') }}" />
            <button>
               <i class="material-icons">search</i>
            </button>
         </form>

         <div class="filter has-dropdown">
            <div class="name">
               <span data-toggle="categories">@tr('categories')</span>
               <i class="material-icons" data-toggle="categories">expand_more</i>
            </div>
            <div class="dropdown-content dropdown-scroll no-padding no-top" id="categories">
               @foreach(App\Category::where('parent_id',null)->get() as $cat)
               <div class="filter_categories">
                  <div class="category_title transitioned hover-bg">{{ $cat->__('title') }}</div>
                  @foreach($cat->sub_categories as $subCat)
                  <a href="?query={{ request()->input('query') }}&category={{ $subCat->id }}&difficulty={{ request()->input('difficulty') }}&language={{ request()->input('language') }}"
                     class="filter_subcategories transitioned hover-bg">{{ $subCat->__('title') }}</a>
                  @endforeach
               </div>
               @endforeach
            </div>
         </div>

         <div class="filter has-dropdown">
            <div class="name">
               <span data-toggle="languages">@tr('languages')</span>
               <i class="material-icons" data-toggle="languages">expand_more</i>
            </div>
            <div class="dropdown-content dropdown-scroll no-padding no-top" id="languages">
               @foreach($courseLanguages as $lang)
               <div class="filter_categories">
                  <a href="?query={{ request()->input('query') }}&category={{ request()->input('category') }}&difficulty={{ request()->input('difficulty') }}&language={{ $lang }}"
                     class="filter_subcategories no-indent transitioned hover-bg">{{ $lang }}</a>
               </div>
               @endforeach
            </div>
         </div>

         <div class="filter has-dropdown">
            <div class="name">
               <span data-toggle="difficulty">@tr('difficulty')</span>
               <i class="material-icons" data-toggle="difficulty">expand_more</i>
            </div>
            <div class="dropdown-content dropdown-scroll no-padding no-top" id="difficulty">
               <div class="filter_categories">
                  <a href="?query={{ request()->input('query') }}&category={{ request()->input('category') }}&difficulty=0&language={{ request()->input('language') }}"
                     class="filter_subcategories no-indent transitioned hover-bg">@tr('difficulty_beginner')</a>
               </div>
               <div class="filter_categories">
                  <a href="?query={{ request()->input('query') }}&category={{ request()->input('category') }}&difficulty=1&language={{ request()->input('language') }}"
                     class="filter_subcategories no-indent transitioned hover-bg">@tr('difficulty_intermediate')</a>
               </div>
               <div class="filter_categories">
                  <a href="?query={{ request()->input('query') }}&category={{ request()->input('category') }}&difficulty=2&language={{ request()->input('language') }}"
                     class="filter_subcategories no-indent transitioned hover-bg">@tr('difficulty_advanced')</a>
               </div>
            </div>
         </div>

         <div class="buttons">
            <a href="#" class="bold">@tr('reset')</a>
         </div>



      </div>

   </div>

   <div class="search_courses">

      @foreach($courses as $course)
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

   @if($packages->count()>0)
   <h2 style="text-align: center; margin-top: 40px; margin-bottom: 20px;">@tr('packages')</h2>

   <div class="search_courses">

      @foreach($packages as $package)
      <div class="course" style="cursor: pointer;" onclick="location = '{{ url("package/{$package->id}") }}'">
         <img src="{{ $package->thumbnail_url }}" class="thumbnail" />
         <div class="details">
            <span>{{ $package->name }}</span>
         </div>
      </div>
      @endforeach

   </div>
   @endif

    @if($instructors->count()>0)
    <h2 style="text-align: center; margin-top: 40px; margin-bottom: 20px;">@tr('instructors')</h2>
    <div class="search_courses">

        @foreach($instructors as $instructor)
        <div class="course" style="cursor: pointer;" onclick="location = '{{ url("instructor/{$instructor->id}") }}'">
            <img src="{{ $instructor->avatar_url }}" style="display: block; border-radius: 50%; width:150px; height:150px; object-fit: contain; margin: 10px auto;" />
            <div class="details">
                <span style="width: 100%; text-align: center; margin: 10px 0;">{{ $instructor->name }}</span>
            </div>
        </div>
        @endforeach

    </div>
    @endif

   @include('pages.parts.footer')
</main>



@endsection
