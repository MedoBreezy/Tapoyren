<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FaqQuestion extends Model
{
   use SoftDeletes;

   protected $guarded = ['id'];
   protected $hidden = ['created_at', 'updated_at'];

   public function translations()
   {
      return $this->hasMany('App\FaqQuestionTranslation', 'faq_question_id');
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
