<?php

namespace App\Mail;

use App\Course;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class CompleteCourseMail extends Mailable
{
   use Queueable, SerializesModels;

   public $course;

   /**
    * Create a new message instance.
    *
    * @return void
    */
   public function __construct(Course $course)
   {
      $this->course = $course;
      //
   }

   /**
    * Build the message.
    *
    * @return $this
    */
   public function build()
   {
      return $this->view('mails.complete_course')->withData($this->course->finishCourseMail);
   }
}
