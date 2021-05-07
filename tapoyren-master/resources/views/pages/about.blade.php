@extends('layouts.tapoyren')


@section('content')

@include('pages.parts.header')

<main class="page-wrapper">


   <div class="page-title">
      <h1>@tr('about')</h1>
   </div>

   <div class="page-content padded">
      @tr('about_inside')

   </div>

   @include('pages.parts.footer')
</main>



@endsection
