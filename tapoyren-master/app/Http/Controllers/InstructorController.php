<?php

namespace App\Http\Controllers;

use App\Course;
use App\CoursePayment;
use App\Package;
use App\PackageCourse;
use App\PackagePayment;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class InstructorController extends Controller
{
    public function list(Request $req)
    {
        $instructors = User::where('type', 'instructor')->get();
        return response()->json(['instructors' => $instructors]);
    }

    public function add(Request $req)
    {
        $req->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:users',
            'password' => 'required|confirmed|min:6',
            'gender' => 'required|in:male,female',
            'birthDate' => 'required|date',
        ]);

        $apiToken = bcrypt(Str::random() . User::count() . time());

        $instructor = User::create([
            'name' => $req->name,
            'email' => $req->email,
            'password' => bcrypt($req->password),
            'type' => 'instructor',
            'gender' => $req->gender,
            'birthDate' => $req->birthDate,
            'api_token' => $apiToken
        ]);

        return redirect('admin/instructors');
    }

    public function delete(Request $req, User $instructor)
    {
        if ($instructor->type === 'instructor') $instructor->delete();
        return redirect('admin/instructors');
    }

    public function view_update(Request $req, User $instructor)
    {
        return view('admin.instructor.update')->withInstructor($instructor);
    }

    public function instructor_info(Request $req, User $instructor)
    {
        return response()->json([
            'instructor' => [
                'avatar_url' => $instructor->avatar_url,
                'bio' => $instructor->bio,
            ]
        ]);
    }

    public function update(Request $req, User $instructor)
    {
        $validation = Validator::make($req->all(), [
            'avatar_url' => 'nullable|string',
            'bio' => 'nullable|string'
        ]);

        if ($validation->fails()) return response()->json(['errors' => $validation->errors()], 401);

        $instructor->update($req->only(['avatar_url', 'bio']));

        return response()->json(['code' => 'done']);
    }

    public function view_dashboard(Request $request)
    {
        $data = [];

        $courses = auth()->user()->courses;

        $data['courses'] = $courses;

        if ($request->input('course_id')) {

            $course_id = $request->input('course_id');
            $course = Course::find($course_id);

            if ($course->instructor_id !== auth()->user()->id) abort(403);

            $totalCoursePayment = 0;
            $totalPackagePayment = 0;

            $payments_monthly = $course->completedPayments()->where('subscription_type', 'monthly')->get();
            $payments_quarterly = $course->completedPayments()->where('subscription_type', 'quarterly')->get();
            $payments_semi_annually = $course->completedPayments()->where('subscription_type', 'semi_annually')->get();
            $payments_annually = $course->completedPayments()->where('subscription_type', 'annually')->get();

            $payments_monthly->each(function ($payment) use (&$totalCoursePayment) {
                $totalCoursePayment += $payment->price;
            });
            $payments_quarterly->each(function ($payment) use (&$totalCoursePayment) {
                $totalCoursePayment += $payment->price;
            });
            $payments_semi_annually->each(function ($payment) use (&$totalCoursePayment) {
                $totalCoursePayment += $payment->price;
            });
            $payments_annually->each(function ($payment) use (&$totalCoursePayment) {
                $totalCoursePayment += $payment->price;
            });

            // $packages = PackageCourse::where('course_id', $course->id)->get()->map(function ($packageCourse) {
            //     return Package::find($packageCourse->package_id);
            // })->unique();
            // $packageIds = $packages->map(function ($package) {
            //     return $package->id;
            // })->toArray();

            // $packagePayments = PackagePayment::whereIn('package_id', $packageIds)->where('status', 'completed')->get();

            // $packagePayments->each(function ($pc) use (&$totalPackagePayment) {

            //     //
            // });

            $ranges = [
                'last_month' => 'Last Month',
                'last_year' => 'Last Year',
            ];


            $totalPayment = $totalPackagePayment + $totalCoursePayment;

            $totalRevenue = $totalPayment * 25 / 100;
            $totalRevenue = number_format($totalRevenue, 2);


            $data['course'] = $course;
            $data['totalPayment'] = $totalPayment;
            $data['totalRevenue'] = $totalRevenue;
            $data['ranges'] = $ranges;

            //
        }

        return view('instructor.dashboard.index')->with($data);
    }

    //
}
