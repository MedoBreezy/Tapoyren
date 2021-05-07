<?php

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Response;

Route::view('yandex_2d9caa8675af27e6.html', 'yandex_verification');

Route::get('/', 'MainController@home')->middleware('ip_log');
Route::view('/about', 'pages.about');
Route::view('/contact', 'pages.contact');
Route::get('/instructor/{instructor}', 'MainController@view_instructor')->where('instructor', '[0-9]+');

Route::post('/payment/handle', 'MainController@payment_complete');

Route::get('locale/{locale}', 'MainController@setLocale');

Route::get('warning/disable_extension', function () {
    session()->flash('message_warning', 'Please disable download managers then refresh the page!');
    return redirect('/');
});

Route::middleware('auth')->prefix('student')->group(function () {

    Route::get('dashboard', 'StudentController@view_dashboard');

    //
});


Route::middleware(['auth', 'company'])->prefix('company')->group(function () {

    Route::get('dashboard', 'CompanyController@view_dashboard');

    //
});


Route::middleware(['auth', 'instructor'])->prefix('instructor')->group(function () {

    Route::get('dashboard', 'InstructorController@view_dashboard');

    //
});

Route::get('/email_not_verified', function () {
    session()->flash('message_warning', 'Your email is not verified!');
    return redirect()->back();
})->name('verification.notice');

Route::get('/search', 'MainController@search');
Route::view('/browse', 'pages.browse_courses');
Route::view('/faq', 'pages.faq');
Route::get('/category/{category}', 'MainController@view_category');

Route::view('/terms_and_conditions', 'pages.terms_and_conditions');


Route::prefix('package')->middleware('active_package')->group(function () {

    Route::get('{package}', 'PackageController@view_package');
    Route::get('{package}/subscribe', 'PackageController@view_subscribe');
    Route::get('{package}/subscribe/{type}', 'PackageController@subscribe');
});

Route::prefix('course')->middleware(['ip_log', 'active_course'])->group(function () {

    Route::get('{course}', 'CourseController@view_course');
    Route::get('{course}/discussions', 'CourseController@view_discussions')->middleware(['auth', 'verified']);
    Route::get('{course}/discussions/ask', 'CourseController@view_discussions_ask')->middleware(['auth', 'verified']);
    Route::get('{course}/discussion/{discussion}', 'CourseController@view_discussion')->middleware(['auth', 'verified']);
    Route::post('{course}/discussion/{discussion}/comment', 'CourseController@discussion_comment')->middleware(['auth', 'verified']);
    Route::post('{course}/discussions/ask', 'CourseController@discussions_ask')->middleware(['auth', 'verified']);
    Route::get('{course}/favorite', 'CourseController@favorite_course')->middleware(['auth', 'verified']);
    Route::get('{course}/enroll', 'CourseController@view_enroll');
    Route::get('{course}/rating/{rating}', 'CourseController@give_rating')->middleware(['auth', 'verified', 'course_student']);

    Route::get('{course}/enroll/{type}', 'CourseController@paid_enrollment')
        ->where('type', 'monthly|quarterly|semi_annually|annually');

    Route::get('{course}/instructor/{instructor}/message', 'ConversationController@view_new_message')->middleware(['auth', 'verified', 'course_student']);
    Route::post('{course}/instructor/{instructor}/message', 'ConversationController@new_message')->middleware(['auth', 'verified', 'course_student']);

    Route::get('{course}/watch', 'CourseController@continue_watching')->middleware(['auth', 'verified']);
    Route::get('{course}/watch', 'CourseController@continue_watching')->middleware(['auth', 'verified']);
    Route::get('{course}/lesson/{lesson}', 'CourseController@take_lesson')->middleware(['auth', 'verified', 'course_student']);
    Route::get('{course}/exam/{exam}', 'CourseController@take_exam')->middleware(['auth', 'verified', 'course_student']);
    Route::get('{course}/preview/{video}', 'CourseController@preview_video');

    //
});

Route::get('instructor/{instructor}/conversation/{conversation}', 'ConversationController@view_conversation')->middleware(['auth', 'verified']);
Route::post('instructor/{instructor}/conversation/{conversation}/new_message', 'ConversationController@new_conversation_message')->middleware(['auth', 'verified']);


Route::prefix('email')->group(function () {

    Route::get('verify/{token}', 'EmailController@verify');
    Route::get('verification/resend', 'EmailController@resend')->middleware('auth');

    //
});

Route::middleware('guest')->group(function () {

    Route::view('login', 'auth.login')->name('login');
    Route::view('register/student', 'auth.login')->name('register');
    Route::view('register/instructor', 'auth.register_instructor')->name('register');
    Route::view('forgot_password', 'auth.forgot_password');
    Route::post('forgot_password', 'MainController@forgot_password');
    Route::get('reset_password/{token}', 'MainController@show_reset_password');
    Route::post('reset_password/{token}', 'MainController@reset_password');


    Route::post('login', 'AuthController@login');
    Route::post('register/student', 'AuthController@register_student');
    Route::post('register/instructor', 'AuthController@register_instructor');

    //
});

Route::get('logout', 'AuthController@logout');
Route::middleware('auth')->group(function () {


    Route::view('account/profile', 'pages.account.profile');
    Route::post('account/profile', 'MainController@update_profile');

    Route::view('account/wishlist', 'pages.account.wishlist');
    Route::view('account/courses', 'pages.account.courses');
    Route::get('account/payments', 'StudentController@view_payments');

    //
});

Route::prefix('user')->middleware(['auth'])->group(function () {

    Route::get('notification/mark_read', 'UserController@mark_all_notifications_as_read');
    Route::get('notification/{notification}/read', 'UserController@go_to_notification');


    //
});

Route::prefix('admin')->middleware('admin')->group(function () {

    Route::view('/', 'admin.dashboard.index');
    Route::view('/categories', 'admin.dashboard.categories');
    Route::view('/companies', 'admin.company.all');
    Route::view('/courses', 'admin.dashboard.courses');

    Route::view('/coupon/all', 'admin.coupon.all');
    Route::view('/coupon/add', 'admin.coupon.add');
    Route::post('/coupon/add', 'CouponController@add');

    Route::view('/languages', 'admin.languages.all');
    Route::view('/language/add', 'admin.languages.add');
    Route::post('/language/add', 'LanguageController@add_language');
    Route::get('/language/{language}/translations', 'LanguageController@view_add_translations');
    Route::get('/language/{language}/delete', 'LanguageController@delete_language');

    Route::view('packages', 'admin.packages.all');
    Route::view('package/add', 'admin.packages.add');
    Route::post('package/add', 'PackageController@add');
    Route::view('package/payments', 'admin.packages.payments');
    Route::get('package/{package}/edit', 'PackageController@view_edit');
    Route::post('package/{package}/update', 'PackageController@update');
    Route::get('package/{package}/publish', 'PackageController@publish');
    Route::get('package/{package}/draft', 'PackageController@draft');

    Route::view('/course/add_student', 'admin.course.add_student');
    Route::post('/course/add_student', 'CourseController@add_student_to_paid_course');

    Route::get('category/{category}/delete', 'CategoryController@delete');
    Route::get('course/{course}/delete', 'CourseController@delete');
    Route::get('course/{course}/publish', 'CourseController@publish');
    Route::get('course/{course}/draft', 'CourseController@draft');
    Route::get('course/{course}/exam/{exam}/publish', 'ExamController@publish');
    Route::get('course/{course}/exam/{exam}/draft', 'ExamController@draft');

    Route::view('exam/add', 'admin.exam.add');
    Route::get('course/{course}/exam/{exam}/edit', 'ExamController@admin_edit_exam');

    Route::view('course/add', 'admin.course.add');
    Route::get('course/{course}/resource/add', 'CourseResourceController@view_course');
    Route::get('course/{course}/lesson/{lesson}/resource/add', 'CourseResourceController@view_lesson_resource_add');
    Route::get('course/{course}/add_data', 'CourseController@view_add_data');
    Route::get('course/{course}/edit', 'CourseController@view_edit');
    Route::get('course/{course}/edit_data', 'CourseController@view_edit_data');

    Route::view('category/add', 'admin.category.add');
    Route::view('course/payments', 'admin.course.payments');
    Route::post('category/add', 'CategoryController@add');

    Route::view('company/add', 'admin.company.add');
    Route::post('company/add', 'CompanyController@add');
    Route::get('company/{company}/delete', 'CompanyController@delete');
    Route::get('company/{company}/enrollments', 'CompanyController@view_enrollments');
    Route::view('company/add_user', 'admin.company.add_user');
    Route::post('company/add_user', 'CompanyController@add_user');

    Route::view('instructor/add', 'admin.instructor.add');
    Route::view('instructors', 'admin.instructor.all');
    Route::post('instructor/add', 'InstructorController@add');
    Route::get('instructor/{instructor}/delete', 'InstructorController@delete');
    Route::get('instructor/{instructor}/update', 'InstructorController@view_update');

    Route::view('faq', 'admin.faq.all');
    Route::view('faq/add', 'admin.faq.add');
    Route::get('faq/{faq}/update', 'FaqController@view_update');
    Route::post('faq/add', 'FaqController@add');
    Route::post('faq/{faq}/update', 'FaqController@update');
    Route::get('faq/{faq}/delete', 'FaqController@delete');

    //
});

Route::get('uploads/{dir}/{filename}', function ($dir, $filename) {
    $path = storage_path("app/public/$dir/" . $filename);

    if (!File::exists($path)) {
        abort(404);
    }

    $file = File::get($path);
    $type = File::mimeType($path);

    $response = Response::make($file, 200);
    $response->header("Content-Type", $type);

    return $response;
});

Route::get('uploads/{dir}/{subDir}/{filename}', function ($dir, $subDir, $filename) {
    $path = storage_path("app/public/$dir/$subDir/" . $filename);

    if (!File::exists($path)) {
        abort(404);
    }

    $file = File::get($path);
    $type = File::mimeType($path);

    $response = Response::make($file, 200);
    $response->header("Content-Type", $type);

    return $response;
});
