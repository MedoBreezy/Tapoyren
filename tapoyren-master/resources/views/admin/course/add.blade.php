@extends('layouts.admin')


@section('content')
<div id="react-add-course" data-token="{{ auth()->user()->api_token }}"></div>

@endsection
