@extends('layouts.admin')

@section('content')

<h1>Kupon kodlar</h1>

@foreach($coupons as $coupon)

<h3>{{ $coupon['code'] }}</h3>

@endforeach

@endsection
