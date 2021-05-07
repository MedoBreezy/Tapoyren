@extends('layouts.admin')


@section('content')
<div class="mdk-header-layout js-mdk-header-layout">

   <div class="mdk-header-layout__content page-content">

      <div style="width: 80%; margin: 0 auto; padding: 15px;">

         <div style="display: flex; flex-direction: column;">
            @foreach(App\Course::all() as $course)
            <div style="margin: 10px 0; background: white; padding: 10px; border-radius: 4px;">
               <div>
                  <li class="mb-8pt">
                     <a href="#">{{ $course->title }}</a>
                     <a class="btn-success btn-sm mr-8pt" href="#">
                        @if($course->status==='pending')
                        Draft
                        @else
                        Published
                        @endif
                     </a>
                     <a class="btn btn-info btn-sm text-white"
                        href="{{ url('admin/course/'.$course->id.'/edit') }}">Edit</a>
                     <a class="btn btn-info btn-sm text-white"
                        href="{{ url('admin/course/'.$course->id.'/edit_data') }}">Edit Data</a>
                     <a class="btn btn-info btn-sm" href="{{ url('admin/course/'.$course->id.'/resource/add') }}">Add
                        Lesson Resource</a>
                     <a class="btn btn-danger btn-sm text-white"
                        href="{{ url('admin/course/'.$course->id.'/delete') }}">Delete</a>
                     @if($course->status==='pending')
                     <a class="btn btn-success btn-sm text-white"
                        href="{{ url('admin/course/'.$course->id.'/publish') }}">Publish</a>
                     @else
                     <a class="btn btn-warning btn-sm" href="{{ url('admin/course/'.$course->id.'/draft') }}">Draft</a>
                     @endif
                  </li>
               </div>
               @if($course->exams->count()>0)
               <h5>EXAMS</h5>
               @foreach($course->exams as $exam)
               <div style="margin: 4px 0;">
                  <a href="{{ url('admin/course/'.$course->id.'/exam/'.$exam->id.'/edit') }}"
                     class="btn btn-success btn-sm mr-8pt">
                     {{ $exam->title }}
                  </a>
                  @if($exam->status==='deactive')
                  <a class="btn btn-success btn-sm text-white"
                     href="{{ url('admin/course/'.$course->id.'/exam/'.$exam->id.'/publish') }}">Publish</a>
                  @else
                  <a class="btn btn-warning btn-sm"
                     href="{{ url('admin/course/'.$course->id.'/exam/'.$exam->id.'/draft') }}">Draft</a>
                  @endif
               </div>
               @endforeach
               @endif
            </div>
            @endforeach
         </div>

      </div>

   </div>

</div>


@endsection
