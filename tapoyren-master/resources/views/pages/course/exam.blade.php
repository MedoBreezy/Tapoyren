@extends('layouts.tapoyren')

@section('content')

@include('pages.parts.header')

<main class="page-wrapper">


   <main class="course_inside">

      <div class="sidebar">
         <div class="course_thumb">
            <img src="{{ $course->thumbnail_url }}" class="course_thumb" />
         </div>
         <div class="course_info">
            <div>
               <span class="bold">{{ $course->timescaleView(true) }}</span>
               <span class="gray" style="text-transform: uppercase;">@tr('hours_lowercase')</span>
            </div>
            <div>
               <span class="bold">{{ $course->difficulty() }}</span>
               <span class="gray">@tr('difficulty')</span>
            </div>
            <div>
               <span class="bold">{{ $course->students->count() }}</span>
               <span class="gray">@tr('students_enrolled')</span>
            </div>
            <div>
               <span class="bold">{{ $course->language }}</span>
               <span class="gray">@tr('language')</span>
            </div>
         </div>
         <div class="course_content">
            <h4>@tr('course_content')</h4>

            <div class="contents">
               @foreach($course->sections as $section)
               <div class="section">
                  <span>{{ $section->title }}</span>
                  <span>{{ $section->timescaleView() }}</span>
               </div>
               <div class="section-content hidden">
                  @foreach($section->videos as $video)

                  @php
                  $isWatched = (auth()->check() && auth()->user()->lessonCompleted($video->id));
                  @endphp
                  <a href="{{ url("course/{$course->id}/lesson/{$video->id}") }}">
                     <div class="section section-video" style="align-items: center;">
                        <div>
                           <span class="material-icons icon-16pt icon--left text-muted">
                              @if($isWatched)
                              done
                              @else
                              play_circle_outline
                              @endif
                           </span>
                           <span>{{ $video->title }}</span>
                        </div>
                        <span>{{ $video->timescaleViewHourly() }}</span>
                     </div>
                  </a>

                  @foreach($course->exams->where('status','active')->where('order_lecture_id',$video->id) as
                  $courseExam)
                  <a href="{{ url('course/'.$course->id.'/exam/'.$courseExam->id) }}">
                     <div class="section section-video" style="align-items: center;">
                        <div>
                           <span class="material-icons icon-16pt icon--left text-muted">
                              @if(auth()->check() && auth()->user()->examCompleted($courseExam->id))
                              done
                              @else
                              grading
                              @endif
                           </span>
                           <span>{{ $courseExam->title }}</span>
                        </div>
                     </div>
                  </a>
                  @endforeach

                  @endforeach
               </div>
               @endforeach
            </div>

         </div>
      </div>

      <div class="course_details">
         <div id="react-take-exam" data-course-id="{{ $exam->course_id }}" data-exam-id="{{ $exam->id }}"
            data-token="{{ auth()->user()->api_token }}"></div>
      </div>

   </main>


   @include('pages.parts.footer')
</main>


@endsection

@push('footer')

<script src="https://cdn.anychart.com/releases/v8/js/anychart-base.min.js"></script>
<script src="https://cdn.anychart.com/releases/v8/js/anychart-ui.min.js"></script>
<script src="https://cdn.anychart.com/releases/v8/js/anychart-exports.min.js"></script>
<link href="https://cdn.anychart.com/releases/v8/css/anychart-ui.min.css" type="text/css" rel="stylesheet">
<link href="https://cdn.anychart.com/releases/v8/fonts/css/anychart-font.min.css" type="text/css" rel="stylesheet">
<style>
    .anychart-credits {
        display: none !important;
    }
</style>


<script>
   $(document).ready(function () {

      var rating = {!! $course->myRating() != null ? 'parseInt("{{$course->myRating()->rating}}")' : 'null' !!};

   $('.rate_course .stars i').hover(function (e) {
      var hoverIndex = $(this).index();

      $('.rate_course .stars i').each(function (index, el) {
         if (index <= hoverIndex) $(el).text('star');
         else $(el).text('star_border');
      })

   });

   $('.rate_course .stars i').click(function (e) {
      var hoverIndex = $(this).index();

      window.location = '{{ url("course/".$course->id."/rating") }}/' + (hoverIndex + 1);

      $('.rate_course .stars i').each(function (index, el) {
         if (index <= hoverIndex) $(el).text('star');
         else $(el).text('star_border');
      })

   });

   $('.rate_course').mouseleave(function () {
      $('.rate_course .stars i').each(function (index, el) {
         if ($(this).index() >= rating) $(el).text('star_border');
         else $(el).text('star');
      })
   });


   const sections = document.querySelectorAll('.course_content .contents > .section');
   sections.forEach(section => {
      section.addEventListener('click', (e) => {
         sections.forEach(s => s.classList.remove('active'));

         section.classList.add('active');

         section.nextElementSibling.classList.toggle('hidden');
      });

   });
   });
</script>
@endpush
