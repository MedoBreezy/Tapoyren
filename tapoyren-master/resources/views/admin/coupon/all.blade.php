@extends('layouts.admin')

@section('content')

<h1>Kupon kodlar</h1>


<table border="1">
    <thead>
        <tr>
            <th style="padding: 15px;">Kod</th>
            <th style="padding: 15px;">Endirim %</th>
            <th style="padding: 15px;">İstifadə</th>
        </tr>
    </thead>
    <tbody>
        @foreach(App\Coupon::all() as $coupon)
        <tr>
            <td style="padding: 10px 15px;">
                {{ $coupon->code }}
            </td>
            <td style="padding: 10px 15px;">
                {{ $coupon->discount }} %
            </td>
            <td style="padding: 10px 15px;">
                {{ $coupon->expired ? 'Olunub' : 'Olunmayıb' }}
            </td>
        </tr>
        @endforeach
    </tbody>
</table>

@endsection
