@extends('layouts.tapoyren')


@section('content')

@include('pages.parts.header')

<main class="page-wrapper">
   <div class="hero_auth">
      <div class="text">
         <img src="{{ asset('public/design/assets/tapoyren_icon.png') }}" />
         <div class="heading">{{ config('app.name') }}</div>
         <div class="description">@tr('tapoyren_is')</div>
      </div>

      <div class="auth">

         <form action="{{ url('forgot_password') }}" method="POST" class="content active" style="margin: 0;">
            @csrf

            @if($errors->any())
            <div style="text-align: center; margin-bottom: 30px; padding: 0 10px;">{{ $errors->first() }}</div>
            @endif

            <div class="input">
               <input type="email" name="email" />
               <span class="placeholder">@tr('email')</span>
            </div>

            <button class="blue" style="margin-top: 0px;">@tr('send_to_mail')</button>

         </form>

      </div>

   </div>


   @include('pages.parts.footer')
</main>



@endsection
