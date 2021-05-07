@extends('layouts.tapoyren')


@section('content')

@include('pages.parts.header')

<main class="page-wrapper">


   <div class="page-title">
      <h1>@tr('contact')</h1>
   </div>

   <div class="page-content padded">
      <h2>@tr('contact_details')</h2>

      @tr('contact_inside')

   </div>

   @include('pages.parts.footer')
</main>



@endsection
