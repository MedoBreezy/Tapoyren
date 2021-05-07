@extends('layouts.dashboard.layout')

@section('content')

<div class="row mt-4">


    @if(!isset($user))
    <div class="col-lg-3">
        <div class="card">
            <div class="card-body">

                <h4 class="card-title">Employees ({{ $company->users()->count() }})</h4><br />

                <div class="d-flex flex-wrap align-items-center center">
                    <div class="col-12" style="max-height: 80vh; overflow-y: scroll;">
                        <select class="custom-select" onchange="userChanged(this)">
                            <option value="">Select Employee</option>
                            @foreach($company->users() as $user)
                            <option value="{{ $user->id }}" {{ request()->input('user_id')==$user->id ? 'selected' : '' }}>
                                {{ $user->name }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <div class="col-lg-12"></div>

    <div class="col-lg-5">
        <div class="card">
            <div class="card-body">

                <h4 class="card-title">Courses ({{ $courses->count() }})</h4><br />

                <div class="d-flex flex-wrap align-items-center center">
                    <div class="col-12" style="max-height: 50vh; overflow-y: scroll;">
                        <div id="accordion" style="max-height: 45vh; overflow-y:scroll; padding-bottom: 60px;">
                            @foreach($courses as $employeeCourse)
                            <div class="card mb-1 shadow-none">
                                <div class="card-header" href="#course_employees_{{ $loop->index }}"
                                    class="text-dark collapsed" data-toggle="collapse" aria-expanded="false"
                                    aria-controls="exam_results_{{ $loop->index }}" style="cursor: pointer;">
                                    <h6 class="m-0 d-flex justify-content-between">
                                        <b>{{ $employeeCourse['title'] }}</b>
                                        <div style="display: inline-flex; align-items: center;">
                                            <a href="?leaderboard={{ $employeeCourse['id'] }}">
                                                <i class="material-icons">leaderboard</i>
                                            </a>
                                            <span class="text-success" style="display: inline-flex; align-items: center; margin-left: 20px;">
                                                {{ count($employeeCourse['users']) }}
                                                <i class="material-icons">person</i>
                                            </span>
                                        </div>
                                    </h6>
                                </div>

                                <div id="course_employees_{{ $loop->index }}" class="collapse" data-parent="#accordion">
                                    <div class="card-body">
                                        @foreach($employeeCourse['users'] as $courseUserEmployee)
                                        <b>{{ $courseUserEmployee['name'] }}</b><br />
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    @if(isset($leaderboard))
    <div class="col-lg-6">
        <div class="card">
            <div class="card-body">

                <h4 class="card-title">Leaderboard - {{ $leaderboardCourse->title }}</h4><br />

                <div>
                    <table class="table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>Completed lessons</th>
                                <th>Attended Quiz</th>
                                <th>Failed Quiz</th>
                                <th>Average Topic</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($leaderboardTable as $trow)
                            <tr>
                                <td>{{ $loop->index + 1 }}</td>
                                <td>{{ $trow['name'] }}</td>
                                <td>{{ $trow['lessonsPercentage'] }}%</td>
                                <td>{{ $trow['attendedQuiz'] }}</td>
                                <td>{{ $trow['failedQuiz'] }}</td>
                                <td>{{ $trow['averageTopic'] }}%</td>
                            </tr>
                            @endforeach
                        </tbody>

                        </table>
                </div>

            </div>

            </div>
    </div>
    @endif

    @else
    <div class="col-lg-6">
        <div class="card">
            <div class="card-body">

                <div style="display: flex; margin-bottom: 15px;">
                    <a href="{{ url('company/dashboard') }}" style="margin-right: 30px;">&laquo; Back</a>
                    <h4 class="card-title">Employee ({{ $user->name }})</h4><br />
                </div>

                <div class="d-flex flex-wrap align-items-center center">
                    <div class="col-12" style="max-height: 80vh; overflow-y: scroll;">
                        <select class="custom-select" onchange="courseChanged(this)">
                            <option value="">Select Course</option>
                            @foreach($user->enrolledCourses() as $enrolledCourse)
                            <option value="{{ $enrolledCourse->id }}"
                                {{ request()->input('user_course_id')==$enrolledCourse->id ? 'selected' : '' }}>
                                {{ $enrolledCourse->title }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

        </div>
    </div>
    @if(isset($topics) && count($topics)>0)
    <div class="col-lg-8">
        <div class="card">
            <div class="card-body">

                <h4 class="card-title">Topic based exam results</h4><br />

                <div class="d-flex flex-wrap align-items-center center" style="width: 100%;">
                    <div id="stackedAnalytics" style="width: 100%; height: 500px;"></div>
                </div>

            </div>

        </div>
    </div>
    @endif
    @if(isset($exams) && count($exams)>0)
    <div class="col-lg-4">
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
                    @foreach($exams->reverse() as $examData)
                    <div class="card mb-1 shadow-none">
                        <div class="card-header" id="exam_name_{{ $loop->index }}">
                            <h6 class="m-0 d-flex justify-content-between" href="#exam_results_{{ $loop->index }}"
                                class="text-dark collapsed" data-toggle="collapse" aria-expanded="false"
                                aria-controls="exam_results_{{ $loop->index }}" style="cursor: pointer">
                                <div class="d-flex align-items-center" style="width: 65%">
                                    <b>
                                        {{ $examData['exam']->title }}
                                    </b>
                                </div>
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
                                Student's Score: {{ $examData['points'] }}<br />

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
    @if(isset($watchedLessons))
    <div class="col-lg-6">
        <div class="card">
            <div class="card-body">

                <h4 class="card-title">Course Completion</h4><br />

                <div id="courseCompletion" style="width: 100%; height: 350px;"></div>



            </div>
        </div>
    </div>
    @endif
    @endif


</div>

@endsection

@push('footer')
<script src="{{ asset('public/dashboard/libs/jquery-knob/jquery.knob.min.js') }}"></script>
<script src="{{ asset('public/dashboard/js/pages/jquery-knob.init.js') }}"></script>
<script>
    function userChanged(el) {
        var user_id = el.value;

        location = '?user_id=' + user_id;
    }

    function courseChanged(el) {
        var course_id = el.value;

        location = '?user_id={{ request()->input('user_id') }}&user_course_id=' + course_id;
    }

    @if(isset($watchedLessons))
    // create data
    var data = [
        {
            x: "Completed",
            value: {{ $watchedLessons }},
            normal: {
                fill: "dodgerblue",
            },
         },
        {
            x: "Uncompleted",
            value: {{ 100-$watchedLessons }},
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
