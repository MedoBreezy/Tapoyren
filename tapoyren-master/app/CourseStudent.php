<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CourseStudent extends Model
{
   use SoftDeletes;

   protected $guarded = ['id'];
   protected $hidden = ['created_at', 'updated_at'];

   public function scopeIsActive($query)
   {
      return $query->where('status', 'active');
   }

   public function scopeIsDeactive($query)
   {
      return $query->where('status', 'deactive');
   }

   //
}
