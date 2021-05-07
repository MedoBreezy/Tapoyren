<?php

namespace App\Http\Controllers;

use App\Coupon;
use Illuminate\Http\Request;

class CouponController extends Controller
{
    public function add(Request $request)
    {
        $request->validate([
            'start' => 'required|string',
            'count' => 'required|numeric|min:0',
            'discount' => 'required|numeric|min:1|max:100',
        ]);

        $coupons = [];
        $count = (int)$request->input('count');

        $startRand = rand(100, 500);

        for ($i = $startRand; $i < $startRand + $count; $i++) {
            $name = $request->input('start') . $i;
            Coupon::create([
                'code' => $name,
                'discount' => $request->discount
            ]);
            array_push($coupons, [
                'code' => $name,
                'discount' => $request->discount
            ]);
        }

        return view('admin/coupon/creation')->with([
            'coupons' => $coupons
        ]);


        //
    }
    //
}
