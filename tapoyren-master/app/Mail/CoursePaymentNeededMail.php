<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class CoursePaymentNeededMail extends Mailable
{
    use Queueable, SerializesModels;

    public $student;
    public $course;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($student, $course)
    {
        $this->student = $student;
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
        $data = [
            'student' => $this->student,
            'course' => $this->course
        ];
        return $this->view('mails.course_payment_needed')->with($data);
    }
}
