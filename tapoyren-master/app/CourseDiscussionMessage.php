<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CourseDiscussionMessage extends Model
{
   use SoftDeletes;

   protected $guarded = ['id'];
   protected $hidden = ['created_at', 'updated_at'];

   public function user()
   {
      return $this->belongsTo('App\User', 'user_id');
   }

   public function comments()
   {
      return $this->hasMany('App\CourseDiscussionMessage', 'parent_message_id');
   }

   //
}
