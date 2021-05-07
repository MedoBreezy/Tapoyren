@extends('layouts.main')

@section('content')

<div class="mdk-header-layout js-mdk-header-layout">
    @include('pages.parts.header')
    <div class="mdk-header-layout__content page-content">

        <div class="mdk-drawer-layout js-mdk-drawer-layout" data-responsive-width="768px" data-push>

            <!-- content -->
            <div class="mdk-drawer-layout__content">
                <div class="page-section">
                    <div class="container page__container" style="max-width: 60%;">

                        <h2>Message to {{ $instructor->name }}</h2>
                        
                        <form method="POST" action="{{ url("course/{$course->id}/instructor/{$instructor->id}/message") }}">
                        
                            {{ csrf_field() }}
                            
                            @if($errors->any())
                            <div>
                                <b>{{ $errors->first() }}</b>
                            </div>
                            @endif

                            <textarea class="form-control" name="message" rows="4" placeholder="Your Message..."></textarea><br />

                            <br />
                            <button type="submit" class="btn btn-primary btn-lg btn-block">Send</button>
                        </form>

                    </div>
                </div>
            </div>

        </div>

        @include('pages.parts.footer')

    </div>
</div>

@include('pages.parts.drawer')
@include('pages.parts.modal_categories')

@endsection