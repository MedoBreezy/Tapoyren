<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Company extends Model
{
   use SoftDeletes;

   protected $guarded = ['id'];
   protected $hidden = ['created_at', 'updated_at'];

   public function users()
   {
      return CompanyUser::where('company_id', $this->id)->get()->map(function ($companyUser) {
         return User::find($companyUser->user_id);
      });
   }

   public function owner(){
       return $this->belongsTo('App\User','owner_id');
   }

   //
}
