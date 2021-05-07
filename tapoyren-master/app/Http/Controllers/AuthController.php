<?php

namespace App\Http\Controllers;

use App\Course;
use App\CourseStudent;
use App\Mail\VerifyEmailMail;
use App\User;
use App\VerifyEmail;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class AuthController extends Controller
{

    public function login(Request $req)
    {
        $req->validate([
            'email' => 'required|email',
            'password' => 'required|min:6'
        ]);

        $email = $req->email;
        $password = $req->password;

        $user = User::where('email', $email);

        if ($user->count() === 1) {
            $user = $user->first();

            $checkPassword = Hash::check($password, $user->password);

            if ($checkPassword) {
                $rememberMe = $req->rememberMe === 'on';
                Auth::login($user, $rememberMe);

                $session = session()->get('return_url');
                if ($session) {
                    session()->remove('return_url');
                    return redirect($session);
                } else return redirect('/');

                //
            } else return redirect()->back()->withErrors(['credentials' => 'Invalid credentials']);

            //
        } else return redirect()->back()->withErrors(['credentials' => 'Invalid credentials']);

        //
    }

    public function register_student(Request $req)
    {
        $req->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:users',
            'password' => 'required|confirmed|min:6',
            'gender' => 'required|string|in:male,female',
            'birthDate' => 'required|numeric|min:4',
        ]);

        $agreeToTerms = $req->agreeToTerms === 'on';

        if (!$agreeToTerms) return redirect()->back()->withErrors(['terms' => 'You must agree to terms and conditions to register!']);

        $user = new User;
        $user->type = 'student';
        $user->name = $req->name;
        $user->email = $req->email;
        $user->password = bcrypt($req->password);
        $user->gender = $req->gender;
        $user->birthDate = $req->birthDate . "-01-01";
        $user->api_token = Str::random(80);
        $user->save();

        $verifyEmailToken = md5($user->id . time());
        $verifyEmailToken = str_replace('/', '', $verifyEmailToken);

        VerifyEmail::create([
            'user_id' => $user->id,
            'token' => $verifyEmailToken,
            'status' => 'pending'
        ]);

        Mail::to($user->email)->send(new VerifyEmailMail($user->name, $verifyEmailToken));

        session()->flash('message_success', 'Registration completed! Please verify your account with the link sent to your email!');

        Auth::login($user);

        $session = session()->get('return_url');
        if ($session) {
            session()->remove('return_url');
            return redirect($session);
        } else return redirect('/');

        //
    }

    public function register_instructor(Request $req)
    {
        $req->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:users',
            'password' => 'required|confirmed',
            'gender' => 'required|string',
            'birthDate' => 'required',
        ]);

        $user = new User;
        $user->type = 'instructor';
        $user->name = $req->name;
        $user->email = $req->email;
        $user->password = bcrypt($req->password);
        $user->gender = $req->gender;
        $user->birthDate = $req->birthDate;
        $user->api_token = Str::random(80);
        if ($req->registration_number) $user->registration_number = $req->registration_number;
        if ($req->employment_status) $user->employment_status = $req->employment_status;
        $user->save();

        $randomString = Str::random(60);
        $verifyEmailToken = bcrypt($user->id . $randomString);
        $verifyEmailToken = str_replace('/', '', $verifyEmailToken);

        VerifyEmail::create([
            'user_id' => $user->id,
            'token' => $verifyEmailToken,
            'status' => 'pending'
        ]);

        Mail::to($req->email)->send(new VerifyEmailMail($req->name, $verifyEmailToken));

        session()->flash('message_success', 'Registration completed! Please verify your account with the link sent to your email!');

        Auth::login($user);

        return redirect('/');

        //
    }

    public function register_from_greencard(Request $req)
    {
        $req->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
            'gender' => 'required|string|in:male,female',
            'birthDate' => 'required|numeric|min:4',
        ]);

        $user = new User;
        $user->type = 'student';
        $user->name = $req->name;
        $user->email = $req->email;
        $user->password = bcrypt($req->password);
        $user->gender = $req->gender;
        $user->birthDate = $req->birthDate . "-01-01";
        $user->api_token = Str::random(80);
        $user->save();

        $verifyEmailToken = md5($user->id . time());
        $verifyEmailToken = str_replace('/', '', $verifyEmailToken);

        VerifyEmail::create([
            'user_id' => $user->id,
            'token' => $verifyEmailToken,
            'status' => 'pending'
        ]);

        Mail::to($user->email)->send(new VerifyEmailMail($user->name, $verifyEmailToken));

        session()->flash('message_success', 'Registration completed! Please verify your account with the link sent to your email!');

        $course = Course::find(66);

        $lastLessonId = $course->sections->first()->videos->first()->id;

        CourseStudent::create([
            'course_id' => $course->id,
            'student_id' => $user->id,
            'last_lesson_id' => $lastLessonId,
            'subscription' => 'annually',
            'is_in_trial' => false,
            'price' => 0,
            'last_payment_date' => Carbon::now(),
            'next_payment_date' => Carbon::now()->addYear()
        ]);

        return response()->json(['code' => 'done']);

        //
    }

    public function logout(Request $req)
    {
        Auth::logout();

        return redirect('/');
    }

    //
}
