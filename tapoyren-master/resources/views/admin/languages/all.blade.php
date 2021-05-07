@extends('layouts.admin')


@section('content')

<h1>Languages</h1>
<hr />

@foreach(App\Language::all() as $language)
<div style="display: flex; align-items: center; margin-bottom: 10px;">
    <h3 style="margin: 0; margin-right: 25px;">{{ $language->title }}</h3>
    <div>
        <a href="{{ url('admin/language/'.$language->id.'/translations') }}" class="btn btn-primary btn-sm">Translations</a>
        <a href="{{ url('admin/language/'.$language->id.'/delete') }}" class="btn btn-danger btn-sm">Delete</a>
    </div>
</div>

@endforeach

@endsection