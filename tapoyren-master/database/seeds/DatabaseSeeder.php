<?php

use App\Category;
use App\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
   /**
    * Seed the application's database.
    *
    * @return void
    */
   public function run()
   {
      User::create([
         'name' => config('app.name'),
         'email' => 'admin@tapoyren.com',
         'password' => bcrypt('secret@tapoyren--admin'),
         'email_verified_at' => \Carbon\Carbon::now(),
         'birthDate' => \Carbon\Carbon::now(),
         'type' => 'admin',
         'api_token' => Str::random(80)
      ]);

      User::create([
         'name' => 'Test Instructor',
         'email' => 'instructor@tapoyren.com',
         'password' => bcrypt('secret@tapoyren--admin'),
         'email_verified_at' => \Carbon\Carbon::now(),
         'birthDate' => \Carbon\Carbon::now(),
         'type' => 'instructor',
         'api_token' => Str::random(80)
      ]);

      // $this->call(UsersTableSeeder::class);
   }
}
