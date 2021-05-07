<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Package extends Model
{
   use SoftDeletes;

   protected $guarded = ['id'];
   protected $hidden = ['created_at', 'updated_at'];

   public function courses()
   {
      return $this->hasMany('App\PackageCourse');
   }

   public function payments()
   {
      return $this->hasMany('App\PackagePayment');
   }

   public function subscribers()
   {
      return $this->hasMany('App\PackageSubscriber');
   }

   public function isNew()
   {
      $now = Carbon::now();
      return ($this->created_at->diff($now)->days < 15);
   }

   public function notSubscribed()
   {
      if (auth()->check()) return PackageSubscriber::where('package_id', $this->id)->where('student_id', auth()->user()->id)->where('status', 'active')->count() === 0;
      else return true;
   }

   //
}
