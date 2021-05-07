@extends('layouts.admin')


@section('content')
<div class="mdk-header-layout js-mdk-header-layout">

    <div class="mdk-header-layout__content page-content">

        <div style="width: 80%; margin: 0 auto; padding: 15px;">

            <ul>
                @foreach(App\User::where('type','instructor')->get() as $instructor)
                <li>
                    <a href="#">{{ $instructor->name }}</a>
                    <a class="btn btn-info btn-sm text-white" href="{{ url('admin/instructor/'.$instructor->id.'/update') }}">Update</a>
                    <a class="btn btn-danger btn-sm text-white" href="{{ url('admin/instructor/'.$instructor->id.'/delete') }}">Delete</a>
                </li>
                @endforeach
            </ul>

        </div>


    </div>

</div>

@endsection
