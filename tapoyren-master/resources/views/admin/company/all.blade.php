@extends('layouts.admin')


@section('content')
<div class="mdk-header-layout js-mdk-header-layout">

    <div class="mdk-header-layout__content page-content">

        <div style="width: 80%; margin: 0 auto; padding: 15px;">

            <ul>
                @foreach(App\Company::all() as $company)
                <li>
                    <a href="#">{{ $company->title }}</a>
                    <a class="btn btn-info btn-sm text-white"
                        href="{{ url('admin/company/'.$company->id.'/enrollments') }}">Enrollments</a>
                    <a class="btn btn-danger btn-sm text-white"
                        href="{{ url('admin/company/'.$company->id.'/delete') }}">Delete</a>
                    <ul>
                        @foreach($company->users() as $user)
                        <li>
                            {{ $user->name }}
                            <i>{{ $user->email }}</i>
                        </li>
                        @endforeach
                    </ul>
                </li>
                @endforeach
            </ul>

        </div>


    </div>

</div>

@endsection
