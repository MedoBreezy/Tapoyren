<?php

namespace App\Http\Controllers;

use App\Coupon;
use App\Course;
use App\CourseStudent;
use App\Package;
use App\PackageCourse;
use App\PackagePayment;
use App\PackageSubscriber;
use App\Payment\Payment;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PackageController extends Controller
{

    public function add(Request $req)
    {

        $req->validate([
            'name' => 'required|string',
            'description' => 'required|string',
            'courses' => 'required|array',
            'price_monthly' => 'required|numeric',
            'price_quarterly' => 'required|numeric',
            'price_semiannually' => 'required|numeric',
            'price_annually' => 'required|numeric',
            'thumbnail' => 'required|image|mimes:jpg,jpeg,png'
        ]);

        $file = $req->file('thumbnail');

        $thumbnailPath = Storage::putFile('public/images/packages', $file, 'public');
        $thumbnailPath = str_replace('public/', '', $thumbnailPath);
        $thumbnailPath = url('uploads/' . $thumbnailPath);

        $package = Package::create([
            'name' => $req->name,
            'description' => $req->description,
            'price_monthly' => $req->price_monthly,
            'price_quarterly' => $req->price_quarterly,
            'price_semiannually' => $req->price_semiannually,
            'price_annually' => $req->price_annually,
            'thumbnail_url' => $thumbnailPath,
            'status' => 'deactive'
        ]);

        foreach ($req->courses as $course) $package->courses()->create([
            'course_id' => $course
        ]);

        return redirect('admin/packages');

        //
    }

    public function publish(Request $req, Package $package)
    {
        $package->update(['status' => 'active']);

        return redirect('admin/packages');
    }

    public function draft(Request $req, Package $package)
    {
        $package->update(['status' => 'deactive']);

        return redirect('admin/packages');
    }

    public function view_package(Request $req, Package $package)
    {

        $courses = $package->courses->map(function ($course) {
            return $course->course();
        });

        return view('pages.package.index')->with([
            'package' => $package,
            'courses' => $courses
        ]);
    }

    public function view_subscribe(Request $req, Package $package)
    {
        return view('pages.package.subscribe')->withPackage($package);
    }

    public function subscribe(Request $req, Package $package, $type)
    {
        if (!auth()->check()) {
            $return_url = "package/{$package->id}/subscribe";
            session()->put('return_url', $return_url);
            return redirect('/login');
        } elseif (auth()->check() && !auth()->user()->verified()) {
            session()->flash('message_warning', 'Please verify your account!');
            return redirect('/');
        }

        $checkStudent = PackageSubscriber::where('package_id', $package->id)->where('student_id', auth()->user()->id)->where('status', 'active')->count() === 0;
        if (!$checkStudent) abort(403, 'You are already subscribed to this package!');

        $_type = $type;
        if ($_type === 'semi_annually') $_type = 'semiannually';

        $priceForBank = $package->{'price_' . $_type} * 100;

        $coupon = null;
        if ($req->input('coupon')) {
            $checkCoupon = Coupon::where('code', $req->input('coupon'))->first();

            if ($checkCoupon && !$checkCoupon->expired) {
                $coupon = $checkCoupon->id;
                $discount = $checkCoupon->discount;

                $discounted = $priceForBank * $discount / 100;
                $priceForBank = (int)($priceForBank - $discounted);
                $checkCoupon->update(['expired' => true]);
            }
        }

        $payment = Payment::redirectToPayment($priceForBank, "Package Payment");
        if (count($payment['errors']) > 0) {
            abort(500, $payment[0]);
        } else {

            PackagePayment::create([
                'package_id' => $package->id,
                'student_id' => auth()->user()->id,
                'subscription_type' => $type,
                'transaction_id' => $payment['trans_id'],
                // 'price' => $package->{'price_' . $_type}
                'price' => $coupon ? number_format($priceForBank / 100, 2) : $package->{'price_' . $_type}
            ]);

            return redirect()->to($payment['client_url']);
        }

        //
    }

    public function view_edit(Request $req, Package $package)
    {
        $courseIds = $package->courses->map(function ($packageCourse) {
            return $packageCourse->course_id;
        });
        return view('admin.packages.edit')->with([
            'package' => $package,
            'courseIds' => $courseIds->toArray()
        ]);
    }

    public function update(Request $req, Package $package)
    {
        $req->validate([
            'name' => 'required|string',
            'description' => 'required|string',
            'courses' => 'required|array',
            'price_monthly' => 'required|numeric',
            'price_quarterly' => 'required|numeric',
            'price_semiannually' => 'required|numeric',
            'price_annually' => 'required|numeric',
            'thumbnail' => 'image|nullable|mimes:jpg,jpeg,png'
        ]);


        if ($req->hasFile('thumbnail')) {
            $file = $req->file('thumbnail');
            $thumbnailPath = Storage::putFile('public/images/packages', $file, 'public');
            $thumbnailPath = str_replace('public/', '', $thumbnailPath);
            $thumbnailPath = url('uploads/' . $thumbnailPath);
            $package->update(['thumbnail_url' => $thumbnailPath]);
        }


        $package->update([
            'name' => $req->name,
            'description' => $req->description,
            'price_monthly' => $req->price_monthly,
            'price_quarterly' => $req->price_quarterly,
            'price_semiannually' => $req->price_semiannually,
            'price_annually' => $req->price_annually,
        ]);

        $oldCourseIds = $package->courses->map(function ($packageCourse) {
            return $packageCourse->course_id;
        })->toArray();
        $newCourseIds = collect($req->courses)->map(function ($newCourseId) {
            return (int) ($newCourseId);
        })->toArray();

        $removedCourseIds = [];
        $addedCourseIds = [];

        foreach ($newCourseIds as $newCourseId) {
            if (!in_array($newCourseId, $oldCourseIds)) array_push($addedCourseIds, $newCourseId);
        }
        foreach ($oldCourseIds as $oldCourseId) {
            if (!in_array($oldCourseId, $newCourseIds)) array_push($removedCourseIds, $oldCourseId);
        }

        // REMOVE STUDENTS FROM REMOVED COURSES
        foreach ($removedCourseIds as $removedCourseId) {
            CourseStudent::where('by_package_id', $package->id)
                ->where('course_id', $removedCourseId)->delete();
            // $package->courses->where('id',$removedCourseId)->delete();
            PackageCourse::where('package_id', $package->id)->where('course_id', $removedCourseId)->delete();
        }

        // ADD STUDENTS TO ADDED COURSES
        foreach ($addedCourseIds as $addedCourseId) {
            $addedCourse = Course::find($addedCourseId);
            $firstLessonId = $addedCourse->sections->first()->videos->first()->id;

            foreach ($package->subscribers as $subscriber) {
                $student = User::find($subscriber->student_id);
                $addedCourse->createPackageStudent(
                    $package,
                    $student,
                    $subscriber->subscription,
                    $firstLessonId,
                    $subscriber->last_payment_date,
                    $subscriber->next_payment_date
                );
            }

            $package->courses()->create(['course_id' => $addedCourseId]);
        }

        session()->flash('message_success', 'Package Updated!');
        return redirect('admin/packages');
    }

    //
}
