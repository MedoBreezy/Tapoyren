<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CourseExamQuestion extends Model
{
   use SoftDeletes;

   protected $guarded = ['id'];
   protected $hidden = ['created_at', 'updated_at'];

   public function answers()
   {
      return $this->hasMany('App\CourseExamAnswer', 'course_exam_question_id');
   }

   //
}
