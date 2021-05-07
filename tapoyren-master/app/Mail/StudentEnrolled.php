<?php

namespace App\Mail;

use App\Course;
use App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class StudentEnrolled extends Mailable
{
   use Queueable, SerializesModels;

   public $course;
   public $student;

   /**
    * Create a new message instance.
    *
    * @return void
    */
   public function __construct(Course $course, User $student)
   {
      $this->course = $course;
      $this->student = $student;
      //
   }

   /**
    * Build the message.
    *
    * @return $this
    */
   public function build()
   {
      return $this->view('mails.student_enrolled', [
         'course' => $this->course,
         'instructor' => $this->course->instructor,
         'student' => $this->student,
      ]);
   }
}
