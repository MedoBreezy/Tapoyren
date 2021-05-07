<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CourseSection extends Model
{
   use SoftDeletes;

   protected $guarded = ['id'];
   protected $hidden = ['created_at', 'updated_at'];

   public function videos()
   {
      return $this->hasMany('App\CourseVideo', 'section_id', 'id');
   }

   public function timescaleView()
   {
      $duration = $this->timescale;
      $hours = number_format($duration / 3600, 2);

      return "{$hours} " . trans('main.hour');
   }

   //
}
