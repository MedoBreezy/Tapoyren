@extends('layouts.tapoyren')


@section('content')

@include('pages.parts.header')

<main class="page-wrapper">


   <div class="page-title">
      <h1>@tr('terms_and_conditions')</h1>
   </div>

   <div class="page-content padded">
      @tr('terms_and_conditions_inside')

   </div>

   @include('pages.parts.footer')
</main>



@endsection