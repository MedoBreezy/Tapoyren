@extends('layouts.admin')


@section('content')
<div class="mdk-header-layout js-mdk-header-layout">

    <div class="mdk-header-layout__content page-content">

        <div style="width: 80%; margin: 0 auto; padding: 15px;">

            <ul>
                @foreach(App\Package::all() as $package)
                <li class="mb-8pt">
                    <a href="#">{{ $package->name }}</a>
                    <a class="btn-success btn-sm mr-8pt" href="#">
                        @if($package->status==='deactive')
                        Deactive
                        @else
                        Active
                        @endif
                    </a>
                    <!-- <a class="btn btn-info btn-sm text-white" href="{{ url('admin/package/'.$package->id.'/edit') }}">Edit</a> -->
                    <!-- <a class="btn btn-danger btn-sm text-white" href="{{ url('admin/package/'.$package->id.'/delete') }}">Delete</a> -->
                    @if($package->status==='deactive')
                    <a class="btn btn-success btn-sm text-white" href="{{ url('admin/package/'.$package->id.'/publish') }}">Publish</a>
                    @else
                    <a class="btn btn-warning btn-sm" href="{{ url('admin/package/'.$package->id.'/draft') }}">Draft</a>
                    @endif
                    <a class="btn btn-info btn-sm" href="{{ url('admin/package/'.$package->id.'/edit') }}">Edit</a>
                </li>
                @endforeach
            </ul>

        </div>

    </div>

</div>


@endsection
