<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CourseExamData extends Model
{
   use SoftDeletes;

   protected $guarded = ['id'];
   protected $hidden = ['created_at', 'updated_at'];

   public function answers()
   {
      return $this->hasMany('App\CourseExamDataAnswer', 'course_exam_data_id');
   }

   //
}
