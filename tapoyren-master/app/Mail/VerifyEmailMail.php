<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class VerifyEmailMail extends Mailable
{
   use Queueable, SerializesModels;

   public $name;
   public $token;

   /**
    * Create a new message instance.
    *
    * @return void
    */
   public function __construct($name, $token)
   {
      $this->name = $name;
      $this->token = $token;
      //
   }

   /**
    * Build the message.
    *
    * @return $this
    */
   public function build()
   {
      $url = url('email/verify/' . $this->token);
      return $this->view('mails.verify_email')->withName($this->name)->withUrl($url);
   }
}
