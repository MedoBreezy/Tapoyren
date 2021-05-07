<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model
{
   use SoftDeletes;

   protected $guarded = ['id'];
   protected $hidden = ['created_at', 'updated_at'];

   public function sub_categories()
   {
      return $this->hasMany('App\Category', 'parent_id', 'id');
   }

   public function courses()
   {
      return $this->hasMany('App\Course', 'category_id');
   }

   public function translations()
   {
      return $this->hasMany('App\CategoryTranslation');
   }

   public function __($key)
   {
      $locale = app()->getLocale();
      $lang = Language::where('slug', $locale)->first();
      $translation = $this->translations->where('language_id', $lang->id)->first();
      if ($translation) return $translation->{$key};
      else return null;
   }

   //
}
