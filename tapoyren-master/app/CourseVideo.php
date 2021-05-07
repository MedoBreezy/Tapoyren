<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CourseVideo extends Model
{
   use SoftDeletes;

   protected $guarded = ['id'];
   protected $hidden = ['created_at', 'updated_at'];

   public function resources(){
      return $this->hasMany('App\CourseVideoResource');
   }

   public function section()
   {
      return $this->belongsTo('App\CourseSection');
   }

   public function timescaleView()
   {
      $duration = $this->timescale;
      $hours = number_format($duration / 3600, 2);

      return "{$hours} " . trans('main.hour');
   }

   public function timescaleViewHourly()
   {
      $duration = $this->timescale;
      $hours = number_format($duration / 3600, 2);

      $hours = (int) ($duration / 3600);
      $minutes = (int) (($duration - ($hours * 3600)) / 60);

      if ($hours < 10) $hours = "0" . $hours;
      if ($minutes < 10) $minutes = "0" . $minutes;

      return "$hours:$minutes";
   }

   //
}
