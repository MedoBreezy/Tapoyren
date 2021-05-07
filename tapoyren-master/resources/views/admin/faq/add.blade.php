@extends('layouts.admin')


@section('content')
<h1>ADD FAQ</h1>

<div id="react-add-faq" data-token="{{ auth()->user()->api_token }}"></div>

@endsection