@extends('layouts.tapoyren')


@section('content')

@include('pages.parts.header')

<main class="page-wrapper">

    <div class="page-title">
        <h1>{{ $package->name }}</h1>
    </div>

    <div class="page-payment">
        <div class="text-center">


            <div class="packages">

                @if($package->price_monthly > 0)
                <div class="package">

                    <div class="head_border">
                        <div class="head">
                            <div class="price">{{ priceSymbolByLocale() }} {{ $package->price_monthly }}</div>
                            <div class="small">@tr('monthly')</div>
                        </div>
                    </div>

                    <!-- <div class="refund">7 @tr('days_money_back')</div> -->

                    <form class="coupon" method="GET" action="{{ url('package/'.$package->id.'/subscribe/monthly') }}">
                        <input type="text" placeholder="Kupon kodu" name="coupon" />
                        <button class="blue" type="submit">@tr('get_started')</button>
                    </form>

                </div>
                @endif
                @if($package->price_quarterly > 0)
                <div class="package package-blue">

                    <div class="head_border">
                        <div class="head">
                            <div class="small">@tr('recommended')</div>
                            <div class="price">{{ priceSymbolByLocale() }} {{ $package->price_quarterly }}</div>
                            <div class="small">@tr('quarterly')</div>
                        </div>
                    </div>

                    <!-- <div class="refund">7 @tr('days_money_back')</div> -->

                    <form class="coupon" method="GET"
                        action="{{ url('package/'.$package->id.'/subscribe/quarterly') }}">
                        <input type="text" placeholder="Kupon kodu" name="coupon" />
                        <button class="blue" type="submit">@tr('get_started')</button>
                    </form>

                </div>
                @endif
                @if($package->price_semiannually > 0)
                <div class="package">

                    <div class="head_border">
                        <div class="head">
                            <div class="price">{{ priceSymbolByLocale() }} {{ $package->price_semiannually }}</div>
                            <div class="small">@tr('semi_annually_lowercase')</div>
                        </div>
                    </div>

                    <!-- <div class="refund">7 @tr('days_money_back')</div> -->

                    <form class="coupon" method="GET" action="">
                        <input type="text" placeholder="Kupon kodu" name="coupon" />
                        <button class="blue" type="submit">@tr('get_started')</button>
                    </form>

                </div>
                @endif
                @if($package->price_annually > 0)
                <div class="package">

                    <div class="head_border">
                        <div class="head">
                            <div class="price">{{ priceSymbolByLocale() }} {{ $package->price_annually }}</div>
                            <div class="small">@tr('annually_lowercase')</div>
                        </div>
                    </div>

                    <!-- <div class="refund">7 @tr('days_money_back')</div> -->

                    <form class="coupon" method="GET" action="{{ url('package/'.$package->id.'/subscribe/annually') }}">
                        <input type="text" placeholder="Kupon kodu" name="coupon" />
                        <button class="blue" type="submit">@tr('get_started')</button>
                    </form>

                </div>
                @endif

            </div>

        </div>
    </div>

    @include('pages.parts.footer')
</main>



@endsection
