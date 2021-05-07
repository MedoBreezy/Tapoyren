@extends('layouts.tapoyren')


@section('content')

@include('pages.parts.header')

<main class="page-wrapper">

    <div class="page-title">
        <h1>{{ $course->title }}</h1>
    </div>

    <div class="page-payment">
        <div class="text-center">


            <div class="packages">

                @if($course->price_monthly > 0)
                <div class="package">

                    <div class="head_border">
                        <div class="head">
                            <div class="price">{{ priceSymbolByLocale() }} {{ $course->price_monthly }}</div>
                            <div class="small">@tr('monthly')</div>
                        </div>
                    </div>

                    <!-- <div class="refund">7 @tr('days_money_back')</div> -->

                    <form class="coupon" method="GET" action="{{ url('course/'.$course->id.'/enroll/monthly') }}">
                        <input type="text" placeholder="Kupon kodu" name="coupon" />
                        <button class="blue" type="submit">@tr('get_started')</button>
                    </form>

                </div>
                @endif
                @if($course->price_quarterly > 0)
                <div class="package package-blue">

                    <div class="head_border">
                        <div class="head">
                            <div class="small">@tr('recommended')</div>
                            <div class="price">{{ priceSymbolByLocale() }} {{ $course->price_quarterly }}</div>
                            <div class="small">@tr('quarterly')</div>
                        </div>
                    </div>

                    <!-- <div class="refund">7 @tr('days_money_back')</div> -->

                    <form class="coupon" method="GET" action="{{ url('course/'.$course->id.'/enroll/quarterly') }}">
                        <input type="text" placeholder="Kupon kodu" name="coupon" />
                        <button class="blue" type="submit">@tr('get_started')</button>
                    </form>

                </div>
                @endif
                @if($course->price_semiannually > 0)
                <div class="package">

                    <div class="head_border">
                        <div class="head">
                            <div class="price">{{ priceSymbolByLocale() }} {{ $course->price_semiannually }}</div>
                            <div class="small">@tr('semi_annually_lowercase')</div>
                        </div>
                    </div>

                    <!-- <div class="refund">7 @tr('days_money_back')</div> -->

                    <form class="coupon" method="GET" action="{{ url('course/'.$course->id.'/enroll/semi_annually') }}">
                        <input type="text" placeholder="Kupon kodu" name="coupon" />
                        <button class="blue" type="submit">@tr('get_started')</button>
                    </form>

                </div>
                @endif
                @if($course->price_annually > 0)
                <div class="package">

                    <div class="head_border">
                        <div class="head">
                            <div class="price">{{ priceSymbolByLocale() }} {{ $course->price_annually }}</div>
                            <div class="small">@tr('annually_lowercase')</div>
                        </div>
                    </div>

                    <!-- <div class="refund">7 @tr('days_money_back')</div> -->

                    <form class="coupon" method="GET" action="{{ url('course/'.$course->id.'/enroll/annually') }}">
                        <input type="text" placeholder="Kupon kodu" name="coupon" />
                        <button class="blue" type="submit">@tr('get_started')</button>
                    </form>

                </div>
                @endif

            </div>

            <div class="bg-white packages_include">
                <div class="w-50">
                    <h2>@tr('all_plans_include')</h2>

                    <div class="includes">
                        <div>@tr('question_and_answers')</div>
                        <div>3 @tr('days_trial')</div>
                        <div>@tr('quizzes_and_mock_exams')</div>
                        <div>@tr('support_from_instructor')</div>
                        <div>@tr('performance_dashboard')</div>
                        <div>@tr('certificate_on_completion')</div>
                        <div>@tr('recognized_by_companies')</div>
                        <div>@tr('effective_practices')</div>
                        <div>@tr('access_to_community')</div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    @include('pages.parts.footer')
</main>



@endsection
