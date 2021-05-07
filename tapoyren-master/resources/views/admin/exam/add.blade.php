@extends('layouts.admin')


@section('content')
<div id="react-add-exam" data-token="{{ auth()->user()->api_token }}"></div>

@endsection
