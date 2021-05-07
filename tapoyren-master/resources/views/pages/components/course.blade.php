<div class="card card--elevated card-course overlay js-overlay mdk-reveal js-mdk-reveal " data-partial-height="40" data-toggle="popover" data-trigger="click" style="width: 250px">


    <a href="{{ url('course/'.$course->id.'/preview') }}" class="js-image" data-position="center">
        <img src="{{ $course->thumbnail_url }}" style="width: 250px; height:250px; object-fit: contain;" alt="course">
    </a>

    @if($course->isNew())
    <span class="corner-ribbon corner-ribbon--default-right-top corner-ribbon--shadow bg-accent text-white">NEW</span>
    @endif

    <div class="mdk-reveal__content">
        <div class="card-body">
            <div class="d-flex">
                <div class="flex">
                    <a class="card-title" href="{{ url('course') }}">{{ $course->title }}</a>
                    <small class="text-50 font-weight-bold mb-4pt">{{ $course->instructor->name}}</small>
                </div>
                <a href="{{ url('course/'.$course->id.'/favorite') }}" class="ml-4pt material-icons text-20 card-course__icon-favorite" style="{{ auth()->check() && auth()->user()->favoritedCourse($course->id) ? 'color: red !important;' :'' }}">favorite</a>

            </div>
            <div class="d-flex">
                <div class="rating flex">
                    <span class="rating__item"><span class="material-icons">star</span></span>
                    <span class="rating__item"><span class="material-icons">
                            @if($course->rating>1)
                            star
                            @else
                            star_border
                            @endif
                        </span></span>
                    <span class="rating__item"><span class="material-icons">
                            @if($course->rating>2)
                            star
                            @else
                            star_border
                            @endif
                        </span></span>
                    <span class="rating__item"><span class="material-icons">
                            @if($course->rating>3)
                            star
                            @else
                            star_border
                            @endif
                        </span></span>
                    <span class="rating__item"><span class="material-icons">
                            @if($course->rating>4)
                            star
                            @else
                            star_border
                            @endif
                        </span></span>
                </div>
                <small class="text-50">{{ $course->timescaleView() }}</small>
            </div>
        </div>
    </div>
</div>
<div class="popoverContainer d-none">
    <div class="media">
        <div class="media-left">
            <img src="{{ $course->thumbnail_url }}" width="40" height="40" alt="{{ $course->title }}" class="rounded">
        </div>
        <div class="media-body">
            <div class="card-title mb-0">{{ $course->title }}</div>
            <p class="lh-1 mb-0">
                <span class="text-black-50 small">with</span>
                <span class="text-black-50 small font-weight-bold">{{ $course->instructor->name }}</span>
            </p>
        </div>
    </div>

    <p class="my-16pt text-black-70">{{ $course->description }}</p>

    <div class="mb-16pt">
        @foreach($course->whatYouLearnList as $list)
        <div class="d-flex align-items-center">
            <span class="material-icons icon-16pt text-black-50 mr-8pt">check</span>
            <p class="flex text-black-50 lh-1 mb-0"><small>{{ $list->title }}</small>
            </p>
        </div>
        @endforeach
    </div>

    <div class="row align-items-center">
        <div class="col-auto">
            <div class="d-flex align-items-center mb-4pt">
                <span class="material-icons icon-16pt text-black-50 mr-4pt">access_time</span>
                <p class="flex text-black-50 lh-1 mb-0"><small>{{ $course->timescaleView() }}</small></p>
            </div>
            <div class="d-flex align-items-center mb-4pt">
                <span class="material-icons icon-16pt text-black-50 mr-4pt">play_circle_outline</span>
                <p class="flex text-black-50 lh-1 mb-0"><small>{{ $course->videos_count() }} lessons</small></p>
            </div>
            <div class="d-flex align-items-center">
                <span class="material-icons icon-16pt text-black-50 mr-4pt">assessment</span>
                <p class="flex text-black-50 lh-1 mb-0"><small>{{ $course->difficulty() }}</small></p>
            </div>
        </div>
        <div class="col text-right">
            <a href="{{ url('course/'.$course->id) }}" class="btn btn-primary">Go to Course</a>
        </div>
    </div>

</div>
