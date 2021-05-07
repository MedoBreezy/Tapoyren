<?php

namespace App\Http\Middleware;

use App\IPLog as AppIPLog;
use Carbon\Carbon;
use Closure;
use Illuminate\Support\Facades\Auth;

class IPLog
{
   /**
    * Handle an incoming request.
    *
    * @param  \Illuminate\Http\Request  $request
    * @param  \Closure  $next
    * @return mixed
    */
   public function handle($request, Closure $next)
   {
      if (auth()->check()) {
         $currentIp = $request->ip();
         $currentSessionId = $request->cookie('tapoyren_session');

         AppIPLog::create([
            'user_id' => auth()->user()->id,
            'ip' => $currentIp,
            'session_id' => $currentSessionId,
            'path' => request()->fullUrl(),
            'time' => time()
         ]);

         $check = true;
         $records = AppIPLog::where('user_id', auth()->user()->id)->where('ip', '!=', $currentIp);

         if ($records->count() > 0) {
            $last = $records->get()->last();
            $lastTime = (int) ($last->time);
            if (time() - $lastTime < 20) $check = false;
         }

         $twoDaysAgo = Carbon::now()->subDays(2);
         AppIPLog::whereDate('created_at', '>', $twoDaysAgo)->forceDelete();

         if (!$check) {
            session()->flash('message_warning', 'Eyni anda birdən artıq cihazdan giriş etmək qadağandır! Davam etmək üçün digər cihazdan çıxış edin');
            return redirect('/');
         }
      }
      return $next($request);
   }
}
