@extends('layouts.dashboard.layout')

@section('content')

<div class="row mt-5">
    <div class="col-6">

        <div class="form-group row mb-0">
            <div class="col-md-10">
                <select class="custom-select" onchange="courseChanged(this)">
                    <option value="">Select Course</option>
                    @foreach(auth()->user()->enrolledCourses() as $enrolledCourse)
                    <option value="{{ $enrolledCourse->id }}"
                        {{ request()->input('course_id')==$enrolledCourse->id ? 'selected' : '' }}>
                        {{ $enrolledCourse->title }}
                    </option>
                    @endforeach
                </select>
            </div>
        </div>

    </div>
</div>

@if(isset($hasData))
<div class="row mt-4">

    <div class="col-lg-12">
        <div class="card">
            <div class="card-body">

                <h4 class="card-title">Completed Lessons</h4><br />

                <div id="courseCompletion" style="width: 100%; height: 350px;"></div>

                <div id="accordion" style="max-height: 45vh; overflow-y:scroll; padding-bottom: 60px;">
                    @foreach($course->sections as $courseSection)
                    <div class="card mb-1 shadow-none">
                        <div class="card-header" id="course_section_{{ $courseSection->id }}">
                            <h6 class="m-0 d-flex justify-content-between">
                                <a href="#section_videos_{{ $courseSection->id }}" class="text-dark collapsed"
                                    data-toggle="collapse" aria-expanded="false"
                                    aria-controls="section_videos_{{ $courseSection->id }}">
                                    {{ $courseSection->title }}
                                </a>
                            </h6>
                        </div>

                        <div id="section_videos_{{ $courseSection->id }}" class="collapse"
                            aria-labelledby="course_section_{{ $courseSection->id }}" data-parent="#accordion">
                            <div class="card-body">

                                @foreach($courseSection->videos as $sectionVideo)
                                <div class="d-flex flex-wrap align-items-center my-1 p-2">
                                    <b>{{ $sectionVideo->title }}</b>

                                    @if(in_array($sectionVideo->id,$watchedLessons))
                                    <h5 class="m-0 ml-4"><span class="badge badge-lg badge-success">COMPLETED</span>
                                    </h5>
                                    @endif

                                </div>
                                @endforeach

                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>

            </div>
        </div>
    </div>
    @if($exams->count()>0)
    <div class="col-lg-12">
        <div class="card">
            <div class="card-body" style="background: #F5FAFA;">

                <h4 class="card-title">Topic based exam results</h4><br />

                <div class="d-flex flex-wrap align-items-center center" style="width: 100%;">
                    <div id="stackedAnalytics" style="width: 100%; height: 500px;"></div>
                </div>
            </div>

        </div>
    </div>
    <div class="col-lg-6">
        <div class="card">
            <div class="card-body">

                <div class="d-flex flex-wrap align-items-center center" style="width: 100%;">
                    <div id="spiderAnalytics" style="width: 100%;"></div>
                </div>

                <table class="table table-dark">
                    <thead>
                        <tr>
                            <th>Topic</th>
                            <th>Percentage</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($topics as $topic)
                        <tr>
                            <td>{{ $topic->name }}</td>
                            <td>{{ $topic->percentage }}%</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>

            </div>

        </div>
    </div>
    <div class="col-lg-6">
        <div class="card">
            <div class="card-body">

                <h4 class="card-title">Exam Results</h4><br />

                <div id="accordion" style="max-height: 45vh; overflow-y:scroll; padding-bottom: 60px;">
                    @foreach($exams as $examData)
                    <div class="card mb-1 shadow-none">
                        <div class="card-header" id="exam_name_{{ $loop->index }}">
                            <h6 class="m-0 d-flex justify-content-between">
                                <a href="#exam_results_{{ $loop->index }}" class="text-dark collapsed"
                                    data-toggle="collapse" aria-expanded="false"
                                    aria-controls="exam_results_{{ $loop->index }}">
                                    {{ $examData['exam']->title }}
                                </a>
                                @if($examData['status']==='failed')
                                <span class="text-danger">FAILED</span>
                                @elseif($examData['status']==='passed')
                                <span class="text-success">PASSED</span>
                                @endif
                            </h6>
                        </div>

                        <div id="exam_results_{{ $loop->index }}" class="collapse"
                            aria-labelledby="exam_name_{{ $loop->index }}" data-parent="#accordion">
                            <div class="card-body" style="line-height: 30px;">
                                <span>Correct: {{ $examData['points'] }}</span><br />
                                <span>Wrong: {{ $examData['wrongAnswers'] }}</span><br />
                                Minimum Score: {{ $examData['exam']->minimum_point }}<br />
                                Your Score: {{ $examData['points'] }}<br />

                                <br />
                                <b>Date Entered</b>: {{ $examData['created_at'] }}
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>

            </div>
        </div>
    </div>

    @endif



</div>
@endif

@endsection

@push('footer')
<script src="{{ asset('public/dashboard/libs/jquery-knob/jquery.knob.min.js') }}"></script>
<script src="{{ asset('public/dashboard/js/pages/jquery-knob.init.js') }}"></script>
<script>
    function courseChanged(el) {
        var course_id = el.value;

        location = '?course_id=' + course_id;
    }

    @if(isset($watchedLessons))
    // create data
    var data = [
        {
            x: "Completed",
            value: {{ $countWatchedLessons }},
            normal: {
                fill: "dodgerblue",
            },
         },
        {
            x: "Uncompleted",
            value: {{ 100-$countWatchedLessons }},
            normal: {
                fill: "lightgray",
            },
        },
    ];

    // create a chart and set the data
    chart = anychart.pie(data);

    // set the container id
    chart.container("courseCompletion");

    // initiate drawing the chart
    chart.draw();
    @endif

    @if (isset($topics))
    anychart.onDocumentReady(function () {
        // create data set on our data
        var dataSet = anychart.data.set([
            @foreach($topics as $topic)
            @php
            echo "['".$topic->name."',".$topic->percentage.",".(100-$topic->percentage)."],";
            @endphp
            @endforeach
        ]);

        // map data for the first series, take x from the zero column and value from the first column of data set
        var firstSeriesData = dataSet.mapAs({ x: 0, value: 1 });

        // map data for the second series, take x from the zero column and value from the second column of data set
        var secondSeriesData = dataSet.mapAs({ x: 0, value: 2 });

        // map data for the second series, take x from the zero column and value from the third column of data set
        var thirdSeriesData = dataSet.mapAs({ x: 0, value: 3 });

        // map data for the fourth series, take x from the zero column and value from the fourth column of data set
        var fourthSeriesData = dataSet.mapAs({ x: 0, value: 4 });

        // create bar chart
        var chart = anychart.column();

        // turn on chart animation
        chart.animation(true);

        // force chart to stack values by Y scale.
        chart.yScale().stackMode('percent');

        // set chart title text settings
        // chart.title('');

        // set yAxis labels formatting, force it to add % to values
        chart.yAxis(0).labels().format('{%Value}%');
        chart.xAxis(0).labels().format('');

        // helper function to setup label settings for all series
        var setupSeries = function (series, name) {
            series.name(name).stroke('2 #fff 1');
            series.hovered().stroke('2 #fff 1');
        };

        // temp variable to store series instance
        var series;

        // create second series with mapped data
        series = chart.column(secondSeriesData);
        series.fill('lightgray');
        setupSeries(series, 'Not Completed');

        // create first series with mapped data
        series = chart.column(firstSeriesData);
        series.fill('dodgerblue');
        setupSeries(series, 'Correct');
        chart.tooltip().format("{%seriesName}: {%yPercentOfCategory}%");


        chart.interactivity().hoverMode('by-x');
        chart.tooltip().titleFormat('{%X}').displayMode('union');

        // turn on legend
        // chart.legend().enabled(true).fontSize(13);

        // set container id for the chart
        chart.container('stackedAnalytics');

        // initiate chart drawing
        chart.draw();
    });
    @endif
</script>
@endpush
