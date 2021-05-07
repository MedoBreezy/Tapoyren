<?php

namespace App\Http\Controllers;

use App\Mail\VerifyEmailMail;
use App\User;
use App\VerifyEmail;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class EmailController extends Controller
{

   public function verify(Request $req, $token)
   {

      if (!auth()->check()) {
         $return_url = "email/verify/{$token}";
         session()->put('return_url', $return_url);
         return redirect('/login');
         //
      }

      $verifyEmail = VerifyEmail::where('token', $token);
      $check = $verifyEmail->count() === 1;

      if (!$check) abort(403);

      $user = User::find($verifyEmail->first()->user_id);

      if ($check && $user->email_verified_at === null) {
         $verifyEmail = $verifyEmail->first();

         $user->email_verified_at = Carbon::now();
         $user->save();

         $verifyEmail->update([
            'status' => 'activated'
         ]);

         session()->flash('message_success', 'Email verified successfully!');

         return redirect('/');

         //
      } else abort(404);

      //
   }

   public function resend(Request $req)
   {
      if (auth()->user()->canResendEmailVerification()) {
         $verifyEmailToken = md5(auth()->user()->id . time());
         $verifyEmailToken = str_replace('/', '', $verifyEmailToken);

         VerifyEmail::create([
            'user_id' => auth()->user()->id,
            'token' => $verifyEmailToken,
            'status' => 'pending'
         ]);

         Mail::to(auth()->user()->email)->send(new VerifyEmailMail(auth()->user()->name, $verifyEmailToken));

         session()->flash('message_success', 'Verification email resent!');

         return redirect('/');
         //
      } else abort(403);


      //
   }

   //
}
