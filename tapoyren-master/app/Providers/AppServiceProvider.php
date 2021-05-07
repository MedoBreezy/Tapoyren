<?php

namespace App\Providers;

use App\Language;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\ServiceProvider;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Log;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        if (app()->environment() === 'production') {

            $locales = Language::all()->map(function ($lang) {
                return $lang->slug;
            })->toArray();

            $locale = null;
            if (request()->cookie('LOCALE')) $locale = Crypt::decrypt(request()->cookie('LOCALE'), false);

            if (isset($locale) && !empty($locale) && in_array($locale, $locales)) app()->setLocale($locale);
            else {
                $newLoc = "en";
                Cookie::queue('LOCALE', $newLoc, time() + (10 * 365 * 24 * 60 * 60));
                app()->setLocale($newLoc);
                // $guzzle = new Client();

                // $ip = request()->ip();
                // if (isset($_SERVER["HTTP_CF_CONNECTING_IP"])) $ip = $_SERVER["HTTP_CF_CONNECTING_IP"];

                // $response = $guzzle->request('GET', "http://api.ipstack.com/{$ip}?access_key=" . env('IPSTACK_TOKEN'));

                // if($response->getStatusCode()===200){
                //     $response = (string) $response->getBody();

                //     $response = json_decode($response);

                //     $cc = $response->country_code;

                //     $newLoc = null;

                //     if ($cc === 'AZ') $newLoc = "az";
                //     elseif ($cc === 'RU' || $cc === 'UA' || $cc === 'KZ') $newLoc = "ru";
                //     else $newLoc = "en";

                //     Cookie::queue('LOCALE', $newLoc, time() + (10 * 365 * 24 * 60 * 60));
                //     app()->setLocale($newLoc);
                // }
                // else {
                //     Cookie::queue('LOCALE', "az", time() + (10 * 365 * 24 * 60 * 60));
                //     app()->setLocale("az");
                // }


            }
        }

        //
    }
}
