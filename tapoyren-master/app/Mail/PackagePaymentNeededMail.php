<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PackagePaymentNeededMail extends Mailable
{
    use Queueable, SerializesModels;

    public $student;
    public $package;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($student, $package)
    {
        $this->student = $student;
        $this->package = $package;
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
            'package' => $this->package
        ];
        return $this->view('mails.package_payment_needed')->with($data);
    }
}
