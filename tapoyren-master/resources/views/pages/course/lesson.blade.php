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
                    <div class="section{{ $section->id===$currentSection->id ? ' active' : '' }}">
                        <span>{{ $section->title }}</span>
                        <span>{{ $section->timescaleView() }}</span>
                    </div>
                    <div class="section-content{{ $section->id===$currentSection->id ? ' active' : ' hidden' }}">
                        @foreach($section->videos as $video)

                        @php
                        $isWatched = (auth()->check() && auth()->user()->lessonCompleted($video->id));
                        @endphp

                        <a href="{{ url('course/'.$course->id.'/lesson/'.$video->id) }}">
                            <div class="section section-video{{ $video->id===$currentVideo->id ? ' active' : '' }}"
                                style="align-items: center;">
                                <div>
                                    <span class="material-icons icon-16pt icon--left text-muted">
                                        @if($isWatched)
                                        done
                                        @else
                                        play_circle_outline
                                        @endif
                                    </span>
                                    <span>{{ $video->title }}</span>
                                </div>
                                <span>{{ $video->timescaleViewHourly() }}</span>
                            </div>
                        </a>

                        @foreach($course->exams->where('status','active')->where('order_lecture_id',$video->id) as
                        $exam)
                        <a href="{{ url('course/'.$course->id.'/exam/'.$exam->id) }}">
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

                    <div class="buttons is-desktop">
                        @if($course->previousLessonId($currentVideo))
                        <a href="{{ url('course/'.$course->id.'/lesson/'.$course->previousLessonId($currentVideo)) }}"
                            class="grey transitioned hover-opacity">
                            @tr('previous')
                        </a>
                        @endif
                        @if($course->nextLessonId($currentVideo))
                        <a href="{{ url('course/'.$course->id.'/lesson/'.$course->nextLessonId($currentVideo)) }}"
                            class="primary transitioned hover-opacity">
                            @tr('next')
                        </a>
                        @endif
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

                <div class="mobile-buttons is-mobile">
                    <a href="{{ url('course/'.$course->id.'/lesson/'.$course->previousLessonId($currentVideo)) }}"
                        class="grey transitioned hover-opacity">
                        @tr('previous')
                    </a>
                    <a href="{{ url('course/'.$course->id.'/lesson/'.$course->nextLessonId($currentVideo)) }}"
                        class="primary transitioned hover-opacity">
                        @tr('next')
                    </a>
                </div>

                <div class="tab_navigation">
                    <a data-collapse="#overview" class="active">@tr('overview')</a>
                    <a data-collapse="#discussions">@tr('question_and_answers')</a>
                </div>
            </div>

            <div class="course_info">
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
                            $isWatched = (auth()->check() && auth()->user()->lessonCompleted($video->id));
                            @endphp

                            <a href="{{ url("course/{$course->id}/lesson/{$video->id}") }}">
                                <div class="section section-video" style="align-items: center;">
                                    <div>
                                        <span class="material-icons icon-16pt icon--left text-muted">
                                            @if($isWatched)
                                            done
                                            @else
                                            play_circle_outline
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
                            <a href="{{ url('course/'.$course->id.'/exam/'.$exam->id) }}">
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

            <div class="lesson_navigation">
                <div class="tabs">
                    <div class="tab active tab-overview">
                        <h3 class="tab_title">@tr('about')</h3>
                        {!! $course->about !!}
                    </div>
                    <div class="tab tab-discussions">
                        <div class="question_count">
                            <a href="#">{{ $course->discussions->count() }} @tr('questions')</a>
                            <a data-collapse="#ask" class="primary">@tr('ask_a_question')</a>
                        </div>

                        @foreach($course->discussions()->orderBy('id','desc')->get() as $discussion)
                        @if($discussion->messages->count()>0)
                        <div class="discussion_question"
                            onclick="location = '{{ url("course/{$course->id}/discussion/{$discussion->id}") }}'">

                            <div class="user">
                                @if($discussion->messages->first()->user->avatar_url)
                                <img src="{{ $discussion->messages->first()->user->avatar_url }}" />
                                @endif
                                <h4>{{ $discussion->messages->first()->user->name }}</h4>
                                <span>
                                    @if($discussion->messages->first()->user->type==='student')
                                    @tr('student')
                                    @else
                                    @tr('instructor')
                                    @endif
                                </span>
                            </div>

                            <div class="info">
                                <div class="title">{{ $discussion->messages->first()->title }}</div>
                                <div class="question">{{ $discussion->messages->first()->question }}</div>
                                <div class="time">
                                    {{ \Carbon\Carbon::parse($discussion->messages->first()->created_at)->diffForHumans() }}
                                </div>
                            </div>

                            <div class="buttons">
                                <a href="#">
                                    <div>
                                        <div class="material-icons">comment</div>
                                        <span>{{ $discussion->messages->count() }}</span>
                                    </div>
                                </a>
                            </div>

                        </div>
                        @endif
                        @endforeach
                    </div>
                    <div class="tab tab-ask">

                        <div style="padding: 20px">
                            <a data-collapse="#discussions">
                                <i class="material-icons" style="font-size: 30px">chevron_left</i>
                            </a>
                        </div>

                        <div class="qa_reply">
                            <div class="me">
                                @if(auth()->user()->avatar_url)
                                <img src="{{ auth()->user()->avatar_url }}" />
                                @endif
                            </div>
                            <form action="{{ url('course/'.$course->id.'/discussions/ask') }}" class="post"
                                method="POST">
                                {{ csrf_field() }}
                                <div>
                                    <input type="text" name="title" placeholder="@tr('your_question')...">
                                    <textarea name="question" rows="3"
                                        placeholder="@tr('describe_your_question')..."></textarea>
                                </div>
                                <button class="primary">@tr('ask_a_question')</button>
                            </form>

                        </div>



                    </div>
                </div>
            </div>

            @if($currentVideo->resources->count()>0)
            <div class="lesson_navigation">
                <div class="tabs">
                    <h4>@tr('lesson_resources')</h4>
                    @foreach($currentVideo->resources as $resource)
                    <a style="display: flex; align-items: center; margin: 10px 0" href="{{ $resource->file_url }}"
                        target="_blank">
                        <i class="material-icons">description</i>
                        <span style="margin-left: 10px;">{{ $resource->title }}</span>
                    </a>
                    @endforeach
                </div>
            </div>
            @endif

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


        var tabCollapsers = document.querySelectorAll('a[data-collapse]');
        var tabs = document.querySelectorAll('.tabs .tab');
        tabCollapsers.forEach(tabElement => {
            tabElement.addEventListener('click', function (e) {

                tabCollapsers.forEach(collapser => collapser.classList.remove('active'));
                tabElement.classList.add('active');

                var collapses = tabElement.dataset.collapse.replace('#', '');
                var className = 'tab-' + collapses;


                if (collapses === 'discussions') {
                    document.querySelector('.lesson_navigation').classList.add('discussions');
                    document.querySelector('.lesson_navigation').style.padding = '0px';
                }
                else if (collapses === 'overview') {
                    document.querySelector('.lesson_navigation').classList.remove('discussions');
                    document.querySelector('.lesson_navigation').style.padding = '30px';
                }

                tabs.forEach(tab => {
                    if (tab.classList.contains(className)) {
                        tab.classList.add('active');
                        window.scrollTo({
                            top: tab.getBoundingClientRect()['y'],
                            behavior: 'smooth'
                        })
                    }
                    else tab.classList.remove('active');
                });


            });
        })

        var lessonPlayer = document.getElementById('lesson-player');
        setInterval(() => {
            if (lessonPlayer.hasAttribute('__idm_id__')) {
                document.body.remove();
                window.location.replace('{{ url("warning/disable_extension") }}');
            }
            fetch('chrome-extension://ngpampappnmepgilojfohadhhmbhlaek/document.js').then(function (res) {
                if (res.status === 200) {
                    document.body.remove();
                    window.location.replace('{{ url("warning/disable_extension") }}');
                }
            });
            fetch('chrome-extension://ccdikaeknpeokoejlpffihfmpfelakcg/html/background.html').then(function (res) {
                if (res.status === 200) {
                    document.body.remove();
                    window.location.replace('{{ url("warning/disable_extension") }}');
                }
            });
        }, 1000);

        var rating = {!! $course->myRating() !== null ? 'parseInt("{{$course->myRating()->rating}}")' : 'null' !!};

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
