<div class="card card--elevated card-course overlay js-overlay mdk-reveal js-mdk-reveal " data-partial-height="40" data-toggle="popover" data-trigger="click" style="width: 250px">


    <a href="{{ url('course/'.$package->id.'/preview') }}" class="js-image" data-position="center">
        <img src="{{ $package->thumbnail_url }}" style="width: 250px; height:250px; object-fit: contain;" alt="course">
    </a>

    @if($package->isNew())
    <span class="corner-ribbon corner-ribbon--default-right-top corner-ribbon--shadow bg-accent text-white">@tr('new')</span>
    @endif

    <div class="mdk-reveal__content">
        <div class="card-body">
            <div class="d-flex">
                <div class="flex">
                    <a class="card-title" href="{{ url('package/'.$package->id) }}">{{ $package->name }}</a>
                </div>

            </div>
            <div class="d-flex">
                <small class="text-50">{{ $package->courses->count() }} @tr('course_count')</small>
            </div>
        </div>
    </div>
</div>
<div class="popoverContainer d-none">
    <div class="media">
        <div class="media-left">
            <img src="{{ $package->thumbnail_url }}" width="40" height="40" alt="{{ $package->name }}" class="rounded">
        </div>
        <div class="media-body">
            <div class="card-title mb-0">{{ $package->name }}</div>
            <p class="lh-1 mb-0">
                <span class="text-black-50 small">{{ $package->courses->count() }}</span>
                <span class="text-black-50 small font-weight-bold">@tr('course_count')</span>
            </p>
        </div>
    </div>

    <p class="my-16pt text-black-70">{{ $package->description }}</p>

    <div class="mb-16pt">
        @foreach($package->courses as $packageCourse)
        <div class="d-flex align-items-center">
            <span class="material-icons icon-16pt text-black-50 mr-8pt">fiber_manual_record</span>
            <p class="flex text-black-50 lh-1 mb-0"><small>{{ $packageCourse->course()->title }}</small>
            </p>
        </div>
        @endforeach
    </div>

    <div class="row align-items-center">
        <div class="col text-right">
            <a href="{{ url('package/'.$package->id) }}" class="btn btn-primary">@tr('go_to_package')</a>
        </div>
    </div>

</div>