@extends('layouts.tapoyren')

@section('content')

@include('pages.parts.header')


<main class="page-wrapper">


   <div class="page-title">
      <h1>@tr('faq')</h1>
   </div>

   <div class="page-content padded">
      @foreach(App\FaqQuestion::all() as $faq)

      <div class="faq">
         <div class="question">{{ $faq->__('question') }}</div>
         <button onclick="toggleFaq(this)" class="btn btn-sm btn-info ml-16pt">
            <i class="material-icons">expand_more</i>
         </button>
         <div class="faq_description hidden">{!! $faq->__('description') !!}</div>
      </div>

      @endforeach

   </div>

   @include('pages.parts.footer')
</main>



@endsection

@push('footer')

<script>
   function toggleFaq(e) {
      const icon = e.querySelector('i');
      const currentIcon = icon.innerText;

      e.parentElement.querySelector('.faq_description').classList.toggle('hidden');

      if (currentIcon === 'expand_more') icon.innerText = 'expand_less';
      else icon.innerText = 'expand_more';
   }
</script>

@endpush
