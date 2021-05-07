@extends('layouts.tapoyren')


@section('content')

<div class="course-previews">
    <iframe id="preview-player" class="embed-responsive-item"
        src="https://player.vimeo.com/video/{{ count($previews)>0 ? $previews[0]->vimeoVideoId : '' }}"
        allowfullscreen></iframe>
    <div class="preview_content">
        @foreach($previews as $preview)
        <div{{$loop->index===0 ? ' class=active' : ''}} data-vvi="{{ $preview->vimeoVideoId }}"
            onclick="previewVideo({{ $preview->vimeoVideoId }})">
            <i class="material-icons text-muted">
                play_circle_outline
            </i>
            {{ $preview->title }}
    </div>
    @endforeach
</div>
</div>

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
                        $lessonLink =
                        'javascript:noAccessInfo();';

                        $isWatched = (auth()->check() && auth()->user()->lessonCompleted($video->id));
                        @endphp
                        <a href="{{ $lessonLink }}">
                            <div class="section section-video" style="align-items: center;">
                                <div>
                                    <span class="material-icons icon-16pt icon--left text-muted">
                                        @if($isWatched)
                                        done
                                        @elseif($video->preview || $lessonLink)
                                        play_circle_outline
                                        @else
                                        lock
                                        @endif
                                    </span>
                                    <span>{{ $video->title }}</span>
                                </div>
                                <span>{{ $video->timescaleViewHourly() }}</span>
                            </div>
                        </a>

                        @foreach($course->exams->where('status','active')->where('order_lecture_id',$video->id) as
                        $exam)
                        <a href="{{ url("course/{$course->id}/exam/{$exam->id}") }}">
                            <div class="section section-video" style="align-items: center;">
                                <div>
                                    <span class="material-icons icon-16pt icon--left text-muted">
                                        @if(auth()->check() && auth()->user()->examCompleted($exam->id))
                                        done
                                        @else
                                        grading
                                        @endif
                                    </span>
                                    <span>{{ $exam->title }}</span>
                                </div>
                            </div>
                        </a>
                        @endforeach

                        @endforeach
                    </div>
                    @endforeach
                </div>

            </div>
        </div>

        <div class="course_details">

            <div class="course_hero">
                <div class="bg">
                    <div class="course_title">{{ $course->title }}</div>
                    <div class="img_wrapper">
                        <img src="{{ $course->instructor->avatar_url }}" />
                    </div>
                </div>
                <div class="hero_content">

                    <div class="rate_course">
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

                    @if(auth()->check())
                    <a class="wishlist" href="{{ url('course/'.$course->id.'/favorite') }}">
                        <i class="material-icons"
                            style="{{ auth()->user()->favoritedCourse($course->id) ? 'color: #E53935;' : 'color: darkgrey;' }}">favorite</i>
                    </a>
                    @endif

                    <div class="instructor">
                        <h1 onclick="location = '{{ url("instructor/{$course->instructor->id}") }}'"
                            style="cursor:pointer;">
                            {{ $course->instructor->name }}</h1>
                        <h3>@tr('instructor')</h3>
                    </div>

                    @if(auth()->check() && $course->activeEnrolled(auth()->user()))
                    <a class="continue_watching transitioned hover-opacity"
                        href="{{ url('course/'.$course->id.'/watch') }}">@tr('continue_watching')</a>
                    @endif

                    @if($course->checkEnrollment())
                    <a class="continue_watching transitioned hover-opacity"
                        href="{{ url('course/'.$course->id.'/enroll') }}">@tr('enroll')</a>
                    @endif

                    @if(count($previews)>0)
                    <a class="watch_preview transitioned hover-opacity" href="#"
                        onclick="showPreviews({{ count($previews)>0 ? $previews[0]->vimeoVideoId : '' }})">@tr('watch_preview')</a>
                    @endif

                    <div class="mobile-only">
                        @if(auth()->check())
                        <a class="wishlist" href="{{ url('course/'.$course->id.'/favorite') }}">
                            <i class="material-icons"
                                style="{{ auth()->user()->favoritedCourse($course->id) ? 'color: #E53935;' : 'color: darkgrey;' }}">favorite</i>
                        </a>
                        @endif
                        @if(auth()->check() && $course->activeEnrolled(auth()->user()))
                        <a class="continue_watching transitioned hover-opacity"
                            href="{{ url('course/'.$course->id.'/watch') }}">@tr('continue_watching')</a>
                        @endif
                        @if($course->checkEnrollment())
                        <a class="continue_watching transitioned hover-opacity"
                            href="{{ url('course/'.$course->id.'/enroll') }}">@tr('enroll')</a>
                        @endif
                        @if(count($previews)>0)
                        <a class="watch_preview transitioned hover-opacity" href="#"
                            onclick="showPreviews({{ count($previews)>0 ? $previews[0]->vimeoVideoId : '' }})">@tr('watch_preview')</a>
                        @endif
                    </div>


                </div>
            </div>

            <div class="course_info">
                <div class="course_about">
                    <h3>@tr('about_course')</h3>
                    {!! $course->about !!}
                </div>
                <div class="course_learnlist">
                    <h3>@tr('what_youll_learn')</h3>
                    <ul class="list-unstyled">
                        @foreach($course->whatYoulearnList as $list)
                        <li class="d-flex align-items-center">
                            <span class="material-icons text-50 mr-8pt">check</span>
                            <span class="text-70">{{ $list->title }}</span>
                        </li>
                        @endforeach
                    </ul>
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
                            $lessonLink
                            = 'javascript:noAccessInfo();';

                            $isWatched = (auth()->check() && auth()->user()->lessonCompleted($video->id));
                            @endphp
                            <a href="{{ $lessonLink }}">
                                <div class="section section-video" style="align-items: center;">
                                    <div>
                                        <span class="material-icons icon-16pt icon--left text-muted">
                                            @if($isWatched)
                                            done
                                            @elseif($video->preview || $lessonLink)
                                            play_circle_outline
                                            @else
                                            lock
                                            @endif
                                        </span>
                                        <span>{{ $video->title }}</span>
                                    </div>
                                    <span>{{ $video->timescaleViewHourly() }}</span>
                                </div>
                            </a>
                            @endforeach
                            @foreach($course->exams->where('status','active')->where('order_lecture_id',$video->id) as
                            $exam)
                            <a href="{{ url("course/{$course->id}/exam/{$exam->id}") }}">
                                <div class="section section-video" style="align-items: center;">
                                    <div>
                                        <span class="material-icons icon-16pt icon--left text-muted">
                                            @if(auth()->check() && auth()->user()->examCompleted($exam->id))
                                            done
                                            @else
                                            grading
                                            @endif
                                        </span>
                                        <span>{{ $exam->title }}</span>
                                    </div>
                                </div>
                            </a>
                            @endforeach
                        </div>
                        @endforeach
                    </div>

                </div>
            </div>

            @if($course->instructor->courses()->where('status','active')->where('id','!=',$course->id)->count()>4)
            <div class="other_courses">
                <h3>@tr('more_from_the_author')</h3>

                <div class="courses">
                    @foreach($course->instructor->courses()->orderBy('id','desc')->where('status','active')->where('id','!=',$course->id)->take(5)->get()
                    as $otherCourse)
                    <div class="course">
                        <div class="thumbnail">
                            <img src="{{ $otherCourse->thumbnail_url }}" class="course_thumb" />
                            <div class="instructor">
                                <div class="instructor_image">
                                    <img src="{{ $otherCourse->instructor->avatar_url }}" />
                                </div>
                                <a class="btn" href="{{ url('course/'.$otherCourse->id) }}">@tr('go_to_course')</a>
                            </div>
                        </div>
                        <div class="content">
                            {{ $otherCourse->title }}
                        </div>
                    </div>
                    @endforeach
                </div>

            </div>
            @endif

            <div class="course_feedbacks">

                <div class="feedbacks">
                    <h3>@tr('student_feedback')</h3>

                    <div class="center">
                        <h1>{{ $course->rating }}</h1>
                        <div class="stars">
                            <div class="stars">
                                <i class="material-icons">star</i>
                                <i class="material-icons">
                                    @if($course->rating>1)
                                    star
                                    @else
                                    star_border
                                    @endif
                                </i>
                                <i class="material-icons">
                                    @if($course->rating>2)
                                    star
                                    @else
                                    star_border
                                    @endif
                                </i>
                                <i class="material-icons">
                                    @if($course->rating>3)
                                    star
                                    @else
                                    star_border
                                    @endif
                                </i>
                                <i class="material-icons">
                                    @if($course->rating>4)
                                    star
                                    @else
                                    star_border
                                    @endif
                                </i>
                            </div>
                        </div>
                        <h4>{{ $course->ratings->count() }} @tr('ratings')</h4>
                    </div>

                    <div class="all_ratings">
                        <div class="ratings">
                            <div class="rating">
                                <div class="rating_full" style="width: {{ $course->ratingPercent(5) }}%"></div>
                            </div>
                            <div class="stars">
                                <i class="material-icons">star</i>
                                <i class="material-icons">star</i>
                                <i class="material-icons">star</i>
                                <i class="material-icons">star</i>
                                <i class="material-icons">star</i>
                            </div>
                            <span>{{ $course->ratingPercent(5) }}%</span>
                        </div>
                        <div class="ratings">
                            <div class="rating">
                                <div class="rating_full" style="width: {{ $course->ratingPercent(4) }}%"></div>
                            </div>
                            <div class="stars">
                                <i class="material-icons">star</i>
                                <i class="material-icons">star</i>
                                <i class="material-icons">star</i>
                                <i class="material-icons">star</i>
                                <i class="material-icons">star_border</i>
                            </div>
                            <span>{{ $course->ratingPercent(4) }}%</span>
                        </div>
                        <div class="ratings">
                            <div class="rating">
                                <div class="rating_full" style="width: {{ $course->ratingPercent(3) }}%"></div>
                            </div>
                            <div class="stars">
                                <i class="material-icons">star</i>
                                <i class="material-icons">star</i>
                                <i class="material-icons">star</i>
                                <i class="material-icons">star_border</i>
                                <i class="material-icons">star_border</i>
                            </div>
                            <span>{{ $course->ratingPercent(3) }}%</span>
                        </div>
                        <div class="ratings">
                            <div class="rating">
                                <div class="rating_full" style="width: {{ $course->ratingPercent(2) }}%"></div>
                            </div>
                            <div class="stars">
                                <i class="material-icons">star</i>
                                <i class="material-icons">star</i>
                                <i class="material-icons">star_border</i>
                                <i class="material-icons">star_border</i>
                                <i class="material-icons">star_border</i>
                            </div>
                            <span>{{ $course->ratingPercent(2) }}%</span>
                        </div>
                        <div class="ratings">
                            <div class="rating">
                                <div class="rating_full" style="width: {{ $course->ratingPercent(1) }}%"></div>
                            </div>
                            <div class="stars">
                                <i class="material-icons">star</i>
                                <i class="material-icons">star_border</i>
                                <i class="material-icons">star_border</i>
                                <i class="material-icons">star_border</i>
                                <i class="material-icons">star_border</i>
                            </div>
                            <span>{{ $course->ratingPercent(1) }}%</span>
                        </div>
                    </div>

                </div>
                <div class="interested_courses">
                    <h3>@tr('top_interested_courses')</h3>

                    <div class="courses">

                        @foreach(App\Course::where('status','active')->orderBy('view_count','desc')->take(4)->get() as
                        $interestedCourse)
                        <a href="{{ url('course/'.$interestedCourse->id) }}">
                            <div class="course">
                                <img src="{{ $interestedCourse->thumbnail_url }}" style="border-radius: 50%;" />
                                <div class="course_title">
                                    <h3>{{ $interestedCourse->title }}</h3>
                                    <h5>{{ $interestedCourse->description }}</h5>
                                </div>
                            </div>
                        </a>
                        @endforeach

                    </div>

                    <a class="see_all" href="{{ url('browse') }}">@tr('see_all_courses')</a>

                </div>

            </div>

        </div>

    </main>


    @include('pages.parts.footer')
</main>

@endsection

@push('footer')

<script>
    var preview_modal = document.querySelector('.course-previews');
    var previewIframe = document.getElementById('preview-player');
    var preview_videos = preview_modal.querySelectorAll('.preview_content > div');

    function noAccessInfo() {
        Swal.fire(
            'Dərsliyə keçid etmək üçün kursda qeydiyyatdan keçməlisiniz!',
            '',
            'info'
        );
    }

    function previewVideo(vvi) {
        previewIframe.src = 'https://player.vimeo.com/video/' + vvi + '?';

        preview_videos.forEach(p => {
            p.classList.remove('active');
            if (p.dataset.vvi == vvi) p.classList.add('active');
        });


    }

    function showPreviews(vvi) {
        document.body.classList.add('modal-active');
        preview_modal.classList.add('active');
        previewIframe.src = 'https://player.vimeo.com/video/' + vvi + '?';
    }

    document.querySelector('.modal-backdrop').addEventListener('click', () => {
        document.body.classList.remove('modal-active');
        preview_modal.classList.remove('active');
        previewIframe.src = 'https://player.vimeo.com/video/{{ count($previews)>0 ? $previews[0]->vimeoVideoId : '' }}';

        preview_videos.forEach(p => {
            p.classList.remove('active');
        });

        preview_videos[0].classList.add('active');

    });

    $(document).ready(function () {

        var rating = {!! $course->myRating() != null ? 'parseInt("{{$course->myRating()->rating}}")' : 'null' !!};

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
