@extends('layouts.tapoyren')


@section('content')

@include('pages.parts.header')

<main class="page-wrapper">

    <div class="page-title">
        <h1>@tr('my_courses')</h1>
    </div>

    <!-- <div class="filters_wrapper">

        <div class="filters">

            <div class="filter filter_search">
                <input type="text" placeholder="@tr('search_description')" />
                <button>
                    <i class="material-icons">search</i>
                </button>
            </div>

            <div class="filter">
                <div class="name">
                    <span>@tr('categories')</span>
                    <i class="material-icons">expand_more</i>
                </div>
            </div>

            <div class="filter">
                <div class="name">
                    <span>@tr('order')</span>
                    <i class="material-icons">expand_more</i>
                </div>
            </div>

            <div class="buttons">
                <a href="#" class="bold">@tr('reset')</a>
            </div>



        </div>

    </div> -->


    <h2 style="text-align: center;">@tr('active_courses')</h2>
    <div class="search_courses">
        @php
        $studentCourse = App\CourseStudent::where('student_id',auth()->user()->id)->get();
        $activeCourses = [];
        $activeCourseIds = [];
        $deactiveCourses = [];

        foreach($studentCourse as $sc) {
            $course = App\Course::find($sc->course_id);

            $active = $sc->status==='active';

            if($active) {
                array_push($activeCourses,$course);
                array_push($activeCourseIds,$course->id);
            }
        }

        foreach($studentCourse as $sc) {
            $course = App\Course::find($sc->course_id);

            $deactive = $sc->status==='deactive';

            if($deactive) {
                if(!in_array($course->id,$activeCourseIds) && !in_array($course,$deactiveCourses)) array_push($deactiveCourses,$course);
            }
        }

        @endphp
        @foreach($activeCourses as $course)
            <div class="course" onclick="location = '{{ url('course/'.$course->id) }}'" style="cursor: pointer;">
                <img src="{{ $course->thumbnail_url }}" class="thumbnail" />
                <div class="details">
                    <span>{{ $course->title }}</span>
                    <a href="{{ url('course/'.$course->id.'/favorite') }}" style="margin-left: auto">
                        <i class="material-icons" style="{{ auth()->check() && auth()->user()->favoritedCourse($course->id) ? 'color: red !important;' :'color: #d8d8d8 !important;' }}">favorite</i>
                    </a>
                </div>
            </div>
        @endforeach
    </div>

    <h2 style="text-align: center;">@tr('deactive_courses')</h2>
    <div class="search_courses">
        @foreach($deactiveCourses as $course)
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
    </div>

    @include('pages.parts.footer')
</main>



@endsection
