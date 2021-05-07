<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CourseDiscussion extends Model
{
   use SoftDeletes;

   protected $guarded = ['id'];
   protected $hidden = ['created_at', 'updated_at'];

   public function messages()
   {
      return $this->hasMany('App\CourseDiscussionMessage', 'course_discussion_id');
   }

   //
}
