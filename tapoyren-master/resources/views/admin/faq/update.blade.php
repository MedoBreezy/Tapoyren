@extends('layouts.admin')


@section('content')
<h1>ADD FAQ</h1>

<div id="react-update-faq" data-faq="{{ $faq->id }}" data-token="{{ auth()->user()->api_token }}"></div>

@endsection