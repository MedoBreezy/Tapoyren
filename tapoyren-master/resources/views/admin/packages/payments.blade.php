@extends('layouts.admin')


@section('content')
<h1>Paket Ödənişləri</h1>
<hr />

<table border="1">
    <thead>
        <tr>
            <th style="padding: 15px;">Paket</th>
            <th style="padding: 15px;">Tələbə</th>
            <th style="padding: 15px;">Ödəniş Növü</th>
            <th style="padding: 15px;">Məbləğ</th>
        </tr>
    </thead>
    <tbody>
        @foreach(App\PackagePayment::where('status','completed')->get() as $payment)
        <tr>
            <td style="padding: 10px 15px;">
                <a href="{{ url('course/'.$payment->course_id) }}">{{ App\Package::find($payment->package_id)->name }}</a>
            </td>
            <td style="padding: 10px 15px;">
                {{ App\User::find($payment->student_id)->name }} (<i>{{ App\User::find($payment->student_id)->email }}</i>)
            </td>
            <td style="padding: 10px 15px;">
                {{ $payment->subscription_type }}
            </td>
            <td style="padding: 10px 15px;">
                {{ $payment->price }} ₼
            </td>
        </tr>
        @endforeach
    </tbody>
</table>


@endsection