@extends('layouts.main')

@section('content')

<div class="mdk-header-layout js-mdk-header-layout">
    @include('pages.parts.header')
    <div class="mdk-header-layout__content page-content">

        <div class="mdk-drawer-layout js-mdk-drawer-layout" data-responsive-width="768px" data-push>

            <!-- content -->
            <div class="mdk-drawer-layout__content">
                <div class="page-section">
                    <div class="container page__container" style="max-width: 40%;">

                        <h2>Messages - {{ $instructor->name }}</h2>

                        <div class="messages" style="height: 40vh; overflow-y: scroll; padding: 20px;">
                            @foreach($conversation->messages as $message)
                            @if($message->sender==='student')
                            <div style="background: #73B508; color: white; border-radius: 4px; padding: 6px 10px; margin: 4px 0; width: max-content; float: right;">
                                {{ $message->message }}
                            </div>
                            @elseif($message->sender==='instructor')
                            <div style="background: dodgerblue; color: white; border-radius: 4px; padding: 6px 10px; margin: 4px 0; width: max-content; float: left;">
                                {{ $message->message }}
                            </div>
                            @endif
                            <div style="clear: both;"></div>
                            @endforeach
                        </div>

                        <form method="POST" action="{{ url("instructor/{$instructor->id}/conversation/{$conversation->id}/new_message") }}">

                            {{ csrf_field() }}

                            @if($errors->any())
                            <div>
                                <b>{{ $errors->first() }}</b>
                            </div>
                            @endif

                            <textarea class="form-control" name="message" rows="4" placeholder="Your Message..."></textarea>

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

@push('footer')

<script type="text/javascript">

var messages = document.querySelector('.messages');
messages.scrollTop = messages.scrollHeight

</script>

@endpush