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
               <div class="section{{ $section->id===$currentSection->id ? ' active' : '' }}">
                  <span>{{ $section->title }}</span>
                  <span>{{ $section->timescaleView() }}</span>
               </div>
               <div class="section-content{{ $section->id===$currentSection->id ? ' active' : ' hidden' }}">
                  @foreach($section->videos as $video)

                  <a href="{{ url('course/'.$course->id.'/lesson/'.$video->id) }}">
                     <div class="section section-video{{ $video->id===$currentVideo->id ? ' active' : '' }}"
                        style="align-items: center;">
                        <div>
                           <span class="material-icons icon-16pt icon--left text-muted">
                              play_circle_outline
                           </span>
                           <span>{{ $video->title }}</span>
                        </div>
                        <span>{{ $video->timescaleViewHourly() }}</span>
                     </div>
                  </a>
                  @endforeach
               </div>
               @endforeach
            </div>

         </div>
      </div>

      <div class="course_details">

         <div class="lessonPlayer">
            <iframe id="lesson-player" class="embed-responsive-item"
               src="https://player.vimeo.com/video/{{ $currentVideo->vimeoVideoId }}?"></iframe>

            <div class="information">
               <div class="instructor_avatar">
                  <div class="img_wrapper">
                     <img src="{{ $course->instructor->avatar_url }}" />
                  </div>
               </div>
               <div class="instructor_details">
                  <h1>{{ $course->instructor->name }}</h1>
                  <h4>@tr('instructor')</h4>
               </div>
               <div class="rate_course is-desktop">
                  <div class="stars">
                     <i class="material-icons">
                        @if($course->myRating())
                        star
                        @else
                        star_border
                        @endif
                     </i>
                     <i class="material-icons">
                        @if($course->myRating() && $course->myRating()->rating>1)
                        star
                        @else
                        star_border
                        @endif
                     </i>
                     <i class="material-icons">
                        @if($course->myRating() && $course->myRating()->rating>2)
                        star
                        @else
                        star_border
                        @endif
                     </i>
                     <i class="material-icons">
                        @if($course->myRating() && $course->myRating()->rating>3)
                        star
                        @else
                        star_border
                        @endif
                     </i>
                     <i class="material-icons">
                        @if($course->myRating() && $course->myRating()->rating>4)
                        star
                        @else
                        star_border
                        @endif
                     </i>
                  </div>
                  <span style="text-transform: uppercase;">@tr('rate_course')</span>
               </div>

               <div class="buttons is-desktop">
                  @if($course->previousLessonId($currentVideo))
                  <a href="{{ url('course/'.$course->id.'/lesson/'.$course->previousLessonId($currentVideo)) }}"
                     class="grey transitioned hover-opacity">
                     @tr('previous')
                  </a>
                  @endif
                  @if($course->nextLessonId($currentVideo))
                  <a href="{{ url('course/'.$course->id.'/lesson/'.$course->nextLessonId($currentVideo)) }}"
                     class="primary transitioned hover-opacity">
                     @tr('next')
                  </a>
                  @endif
               </div>

            </div>

            <div class="mobile-rate_course is-mobile">
               <div class="stars">
                  <div class="stars">
                     <i class="material-icons">
                        @if($course->myRating())
                        star
                        @else
                        star_border
                        @endif
                     </i>
                     <i class="material-icons">
                        @if($course->myRating() && $course->myRating()->rating>1)
                        star
                        @else
                        star_border
                        @endif
                     </i>
                     <i class="material-icons">
                        @if($course->myRating() && $course->myRating()->rating>2)
                        star
                        @else
                        star_border
                        @endif
                     </i>
                     <i class="material-icons">
                        @if($course->myRating() && $course->myRating()->rating>3)
                        star
                        @else
                        star_border
                        @endif
                     </i>
                     <i class="material-icons">
                        @if($course->myRating() && $course->myRating()->rating>4)
                        star
                        @else
                        star_border
                        @endif
                     </i>
                  </div>
               </div>
            </div>

            <div class="mobile-buttons is-mobile">
               <a href="{{ url('course/'.$course->id.'/lesson/'.$course->previousLessonId($currentVideo)) }}"
                  class="grey transitioned hover-opacity">
                  @tr('previous')
               </a>
               <a href="{{ url('course/'.$course->id.'/lesson/'.$course->nextLessonId($currentVideo)) }}"
                  class="primary transitioned hover-opacity">
                  @tr('next')
               </a>
            </div>

            <div class="tab_navigation">
               <a href="#">@tr('overview')</a>
               <a class="active" href="{{ url("course/{$course->id}/discussions") }}">@tr('question_and_answers')</a>
            </div>
         </div>


         <div class="discussions">
            <div class="tabs">
               <div class="tab active">

                  <div style="padding: 20px">
                     <a href="{{ url("course/{$course->id}/discussions") }}">
                        <i class="material-icons" style="font-size: 30px">chevron_left</i>
                     </a>
                  </div>

                  <div class="qa_reply">
                     <div class="me">
                        @if(auth()->user()->avatar_url)
                        <img src="{{ auth()->user()->avatar_url }}" />
                        @endif
                     </div>
                     <form action="{{ url('course/'.$course->id.'/discussions/ask') }}" class="post" method="POST">
                        {{ csrf_field() }}
                        <div>
                           <input type="text" name="title" placeholder="@tr('your_question')...">
                           <textarea name="question" rows="3" placeholder="@tr('describe_your_question')..."></textarea>
                        </div>
                        <button class="primary">@tr('ask_a_question')</button>
                     </form>

                  </div>



               </div>
            </div>
         </div>

      </div>

   </main>

   @include('pages.parts.footer')
</main>



@endsection

@push('footer')

<script>
   var previewIframe = document.getElementById('preview_iframe');

   function noAccessInfo() {
      Swal.fire(
         'Dərsliyə keçid etmək üçün kursda qeydiyyatdan keçməlisiniz!',
         '',
         'info'
      );
   }

   $(document).ready(function () {

   var rating = {!! $course->myRating() !== null ? 'parseInt("{{$course->myRating()->rating}}")' : 'null' !!};

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

   $('.mobile-rate_course .stars i').click(function (e) {
      var hoverIndex = $(this).index();

      window.location = '{{ url("course/".$course->id."/rating") }}/' + (hoverIndex + 1);

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
