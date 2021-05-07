@extends('layouts.admin')


@section('content')
<div id="react-language-add-translations" data-language-id="{{ $language->id }}" data-token="{{ auth()->user()->api_token }}"></div>

@endsection