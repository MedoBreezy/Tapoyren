<div class="card course-list-item o-hidden overlay js-overlay" data-trigger="hover">
    <div class="media media-stack-xs align-items-stretch">
        <div class="media-left media__thumbnail mr-0">
            <a href="{{ url('course/'.$course->id) }}" class="js-image" data-position="center">
                <img src="{{ $course->thumbnail_url }}" style="height: 180px;" alt="course">
            </a>

            @if($course->isNew())
            <span class="corner-ribbon corner-ribbon--default-left-top corner-ribbon--shadow bg-accent text-white">NEW</span>
            @endif

        </div>
        <div class="media-body card-body">
            <div class="d-flex">
                <div class="flex">
                    <a class="card-title m-0" href="{{ url('course/'.$course->id) }}">{{ $course->title }}</a>
                    <p class="d-flex flex-wrap lh-1 mb-16pt">
                        <small class="text-50 font-weight-bold mr-8pt">{{ $course->instructor->name }}</small>
                        <small class="text-50">{{ $course->instructor->about}}</small>
                    </p>
                </div>
                <a href="{{ url('course/'.$course->id.'/favorite') }}" class="ml-4pt material-icons text-20 card-course__icon-favorite" style="{{ auth()->check() && auth()->user()->favoritedCourse($course->id) ? 'color: red !important;' :'' }}">favorite</a>


            </div>

            <p class="text-50 course-list-item__excerpt">{{ $course->description}}</p>

            <div class="d-flex align-items-center">
                <div class="flex d-flex lh-1">
                    <small class="text-50 mr-8pt">{{ $course->timescaleView() }}</small>
                    <small class="text-50">{{ $course->videos_count() }} @tr('lessons')</small>
                </div>
                <div class="d-flex align-items-center">
                    <small class="text-50 mr-8pt">{{ $course->rating }}</small>
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

                </div>
            </div>
        </div>

    </div>
</div>