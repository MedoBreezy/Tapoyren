@extends('layouts.tapoyren')


@section('content')

@include('pages.parts.header')

<main class="page-wrapper">

    <div class="page-title">
        <h1>@tr('payments')</h1>
    </div>

    <div style="text-align: center;">
        <h1>@tr('course_payments')</h1>
        <table border="1" style="margin: 20px auto;">
            <thead>
                <tr>
                    <th style="padding: 15px;">@tr('course')</th>
                    <th style="padding: 15px;">@tr('subscription_type')</th>
                    <th style="padding: 15px;">@tr('price')</th>
                    <th style="padding: 15px;">@tr('date')</th>
                    <th style="padding: 15px;">@tr('sub_end_date')</th>
                </tr>
            </thead>
            <tbody>
                @foreach($coursePayments as $payment)
                <tr>
                    <td style="padding: 10px 15px;">
                        <a href="{{ url('course/'.$payment->course->id) }}">{{ $payment->course->title }}</a>
                    </td>
                    <td style="padding: 10px 15px;">
                        {{ translate($payment->subscription_type) }}
                    </td>
                    <td style="padding: 10px 15px;">
                        {{ $payment->price }} ₼
                    </td>
                    <td>
                        {{ $payment->date }}
                    </td>
                    <td>
                        {{ $payment->end_date }}
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <hr />

        <h1>@tr('package_payments')</h1>
        <table border="1" style="margin: 20px auto;">
            <thead>
                <tr>
                    <th style="padding: 15px;">@tr('package')</th>
                    <th style="padding: 15px;">@tr('subscription_type')</th>
                    <th style="padding: 15px;">@tr('price')</th>
                    <th style="padding: 15px;">@tr('date')</th>
                    <th style="padding: 15px;">@tr('sub_end_date')</th>
                </tr>
            </thead>
            <tbody>
                @foreach($packagePayments as $payment)
                <tr>
                    <td style="padding: 10px 15px;">
                        <a href="{{ url('package/'.$payment->package->id) }}">{{ $payment->package->name }}</a>
                    </td>
                    <td style="padding: 10px 15px;">
                        {{ translate($payment->subscription_type) }}
                    </td>
                    <td style="padding: 10px 15px;">
                        {{ $payment->price }} ₼
                    </td>
                    <td>
                        {{ $payment->date }}
                    </td>
                    <td>
                        {{ $payment->end_date }}
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

    </div>

    @include('pages.parts.footer')
</main>



@endsection
