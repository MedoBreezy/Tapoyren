@extends('layouts.tapoyren')


@section('content')

@include('pages.parts.header')

<main class="page-wrapper">

    <main class="course_inside">

        <div class="sidebar">
            <div class="course_thumb">
                <img src="{{ $course->thumbnail_url }}" class="course_thumb" />
            </div>
            <div class="course_info">
                <div>
                    <span class="bold">{{ $course->timescaleView(true) }}</span>
                    <span class="gray" style="text-transform: uppercase;">@tr('hours_lowercase')</span>
                </div>
                <div>
                    <span class="bold">{{ $course->difficulty() }}</span>
                    <span class="gray">@tr('difficulty')</span>
                </div>
                <div>
                    <span class="bold">{{ $course->students->count() }}</span>
                    <span class="gray">@tr('students_enrolled')</span>
                </div>
                <div>
                    <span class="bold">{{ $course->language }}</span>
                    <span class="gray">@tr('language')</span>
                </div>
            </div>
            <div class="course_content">
                <h4>@tr('course_content')</h4>

                <div class="contents">
                    @foreach($course->sections as $section)
                    <div class="section">
                        <span>{{ $section->title }}</span>
                        <span>{{ $section->timescaleView() }}</span>
                    </div>
                    <div class="section-content hidden">
                        @foreach($section->videos as $video)

                        @php
                        $lessonLink = '';

                        if(auth()->check() && $course->activeEnrolled(auth()->user())) $lessonLink =
                        url('course/'.$course->id.'/lesson/'.$video->id);
                        elseif($video->preview) $lessonLink = url('course/'.$course->id.'/preview/'.$video->id);
                        elseif(!$video->preview && auth()->check() && !$course->activeEnrolled(auth()->user()))
                        $lessonLink = 'javascript:noAccessInfo();';
                        @endphp
                        <a href="{{ $lessonLink }}">
                            <div class="section section-video" style="align-items: center;">
                                <div>
                                    <span
                                        class="material-icons icon-16pt icon--left {{ $video->preview ? 'text-primary' : 'text-muted' }}">
                                        {{ $video->preview ? 'play_circle_outline' : 'lock' }}
                                    </span>
                                    <span>{{ $video->title }}</span>
                                </div>
                                <span>{{ $video->timescaleViewHourly() }}</span>
                            </div>
                        </a>
                        @endforeach
                    </div>
                    @endforeach
                </div>

            </div>
        </div>

        <div class="course_details">

            <div class="lessonPlayer">
                <iframe id="lesson-player" class="embed-responsive-item"
                    src="https://player.vimeo.com/video/{{ $currentVideo->vimeoVideoId }}?" allowfullscreen></iframe>

                <div class="information">
                    <div class="instructor_avatar">
                        <div class="img_wrapper">
                            <img src="{{ $course->instructor->avatar_url }}" />
                        </div>
                    </div>
                    <div class="instructor_details">
                        <h1>{{ $course->instructor->name }}</h1>
                        <h4>@tr('instructor')</h4>
                    </div>
                    <div class="rate_course is-desktop">
                        <div class="stars">
                            <i class="material-icons">
                                @if($course->myRating())
                                star
                                @else
                                star_border
                                @endif
                            </i>
                            <i class="material-icons">
                                @if($course->myRating() && $course->myRating()->rating>1)
                                star
                                @else
                                star_border
                                @endif
                            </i>
                            <i class="material-icons">
                                @if($course->myRating() && $course->myRating()->rating>2)
                                star
                                @else
                                star_border
                                @endif
                            </i>
                            <i class="material-icons">
                                @if($course->myRating() && $course->myRating()->rating>3)
                                star
                                @else
                                star_border
                                @endif
                            </i>
                            <i class="material-icons">
                                @if($course->myRating() && $course->myRating()->rating>4)
                                star
                                @else
                                star_border
                                @endif
                            </i>
                        </div>
                        <span style="text-transform: uppercase;">@tr('rate_course')</span>
                    </div>

                </div>

                <div class="mobile-rate_course is-mobile">
                    <div class="stars">
                        <div class="stars">
                            <i class="material-icons">
                                @if($course->myRating())
                                star
                                @else
                                star_border
                                @endif
                            </i>
                            <i class="material-icons">
                                @if($course->myRating() && $course->myRating()->rating>1)
                                star
                                @else
                                star_border
                                @endif
                            </i>
                            <i class="material-icons">
                                @if($course->myRating() && $course->myRating()->rating>2)
                                star
                                @else
                                star_border
                                @endif
                            </i>
                            <i class="material-icons">
                                @if($course->myRating() && $course->myRating()->rating>3)
                                star
                                @else
                                star_border
                                @endif
                            </i>
                            <i class="material-icons">
                                @if($course->myRating() && $course->myRating()->rating>4)
                                star
                                @else
                                star_border
                                @endif
                            </i>
                        </div>
                    </div>
                </div>

            </div>


            <div class="lesson_navigation">
                <div class="tabs">
                    <div class="tab active">
                        <h3 class="tab_title">@tr('about')</h3>
                        {!! $course->about !!}
                    </div>
                </div>
            </div>

        </div>

    </main>

    @include('pages.parts.footer')
</main>



@endsection

@push('footer')

<script>
    var previewIframe = document.getElementById('preview_iframe');

    function noAccessInfo() {
        Swal.fire(
            'Dərsliyə keçid etmək üçün kursda qeydiyyatdan keçməlisiniz!',
            '',
            'info'
        );
    }

    $(document).ready(function () {

        var rating = {!! $course->myRating() ? 'parseInt("{{$course->myRating()->rating}}")' : 'null' !!};

    $('.rate_course .stars i').hover(function (e) {
        var hoverIndex = $(this).index();

        $('.rate_course .stars i').each(function (index, el) {
            if (index <= hoverIndex) $(el).text('star');
            else $(el).text('star_border');
        })

    });

    $('.rate_course .stars i').click(function (e) {
        var hoverIndex = $(this).index();

        window.location = '{{ url("course/".$course->id."/rating") }}/' + (hoverIndex + 1);

        $('.rate_course .stars i').each(function (index, el) {
            if (index <= hoverIndex) $(el).text('star');
            else $(el).text('star_border');
        })

    });

    $('.mobile-rate_course .stars i').click(function (e) {
        var hoverIndex = $(this).index();

        window.location = '{{ url("course/".$course->id."/rating") }}/' + (hoverIndex + 1);

    });

    $('.rate_course').mouseleave(function () {
        $('.rate_course .stars i').each(function (index, el) {
            if ($(this).index() >= rating) $(el).text('star_border');
            else $(el).text('star');
        })
    });


    const sections = document.querySelectorAll('.course_content .contents > .section');
    sections.forEach(section => {
        section.addEventListener('click', (e) => {
            sections.forEach(s => s.classList.remove('active'));

            section.classList.add('active');

            section.nextElementSibling.classList.toggle('hidden');
        });

    });
    });
</script>

@endpush
