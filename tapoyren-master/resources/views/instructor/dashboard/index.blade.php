@extends('layouts.dashboard.layout')

@section('content')

<div class="row mt-4">

    <div class="col-lg-6">
        <div class="card">
            <div class="card-body">

                <div style="display: flex; margin-bottom: 15px;">
                    @if(request()->input('course_id'))
                    <a href="{{ url('instructor/dashboard') }}" style="margin-right: 30px;">&laquo; Back</a>
                    @endif
                    <h4 class="card-title">Courses </h4><br />
                </div>

                <div class="d-flex flex-wrap align-items-center center">
                    <div class="col-12" style="max-height: 80vh; overflow-y: scroll;">
                        <select class="custom-select" onchange="courseChanged(this)">
                            <option value="">Select Course</option>
                            @foreach($courses as $myCourse)
                            <option value="{{ $myCourse->id }}"
                                {{ request()->input('course_id')==$myCourse->id ? 'selected' : '' }}>
                                {{ $myCourse->title }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                </div>

            </div>

        </div>
    </div>
    @if(isset($course))
    <div class="col-lg-6">
        <div class="card">
            <div class="card-body">

                <h5>Total Enrolled Students: {{ $course->students->count() }}</h5>
                <h5>Active Students: {{ $course->students()->isActive()->count() }}</h5>
                <h5>Total Payments: {{ $totalPayment }} AZN</h5>
                <h5>Total Revenue: {{ $totalRevenue }} AZN</h5>
                <h5>Average Rating: {{ $course->rating }}</h5>

                <br />
                <div class="col-12">
                    <select class="custom-select" onchange="rangeChanged(this)">
                        @foreach($ranges as $key=>$name)
                        @php
                        $checkOption = false;
                        if(request()->input('range') && request()->input('range')==$key) $checkOption = true;

                        $checkFirst = false;
                        if(!request()->input('range') && $key==='last_month') $checkFirst = true;
                        @endphp
                        <option value="{{ $key }}"
                            {{ $checkOption ? 'selected' : '' }}{{ $checkFirst ? 'selected' : ''}}>{{ $name }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <div id="column_chart" class="apex-charts" dir="ltr"></div>

            </div>
        </div>

    </div>
    @endif

    @endsection

    @push('footer')
    <script src="{{ asset('public/dashboard/libs/jquery-knob/jquery.knob.min.js') }}"></script>
    <script src="{{ asset('public/dashboard/libs/apexcharts/apexcharts.min.js') }}"></script>
    <script src="{{ asset('public/dashboard/js/pages/jquery-knob.init.js') }}"></script>
    <script>
        options = {
            chart: {
                height: 350,
                type: "bar",
                toolbar: {
                    show: !1
                }
            },
            plotOptions: {
                bar: {
                    horizontal: !1,
                    columnWidth: "45%",
                    endingShape: "rounded"
                }
            },
            dataLabels: {
                enabled: !1
            },
            stroke: {
                show: !0,
                width: 2,
                colors: ["transparent"]
            },
            series: [{
                name: "Total Payments",
                data: [46, 57, 59, 54, 62, 58, 64, 60, 66]
            }, {
                name: "Revenue",
                data: [74, 83, 102, 97, 86, 106, 93, 114, 94]
            }],
            colors: ["#45cb85", "#3b5de7"],
            xaxis: {
                categories: ["1", "2", "3", "4", "5", "6", "7", "8", "9", "10"]
            },
            yaxis: {
                title: {
                    text: "AZN"
                }
            },
            grid: {
                borderColor: "#f1f1f1"
            },
            fill: {
                opacity: 1
            },
            tooltip: {
                y: {
                    formatter: function (e) {
                        return e + ' AZN'
                    }
                }
            }
        };
        (chart = new ApexCharts(document.querySelector("#column_chart"), options)).render();

        function courseChanged(el) {
            var course_id = el.value;

            location = '?course_id=' + course_id;
        }

        function rangeChanged(el) {
            var range = el.value;

            location = '?course_id={{ request()->input('course_id') }}&range=' + range;
        }
    </script>
    @endpush
