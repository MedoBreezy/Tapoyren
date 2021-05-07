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

         <form action="{{ url('reset_password/'.$token) }}" method="POST" class="content active" style="margin: 0;">
            @csrf

            @if($errors->any())
            <div style="text-align: center; margin-bottom: 30px; padding: 0 10px;">{{ $errors->first() }}</div>
            @endif

            <div class="input">
               <input type="password" name="password" />
               <span class="placeholder">@tr('password')</span>
            </div>

            <div class="input">
               <input type="password" name="password_confirmation" />
               <span class="placeholder">@tr('password_confirmation')</span>
            </div>

            <button class="blue" style="margin-top: 0px;">@tr('reset')</button>

         </form>

      </div>

   </div>


   @include('pages.parts.footer')
</main>



@endsection


@push('footer')

<script>
   var tabs = document.querySelectorAll('.hero_auth .auth .tabs .tab');
   var tab_contents = document.querySelectorAll('.hero_auth .auth .content');

   function showTab(tabName, el) {
      tabs.forEach(tab => tab.classList.remove('active'));
      tab_contents.forEach(tabContent => tabContent.classList.remove('active'));
      el.classList.add('active');
      document.querySelector('.tab-' + tabName).classList.add('active');
   }

   function rememberMe() {
      document.querySelector('.tab-login input[id=remember_me]').click();
   }

   function agreeToTerms() {
      document.querySelector('.tab-register input[id=agreeToTerms]').click();
   }

</script>

@endpush
