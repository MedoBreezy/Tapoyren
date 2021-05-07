<?php

namespace App\Http\Controllers;

use App\SiteConfig;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;

class SiteConfigController extends Controller
{
    public function update_features(Request $req){
        $req->validate([
            'feature_first_title' => 'required|string',
            'feature_first_description' => 'required|string',
            'feature_second_title' => 'required|string',
            'feature_second_description' => 'required|string',
            'feature_third_title' => 'required|string',
            'feature_third_description' => 'required|string',
        ]);

        SiteConfig::first()->update([
            'feature_first_title' => $req->feature_first_title,
            'feature_first_description' => $req->feature_first_description,
            'feature_second_title' => $req->feature_second_title,
            'feature_second_description' => $req->feature_second_description,
            'feature_third_title' => $req->feature_third_title,
            'feature_third_description' => $req->feature_third_description,
        ]);

        return redirect()->back();
    }
    //
}
