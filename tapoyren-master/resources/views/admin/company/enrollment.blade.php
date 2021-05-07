@extends('layouts.admin')


@section('content')
<div class="mdk-header-layout js-mdk-header-layout">

    <div class="mdk-header-layout__content page-content">

        <div style="width: 80%; margin: 0 auto; padding: 15px;">

            <h1>Total: {{ $totalPrice }} {{ priceSymbolByLocale() }}</h1>

            <table border="1">
                <thead>
                    <tr>
                        <th style="padding: 10px;">Kurs</th>
                        <th style="padding: 10px;">Tələbə</th>
                        <th style="padding: 10px;">Abunəlik</th>
                        <th style="padding: 10px;">Qiymət</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($enrollments as $enrollment)
                    <tr>
                        <td style="padding: 10px;">{{ App\Course::find($enrollment->course_id)->title }}</td>
                        <td style="padding: 10px;">{{ App\User::find($enrollment->student_id)->name }}</td>
                        <td style="padding: 10px;">{{ $enrollment->subscription }}</td>
                        <td style="padding: 10px;">{{ $enrollment->price }} {{ priceSymbolByLocale() }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>


        </div>


    </div>

</div>

@endsection
