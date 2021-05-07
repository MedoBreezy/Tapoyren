<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ResetPasswordMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $token;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($user,$token)
    {
       $this->user = $user;
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
      $data = [
         'name' => $this->user->name,
         'url' => url('reset_password/'.$this->token)
      ];

      return $this->view('mails.reset_password')->with($data);
    }
}
