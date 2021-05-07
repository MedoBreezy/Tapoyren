@extends('layouts.admin')


@section('content')
<h1>INSTRUCTOR: {{ $instructor->name }}</h1>

<div id="react-update-instructor" data-instructor="{{ $instructor->id }}" data-token="{{ auth()->user()->api_token }}"></div>

@endsection