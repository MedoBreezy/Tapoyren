<?php

namespace App\Http\Controllers;

use App\Category;
use App\Course;
use App\CoursePayment;
use App\CourseStudent;
use App\Language;
use App\Mail\ResetPasswordMail;
use App\Mail\StartCourseMail;
use App\Mail\StudentEnrolled;
use App\Mail\VerifyEmailMail;
use App\Package;
use App\PackagePayment;
use App\PackageSubscriber;
use App\Payment\Payment;
use App\ResetPasswordToken;
use App\User;
use App\VerifyEmail;
use Carbon\Carbon;
use Illuminate\Foundation\Console\Presets\React;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class MainController extends Controller
{

    public function home(Request $request)
    {
        $byLang = "Azərbaycan";

        $currentLocale = app()->getLocale();
        if ($currentLocale === "en") $byLang = "English";
        elseif ($currentLocale === "ru") $byLang = "Русский";

        $firstRows = Course::where('language', $byLang)->isActive()->inRandomOrder()->take(15)->get();

        if ($firstRows->count() < 15) {
            $diff = 15 - $firstRows->count();
            $secondRows = Course::where('language', '!=', $byLang)->isActive()->inRandomOrder()->take($diff)->get();
            $firstRows = $firstRows->merge($secondRows);
        }

        return view('pages.home')->with([
            'home_courses' => $firstRows
        ]);

        //
    }


    public function search(Request $req)
    {
        $search_query = $req->input('query');
        $query = "%$search_query%";

        $courses = Course::where('title', 'LIKE', $query)->where('status', 'active');

        $courseLanguages = Course::where('title', 'LIKE', $query)->where('status', 'active')->get()->map(function ($course) {
            return $course->language;
        })->toArray();
        $courseLanguages = array_unique($courseLanguages);

        $packages = Package::where('name', 'LIKE', $query)->where('status', 'active')->take(4)->get();
        if ($req->input('category')) $courses->where('category_id', $req->input('category'));
        if ($req->input('difficulty')) $courses->where('difficulty', $req->input('difficulty'));
        if ($req->input('language')) $courses->where('language', $req->input('language'));

        $instructors = User::where('type', 'instructor')->where('name', 'LIKE', $query)->get();

        return view('pages.search')->with([
            'search_query' => $search_query,
            'courses' => $courses->paginate(10),
            'courseLanguages' => $courseLanguages,
            'packages' => $packages,
            'instructors' => $instructors
        ]);

        //
    }

    public function view_category(Request $req, Category $category)
    {
        $courses = null;
        if ($category->parent_id !== null) $courses = $category->courses()->where('status', 'active')->paginate(10);
        else {
            $cats = Category::where('parent_id', $category->id)->get()->map(function ($cat) {
                return $cat->id;
            });

            $courses = Course::whereIn('category_id', $cats)->isActive()->paginate(10);
        }
        return view('pages.category')->withCategory($category)->withCourses($courses);
    }

    public function update_profile(Request $req)
    {
        $req->validate([
            'name' => 'required|string',
            'email' => 'required|email',
            'gender' => 'required|string',
            'birthDate' => 'nullable|numeric|min:4',
            'registration_number' => 'nullable|numeric',
            'employment' => 'nullable|in:1,2,3,4',
            'avatar' => 'nullable|image|mimes:jpg,jpeg,png'
        ]);

        $oldEmail = auth()->user()->email;

        if ($req->email !== $oldEmail) {
            if (User::where('email', $req->email)->count() === 1) return response()->json(['errors' => ['email' => 'Email has already been taken.']], 401);
        }

        $avatar = null;
        if ($req->hasFile('avatar')) {
            $file = $req->file('avatar');

            $path = Storage::putFile('public/avatars', $file, 'public');


            $quality = 60;

            $imagePath = storage_path('app/' . $path);

            $img = \Intervention\Image\Facades\Image::make($imagePath);
            $img->save($imagePath, $quality);

            $path = str_replace('public/', '', $path);

            $path = url('uploads/' . $path);

            $avatar = $path;
        }

        $user = User::find(auth()->user()->id);
        if ($avatar !== null) $user->avatar_url = $avatar;
        $user->name = $req->name;
        $user->email = $req->email;
        // $user->password = bcrypt($req->password);
        $user->gender = $req->gender;
        if ($req->birthDate) $user->birthDate = $req->birthDate . "-01-01";
        $user->registration_number = $req->registration_number;
        $user->employment_status = $req->employment_status;
        $user->save();

        if ($req->email !== $oldEmail) {
            $randomString = Str::random(60);
            $verifyEmailToken = bcrypt($user->id . $randomString);
            $verifyEmailToken = str_replace('/', '', $verifyEmailToken);

            VerifyEmail::create([
                'user_id' => $user->id,
                'token' => $verifyEmailToken,
                'status' => 'pending'
            ]);

            Mail::to($user->email)->send(new VerifyEmailMail($user->name, $verifyEmailToken));

            session()->flash('message_success', 'Registration completed! Please verify your account with the link sent to your email!');

            // Auth::login($user);
        }

        session()->flash('message_success', 'Profile updated!');

        return redirect('/');
        //
    }


    public function forgot_password(Request $req)
    {

        $req->validate([
            'email' => 'required|email'
        ]);

        $check = User::where('email', $req->email);

        if ($check->count() === 1) {
            $user = $check->first();

            $token = md5(time() . $user->id);

            ResetPasswordToken::create([
                'user_id' => $user->id,
                'token' => $token
            ]);

            Mail::to($user->email)->send(new ResetPasswordMail($user, $token));

            session()->flash('message_success', 'Reset Password Email sent to your email address!');

            return redirect('/');



            //
        } else return redirect()->back()->withErrors(['email' => 'User registered with this email is not found!']);



        //
    }

    public function show_reset_password(Request $req, $token)
    {
        $check = ResetPasswordToken::where('token', $token)->where('status', 'pending')->count() === 1;

        if ($check) {

            return view('auth.reset_password')->with([
                'token' => $token,
            ]);

            //
        } else abort(404);
    }

    public function reset_password(Request $req, $token)
    {
        $req->validate([
            'password' => 'required|confirmed|min:6'
        ]);

        $check = ResetPasswordToken::where('token', $token)->where('status', 'pending');

        if ($check->count() === 1) {
            $data = $check->first();
            $user = User::find($data->user_id);

            $user->update([
                'password' => bcrypt($req->password)
            ]);

            $data->update([
                'status' => 'expired'
            ]);

            session()->flash('message_success', 'Your password is changed successfully!');

            return redirect('/login');

            //
        } else abort(404);

        //
    }

    public function setLocale(Request $req, $locale)
    {
        $locales = Language::all()->map(function ($lang) {
            return $lang->slug;
        })->toArray();

        if (in_array($locale, $locales)) {
            Cookie::queue('LOCALE', $locale, time() + (10 * 365 * 24 * 60 * 60));
            app()->setLocale($locale);

            return redirect()->back();
        } else abort(404);
    }

    public function payment_complete(Request $req)
    {
        $trans_id = $req->input('trans_id');
        if (!isset($trans_id) || empty($trans_id) && !is_string($trans_id)) return;

        $paymentDetails = Payment::getPaymentDetails($trans_id);

        if ($paymentDetails['paymentDetails']['status'] === 'completed') {
            // COMPLETED

            $encodedTransId = urlencode($trans_id);

            $paymentFor = null;
            if (CoursePayment::where('transaction_id', $encodedTransId)->where('status', 'pending')->count() === 1) $paymentFor = 'course';
            elseif (PackagePayment::where('transaction_id', $encodedTransId)->where('status', 'pending')->count() === 1) $paymentFor = 'package';
            else abort(403, 'Payment information not found!');

            if ($paymentFor === 'course') {
                $coursePayment = CoursePayment::where('transaction_id', $encodedTransId)->where('status', 'pending')->firstOrFail();

                $coursePayment->update([
                    'status' => 'completed',
                ]);

                $course = Course::find($coursePayment->course_id);

                $firstSection = $course->sections->first();
                $firstLesson = $firstSection->videos->first();

                $lastPaymentDate = [
                    'monthly' => Carbon::now()->addMonth(),
                    'quarterly' => Carbon::now()->addMonths(3),
                    'semi_annually' => Carbon::now()->addMonths(6),
                    'annually' => Carbon::now()->addYear(),
                ];

                $checkStudent = CourseStudent::where('course_id', $course->id)->where('student_id', $coursePayment->student_id);
                $lastLessonId = $checkStudent->exists() ? $checkStudent->first()->last_lesson_id : $firstLesson->id;

                CourseStudent::create([
                    'course_id' => $course->id,
                    'student_id' => $coursePayment->student_id,
                    'subscription' => $coursePayment->subscription_type,
                    // 'price' => $course->{'price_' . $coursePayment->subscription_type},
                    'price' => $coursePayment->price,
                    'last_lesson_id' => $lastLessonId,
                    'last_payment_date' => Carbon::now(),
                    'next_payment_date' => $lastPaymentDate[$coursePayment->subscription_type]
                ]);

                $student = User::find($coursePayment->student_id);

                Mail::to($student->email)->send(new StartCourseMail($course));
                Mail::to($course->instructor->email)->send(new StudentEnrolled($course, $student));

                return redirect('/course/' . $course->id);
            } elseif ($paymentFor === 'package') {
                $packagePayment = PackagePayment::where('transaction_id', $encodedTransId)->where('status', 'pending')->firstOrFail();

                $student = User::find($packagePayment->student_id);

                $packagePayment->update([
                    'status' => 'completed',
                ]);

                $package = Package::find($packagePayment->package_id);

                $lastPaymentDate = [
                    'monthly' => Carbon::now()->addMonth(),
                    'quarterly' => Carbon::now()->addMonths(3),
                    'semi_annually' => Carbon::now()->addMonths(6),
                    'annually' => Carbon::now()->addYear(),
                ];

                $checkSubscriber = PackageSubscriber::where('package_id', $package->id)->where('student_id', $packagePayment->student_id);

                if ($checkSubscriber->count() === 1) $checkSubscriber->first()->update([
                    'status' => 'active',
                    'subscription' => $packagePayment->subscription_type,
                    'last_payment_date' => Carbon::now(),
                    'next_payment_date' => $lastPaymentDate[$packagePayment->subscription_type]
                ]);
                else PackageSubscriber::create([
                    'package_id' => $package->id,
                    'student_id' => $packagePayment->student_id,
                    'subscription' => $packagePayment->subscription_type,
                    'last_payment_date' => Carbon::now(),
                    'next_payment_date' => $lastPaymentDate[$packagePayment->subscription_type]
                ]);

                foreach ($package->courses as $course) {
                    $course = $course->course();
                    $firstLesson = $course->sections->first()->videos()->first();

                    $data = [
                        'subscription' => $packagePayment->subscription_type,
                        'last_payment_date' => Carbon::now(),
                        'last_lesson_id' => $firstLesson->id,
                        'next_payment_date' => $lastPaymentDate[$packagePayment->subscription_type]
                    ];
                    $course->createPackageStudent($package, $student, $data['subscription'], $data['last_lesson_id'], $data['last_payment_date'], $data['next_payment_date']);
                }


                return redirect('/package/' . $package->id);
            }

            //
        } else {
            abort(403, 'Payment error: ' . $paymentDetails['paymentDetails']['status']);
            // HANDLE THE ERROR
        }
    }

    public function view_instructor(Request $request, User $instructor)
    {
        if ($instructor->type !== 'instructor') abort(403);

        else return view('pages.instructor')->with([
            'instructor' => $instructor
        ]);
    }

    public function translations_api(Request $request)
    {
        $locale = app()->getLocale();
        $lang = Language::where('slug', $locale)->first();

        $translationData = [];

        $translations = $lang->translations->each(function ($translation) use (&$translationData) {
            $translationData[$translation->key] = $translation->value;
        });

        return response()->json(['translations' => $translationData]);
    }

    //
}
