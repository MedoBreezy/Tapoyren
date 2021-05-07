@if(auth()->check() && auth()->user()->canResendEmailVerification())
<div style="background: #080808; color: white; text-align: center; padding: 10px;">
   @tr('email_not_verified') <a style="color: dodgerblue;" href="{{ url('email/verification/resend') }}">@tr('email_clicking_here')</a>
</div>
@endif