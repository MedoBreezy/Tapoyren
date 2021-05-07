<?php

namespace App\Http\Controllers;

use App\Coupon;
use App\Course;
use App\CourseDiscussion;
use App\CourseExam;
use App\CourseFreeTrial;
use App\CourseLearnList;
use App\CoursePayment;
use App\CourseRating;
use App\CourseResource;
use App\CourseSection;
use App\CourseStudent;
use App\CourseVideo;
use App\CourseVideoWatched;
use App\Mail\StartCourseMail;
use App\Mail\StudentEnrolled;
use App\Notification;
use App\Payment\Payment;
use App\StudentWishlist;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class CourseController extends Controller
{

    public function get_data(Request $req, Course $course)
    {
        $data = $course->toArray();

        $whatYouWillLearnList = CourseLearnList::where('course_id', $course->id)->get();
        $whatYouWillLearnList = (array) $whatYouWillLearnList->map(function ($item) {
            return ['title' => $item->title];
        })->toArray();
        $whatYouWillLearnList = ['whatYouWillLearnList' => $whatYouWillLearnList];

        $resources = CourseResource::where('course_id', $course->id)->get();
        $resources = $resources->map(function ($item) {
            return [
                'id' => $item->id,
                'title' => $item->title,
                'file' => $item->file_url
            ];
        })->toArray();
        $resources = ['resources' => $resources];

        $sections = CourseSection::where('course_id', $course->id)->get();
        $sections = $sections->map(function ($section) {
            $videos = CourseVideo::where('section_id', $section->id)->get();
            $videos = $videos->map(function ($video) {
                return [
                    'id' => $video->id,
                    'title' => $video->title,
                    'vimeoVideoId' => $video->vimeoVideoId,
                    'preview' => (bool) $video->preview
                ];
            })->toArray();

            return [
                'id' => $section->id,
                'title' => $section->title,
                'videos' => $videos
            ];
            //
        })->toArray();
        $sections = ['sections' => $sections];

        $data = array_merge($data, $whatYouWillLearnList);
        $data = array_merge($data, $resources);
        $data = array_merge($data, $sections);
        return response()->json(['course' => $data]);
    }

    public function list(Request $req)
    {
        $data = Course::all()->map(function ($course) {
            return [
                'id' => $course->id,
                'title' => $course->title
            ];
        });

        return response()->json(['courses' => $data]);
    }

    public function sections(Request $req, Course $course)
    {
        $data = $course->sections->map(function ($section) {

            $lectures = ['lectures' => $section->videos];
            return array_merge($section->toArray(), $lectures);
        });

        return response()->json(['sections' => $data]);
    }

    public function add(Request $req)
    {
        $validation = Validator::make($req->all(), [
            'title' => 'required|string',
            'description' => 'required|string',
            'language' => 'required|string',
            'category_id' => 'required|numeric',
            'difficulty' => 'required|numeric',
            'about' => 'required',
            'priceType' => 'required',
            'thumbnail' => 'required',
            'whatYouWillLearnList' => 'required|json',
            'instructor_id' => 'required|numeric'
        ]);

        if ($validation->fails()) return response()->json(['errors' => $validation->errors()], 401);

        $course = new Course;
        $course->title = $req->title;
        $course->description = $req->description;
        $course->language = $req->language;
        $course->difficulty = $req->difficulty;
        if ($req->priceType === 'paid') $course->has_trial = (bool) $req->has_trial;
        if ($req->startCourseMail) $course->startCourseMail = $req->startCourseMail;
        if ($req->finishCourseMail) $course->finishCourseMail = $req->finishCourseMail;
        $course->slug = Str::slug($req->title);
        $course->about = $req->about;
        $course->timescale = 0;
        $course->category_id = $req->category_id;
        $course->type = "course";

        $course->price_type = $req->priceType;

        if ($req->priceType === 'paid') {
            $course->price_monthly = $req->monthlyPrice;
            $course->price_quarterly = $req->quarterlyPrice;
            $course->price_semiannually = $req->semiannuallyPrice;
            $course->price_annually = $req->annuallyPrice;
        }

        $course->thumbnail_url = $req->thumbnail;
        $course->instructor_id = $req->instructor_id;
        $course->save();

        $learnList = json_decode($req->whatYouWillLearnList);
        foreach ($learnList as $list) {
            CourseLearnList::create([
                'title' => $list->title,
                'course_id' => $course->id
            ]);
        }

        return response()->json(['code' => 'done', 'id' => $course->id]);


        //
    }

    public function add_data(Request $req, Course $course)
    {
        $validation = Validator::make($req->all(), [
            'resources' => 'required|json',
            'sections' => 'required|json'
        ]);

        if ($validation->fails()) return response()->json(['errors' => $validation->errors()], 401);

        $vimeo = new \Vimeo\Vimeo(env('VIMEO_CLIENT_ID'), env('VIMEO_CLIENT_SECRET'));
        $vimeo->setToken(env('VIMEO_ACCESS_TOKEN'));

        $resources = json_decode($req->resources);
        foreach ($resources as $resource) {
            CourseResource::create([
                'title' => $resource->title,
                'file_url' => $resource->file,
                'course_id' => $course->id
            ]);
        }

        $totalDuration = 0;

        $sections = json_decode($req->sections);
        foreach ($sections as $section) {
            $totalSectionDuration = 0;

            $courseSection = CourseSection::create([
                'title' => $section->title,
                'timescale' => 0,
                'course_id' => $course->id
            ]);

            foreach ($section->videos as $video) {
                $response = $vimeo->request('/me/videos/' . $video->vimeoVideoId);
                $duration = $response['body']['duration'];
                $totalSectionDuration += $duration;

                CourseVideo::create([
                    'title' => $video->title,
                    'vimeoVideoId' => $video->vimeoVideoId,
                    'preview' => $video->preview,
                    'section_id' => $courseSection->id,
                    'timescale' => $duration
                ]);
            }
            $courseSection->update([
                'timescale' => $totalSectionDuration
            ]);
            $totalDuration += $totalSectionDuration;
        }

        $course->update([
            'timescale' => $totalDuration
        ]);

        return response()->json(['code' => 'done']);


        //
    }

    public function update(Request $req, Course $course)
    {

        $validation = Validator::make($req->all(), [
            'title' => 'required|string',
            'description' => 'required|string',
            'language' => 'required|string',
            'category_id' => 'required|numeric',
            'difficulty' => 'required|numeric',
            'about' => 'required',
            'type' => 'required',
            'priceType' => 'required',
            'whatYouWillLearnList' => 'required|json',
            'instructor_id' => 'required|numeric'
        ]);

        if ($validation->fails()) return response()->json(['errors' => $validation->errors()], 401);

        $oldCoursePriceType = $course->price_type;
        $newCoursePriceType = $req->priceType;

        if ($oldCoursePriceType === 'free' && $newCoursePriceType === 'paid') {
            $oldStudents = CourseStudent::where('course_id', $course->id)->where('status', 'active')->get();

            $oldStudents->each(function ($oldStudent) {
                $oldStudent->update(['status' => 'deactive']);
            });
        }

        $course->update([
            'title' => $req->title,
            'description' => $req->description,
            'slug' => Str::slug($req->title),
            'category_id' => $req->category_id,
            'difficulty' => $req->difficulty,
            'startCourseMail' => $req->startCourseMail,
            'finishCourseMail' => $req->finishCourseMail,
            'about' => $req->about,
            'type' => $req->type,
            'price_type' => $req->priceType,
            'instructor_id' => $req->instructor_id,
            'parent_course_id' => $req->parentCourseId,
        ]);

        if ($req->priceType === 'paid') {
            $course->update([
                'has_trial' => (bool) $req->has_trial,
                'price_monthly' => $req->monthlyPrice ?: 0,
                'price_quarterly' => $req->quarterlyPrice ?: 0,
                'price_semiannually' => $req->semiannuallyPrice ?: 0,
                'price_annually' => $req->annuallyPrice ?: 0,
            ]);
        }

        if ($req->thumbnail) $course->thumbnail_url = $req->thumbnail;
        if ($req->type === 'revision') $course->revision_parent_id = $req->revisionParentId;
        $course->save();

        CourseLearnList::where('course_id', $course->id)->delete();
        $learnList = json_decode($req->whatYouWillLearnList);
        foreach ($learnList as $list) {
            CourseLearnList::create([
                'title' => $list->title,
                'course_id' => $course->id
            ]);
        }

        return response()->json(['code' => 'done']);
    }

    public function update_data(Request $req, Course $course)
    {

        $validation = Validator::make($req->all(), [
            'resources' => 'required|json',
            'sections' => 'required|json',
        ]);

        if ($validation->fails()) return response()->json(['errors' => $validation->errors()], 401);


        CourseResource::where('course_id', $course->id)->delete();
        $resources = json_decode($req->resources);
        foreach ($resources as $resource) {
            CourseResource::create([
                'title' => $resource->title,
                'file_url' => $resource->file,
                'course_id' => $course->id
            ]);
        }

        $vimeo = new \Vimeo\Vimeo(env('VIMEO_CLIENT_ID'), env('VIMEO_CLIENT_SECRET'));
        $vimeo->setToken(env('VIMEO_ACCESS_TOKEN'));

        $totalDuration = 0;

        CourseSection::where('course_id', $course->id)->delete();

        $sections = json_decode($req->sections);
        foreach ($sections as $section) {
            $totalSectionDuration = 0;

            $courseSection = CourseSection::create([
                'title' => $section->title,
                'timescale' => 0,
                'course_id' => $course->id
            ]);

            foreach ($section->videos as $video) {
                Log::info('VIDEO ID: ' . $video->vimeoVideoId);
                $response = $vimeo->request('/me/videos/' . $video->vimeoVideoId);
                Log::info($response);
                $duration = $response['body']['duration'];
                Log::info($duration);
                $totalSectionDuration += $duration;
                sleep(3);

                CourseVideo::create([
                    'title' => $video->title,
                    'vimeoVideoId' => $video->vimeoVideoId,
                    'preview' => $video->preview,
                    'section_id' => $courseSection->id,
                    'timescale' => $duration
                ]);
            }
            $courseSection->update([
                'timescale' => $totalSectionDuration
            ]);
            $totalDuration += $totalSectionDuration;
        }

        $course->update([
            'timescale' => $totalDuration
        ]);


        return response()->json(['code' => 'done']);
        //
    }

    public function view_discussions(Request $req, Course $course)
    {
        $me = auth()->user();
        $student = CourseStudent::where('course_id', $course->id)->where('student_id', $me->id)
            ->isActive()->first();
        $currentVideo = CourseVideo::find($student->last_lesson_id);
        $currentSection = CourseSection::find($currentVideo->section_id);

        return view('pages.course.discussions.index')->with([
            'course' => $course,
            'currentSection' => $currentSection,
            'currentVideo' => $currentVideo
        ]);
    }

    public function view_discussion(Request $req, Course $course, CourseDiscussion $discussion)
    {
        $me = auth()->user();
        $student = CourseStudent::where('course_id', $course->id)->where('student_id', $me->id)
            ->isActive()->first();
        $currentVideo = CourseVideo::find($student->last_lesson_id);
        $currentSection = CourseSection::find($currentVideo->section_id);

        return view('pages.course.discussions.discussion')->with([
            'course' => $course,
            'discussion' => $discussion,
            'currentSection' => $currentSection,
            'currentVideo' => $currentVideo
        ]);
    }

    public function view_discussions_ask(Request $req, Course $course)
    {
        $me = auth()->user();
        $student = CourseStudent::where('course_id', $course->id)->where('student_id', $me->id)
            ->isActive()->first();
        $currentVideo = CourseVideo::find($student->last_lesson_id);
        $currentSection = CourseSection::find($currentVideo->section_id);

        return view('pages.course.discussions.ask')->with([
            'course' => $course,
            'currentSection' => $currentSection,
            'currentVideo' => $currentVideo
        ]);;
    }

    public function view_add_data(Request $req, Course $course)
    {
        return view('admin.course.add_data')->withCourse($course);
    }

    public function delete(Request $req, Course $course)
    {
        $course->delete();
        return redirect('/admin');
    }

    public function publish(Request $req, Course $course)
    {
        $course->update([
            'status' => 'active'
        ]);
        return redirect('/admin/courses');
    }

    public function draft(Request $req, Course $course)
    {
        $course->update([
            'status' => 'pending'
        ]);
        return redirect('/admin/courses');
    }

    public function view_edit(Request $req, Course $course)
    {
        return view('admin.course.edit')->withCourse($course);
    }

    public function view_edit_data(Request $req, Course $course)
    {
        return view('admin.course.edit_data')->withCourse($course);
    }

    public function view_course(Request $req, Course $course)
    {
        if (auth()->check()) {
            $checkStudent = CourseStudent::where('course_id', $course->id)
                ->where('student_id', auth()->user()->id)->where('status', 'active');

            if ($checkStudent->count() > 0) {
                $lastLessonId = $checkStudent->first()->last_lesson_id;

                return redirect("course/" . $course->id . "/lesson/" . $lastLessonId);
            }
        }

        $previews = [];

        $course->sections->each(function ($section) use (&$previews) {

            $section->videos->each(function ($video) use (&$previews) {
                if ($video->preview == true) {
                    array_push($previews, $video);
                }
            });

            //
        });

        $course->update([
            'view_count' => $course->view_count + 1
        ]);

        return view('pages.course.index')->with([
            'course' => $course,
            'previews' => $previews
        ]);
    }

    public function view_enroll(Request $req, Course $course)
    {

        if (!auth()->check()) {
            $return_url = "course/{$course->id}";
            session()->put('return_url', $return_url);
            return redirect('/login');
        }

        $userId = auth()->user()->id;

        $isNotAlreadyStudent = CourseStudent::where('course_id', $course->id)
            ->where('student_id', $userId)
            ->count() === 0;

        $deactiveStudent = CourseStudent::where('course_id', $course->id)
            ->where('student_id', $userId)
            ->where('status', 'deactive');




        if ($course->price_type === 'free') {
            $firstLesson = $course->sections->first()->videos->first();

            if ($isNotAlreadyStudent) {

                CourseStudent::create([
                    'course_id' => $course->id,
                    'student_id' => auth()->user()->id,
                    'last_lesson_id' => $firstLesson->id,
                    'is_in_trial' => false,
                ]);

                Mail::to(auth()->user()->email)->send(new StartCourseMail($course));
                Mail::to($course->instructor->email)->send(new StudentEnrolled($course, auth()->user()));


                return redirect('course/' . $course->id . '/lesson/' . $firstLesson->id);
            } else if ($deactiveStudent->count() === 1) {

                $deactiveStudent->update(['status' => 'active']);

                return redirect('course/' . $course->id . '/lesson/' . $firstLesson->id);
            } else abort(403, 'You are already enrolled for this course!');



            // ELSE PAID
        } else {

            if (auth()->user()->isCompanyEmployee()) {
                $lastPaymentDate = [
                    'monthly' => Carbon::now()->addMonth(),
                    'quarterly' => Carbon::now()->addMonths(3),
                    'semi_annually' => Carbon::now()->addMonths(6),
                    'annually' => Carbon::now()->addYear(),
                ];

                $firstSection = $course->sections->first();
                $firstLesson = $firstSection->videos->first();

                CourseStudent::create([
                    'course_id' => $course->id,
                    'student_id' => auth()->user()->id,
                    'by_company_id' => auth()->user()->isCompanyEmployee()->company_id,
                    'subscription' => 'monthly',
                    'price' => $course->price_monthly,
                    'last_lesson_id' => $firstLesson->id,
                    'last_payment_date' => Carbon::now(),
                    'next_payment_date' => $lastPaymentDate['monthly']
                ]);

                Mail::to(auth()->user()->email)->send(new StartCourseMail($course));
                Mail::to($course->instructor->email)->send(new StudentEnrolled($course, auth()->user()));

                return redirect('/course/' . $course->id);
            } else return view('pages.course.enroll')->withCourse($course);
        }
    }

    public function take_lesson(Request $req, Course $course, CourseVideo $lesson)
    {
        $currentSection = $lesson->section;

        $student = CourseStudent::where('course_id', $course->id)
            ->where('student_id', auth()->user()->id)->first();

        $student->update([
            'last_lesson_id' => $lesson->id
        ]);

        $data = [
            'course' => $course,
            'currentSection' => $currentSection,
            'currentVideo' => $lesson
        ];

        $checkCompleted = CourseVideoWatched::where('course_id', $course->id)
            ->where('lesson_id', $lesson->id)
            ->where('student_id', auth()->user()->id)
            ->count() === 0;

        $checkAdmin = auth()->user()->type !== 'admin';
        $checkMyCourse = auth()->user()->id !== $course->instructor_id;


        if ($checkCompleted && $checkAdmin && $checkMyCourse) CourseVideoWatched::create([
            'course_id' => $course->id,
            'lesson_id' => $lesson->id,
            'student_id' => auth()->user()->id
        ]);

        return view('pages.course.lesson')->with($data);

        //
    }

    public function take_exam(Request $req, Course $course, CourseExam $exam)
    {
        $afterVideo = CourseVideo::find($exam->order_lecture_id);
        $currentSection = $afterVideo->section;

        return view('pages.course.exam')->with([
            'course' => $course,
            'exam' => $exam,
            'currentSection' => $currentSection
        ]);

        //
    }

    public function preview_video(Request $req, Course $course, CourseVideo $video)
    {
        if ($video->preview == true) return view('pages.course.preview_video')->withCourse($course)->withCurrentVideo($video);
        else return abort(403);
        //
    }

    public function continue_watching(Request $req, Course $course)
    {
        $student = CourseStudent::where('course_id', $course->id)
            ->where('student_id', auth()->user()->id)->where('status', 'active');
        $check = $student->count() > 0;

        if ($check) {
            $student = $student->first();
            return redirect('course/' . $course->id . '/lesson/' . $student->last_lesson_id);
        } else abort(403);


        //
    }

    public function paid_enrollment(Request $req, Course $course, $type)
    {
        if (!auth()->check()) {
            $return_url = "course/{$course->id}/enroll";
            session()->put('return_url', $return_url);
            return redirect('/login');
        } elseif (auth()->check() && !auth()->user()->verified()) {
            session()->flash('message_warning', 'Please verify your account!');
            return redirect('/');
        }

        $firstSection = $course->sections->first();
        $firstLesson = $firstSection->videos->first();


        $checkStudent = CourseStudent::where('course_id', $course->id)->where('student_id', auth()->user()->id)->where('status', 'active')->count() === 0;
        if (!$checkStudent) abort(403, 'You are already enrolled to this course!');

        $checkIfAlreadyHasTrial = CourseStudent::where('course_id', $course->id)->where('student_id', auth()->user()->id)->where('trial_started', null)->count() === 0;

        if ($course->has_trial && $checkIfAlreadyHasTrial) {

            CourseStudent::create([
                'course_id' => $course->id,
                'student_id' => auth()->user()->id,
                'subscription' => $type,
                'is_in_trial' => true,
                'price' => 0,
                'trial_started' => Carbon::now(),
                'last_lesson_id' => $firstLesson->id
            ]);

            Mail::to(auth()->user()->email)->send(new StartCourseMail($course));
            Mail::to($course->instructor->email)->send(new StudentEnrolled($course, auth()->user()));

            return redirect('course/' . $course->id . '/lesson/' . $firstLesson->id);
            //
        } else {

            $_type = $type;
            if ($_type === 'semi_annually') $_type = 'semiannually';

            $priceForBank = $course->{'price_' . $_type} * 100;
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


            $payment = Payment::redirectToPayment($priceForBank, "Course Payment");
            if (count($payment['errors']) > 0) {
                session()->flash('message_warning', 'Ödəniş zamanı xəta baş verdi. Bir az sonra yenidən cəhd edin');
                return redirect()->back();
                // abort(500, $payment[0]);
            } else {

                CoursePayment::create([
                    'course_id' => $course->id,
                    'student_id' => auth()->user()->id,
                    'subscription_type' => $type,
                    'transaction_id' => $payment['trans_id'],
                    'price' => $coupon ? number_format($priceForBank / 100, 2) : $course->{'price_' . $_type},
                    'coupon_id' => $coupon
                ]);

                return redirect()->to($payment['client_url']);
            }
        }
    }

    public function give_rating(Request $req, Course $course, $rating)
    {

        $req->validate([
            'rating' => 'numeric|in:1,2,3,4,5'
        ]);

        $myRating = $course->myRating();
        if ($myRating) $myRating->update([
            'rating' => $rating
        ]);
        else CourseRating::create([
            'course_id' => $course->id,
            'student_id' => auth()->user()->id,
            'rating' => $rating
        ]);

        $totalRating = 0;
        $totalRatingCount = $course->ratings->count();
        $course->ratings->each(function ($rating) use (&$totalRating) {
            $totalRating += (int) ($rating->rating);
        });
        if ($totalRatingCount !== 0) $rating = (float) ($totalRating / $totalRatingCount);
        else $rating = (float) ($req->rating);

        $course->update([
            'rating' => $rating
        ]);

        return redirect()->back();

        //
    }

    public function create_section(Request $req, Course $course)
    {
        $validation = Validator::make($req->all(), [
            'title' => 'required|string'
        ]);
        if ($validation->fails()) return response()->json($validation->errors(), 401);

        $section = $course->sections()->create([
            'title' => $req->title,
            'timescale' => 0
        ]);

        return response()->json(['code' => 'done', 'sectionId' => $section->id]);

        //
    }

    public function update_section(Request $req, Course $course, CourseSection $section)
    {
        $validation = Validator::make($req->all(), [
            'title' => 'required|string'
        ]);
        if ($validation->fails()) return response()->json($validation->errors(), 401);

        $section->update([
            'title' => $req->title
        ]);

        return response()->json(['code' => 'done']);

        //
    }

    public function delete_section(Request $req, Course $course, CourseSection $section)
    {
        $course->update([
            'timescale' => $course->timescale - $section->timescale
        ]);
        $section->delete();

        return response()->json(['code' => 'done']);

        //
    }

    public function delete_video(Request $req, Course $course, CourseSection $section, CourseVideo $video)
    {
        $video = $section->videos->where('id', $video->id)->first();

        $section->update([
            'timescale' => $section->timescale - $video->timescale
        ]);

        $course->update([
            'timescale' => $course->timescale - $video->timescale
        ]);

        $checkStudents = CourseStudent::where('last_lesson_id', $video->id);

        if ($checkStudents->count() > 0) {
            $checkStudents = $checkStudents->get();

            $foundVideo = null;

            $foundVideo = $section->videos->where('id', '<', $video->id)->first();
            if (!$foundVideo) $course->sections->where('id', '<', $section->id)->each(function ($courseSection) use (&$foundVideo) {
                if ($courseSection->videos->count() > 0) {
                    $foundVideo = $courseSection->videos->last();
                    return;
                }
            });

            $checkStudents->each(function ($courseStudent) use ($foundVideo) {
                $courseStudent->update([
                    'last_lesson_id' => $foundVideo->id
                ]);
            });
        }


        $video->delete();

        return response()->json(['code' => 'done']);

        //
    }

    public function update_video(Request $req, Course $course, CourseSection $section, CourseVideo $video)
    {
        $validation = Validator::make($req->all(), [
            'title' => 'required|string',
            'vimeoVideoId' => 'required|numeric',
            'preview' => 'required|boolean'
        ]);
        if ($validation->fails()) return response()->json($validation->errors(), 401);

        $video = $section->videos->where('id', $video->id)->first();

        $timescale = $video->timescale;
        if ($video->vimeoVideoId !== $req->vimeoVideoId) {
            $vimeo = new \Vimeo\Vimeo(env('VIMEO_CLIENT_ID'), env('VIMEO_CLIENT_SECRET'));
            $vimeo->setToken(env('VIMEO_ACCESS_TOKEN'));
            $response = $vimeo->request('/me/videos/' . $req->vimeoVideoId);
            $duration = $response['body']['duration'];
            $timescale = $duration;
        }

        $video->update([
            'title' => $req->title,
            'vimeoVideoId' => $req->vimeoVideoId,
            'preview' => $req->preview,
            'timescale' => $timescale
        ]);

        $totalSectionDuration = 0;
        $section->videos->each(function ($sectionVideo) use (&$totalSectionDuration) {
            $totalSectionDuration += (int) ($sectionVideo->timescale);
        });
        $section->update([
            'timescale' => $totalSectionDuration
        ]);

        $totalCourseDuration = 0;
        $course->sections->each(function ($courseSection) use (&$totalCourseDuration) {
            $totalCourseDuration += (int) ($courseSection->timescale);
        });
        $course->update([
            'timescale' => $totalCourseDuration
        ]);

        return response()->json(['code' => 'done']);

        //
    }

    public function add_video(Request $req, Course $course, CourseSection $section)
    {
        $validation = Validator::make($req->all(), [
            'title' => 'required|string',
            'vimeoVideoId' => 'required|numeric',
            'preview' => 'required|boolean',
        ]);
        if ($validation->fails()) return response()->json($validation->errors(), 401);

        $vimeo = new \Vimeo\Vimeo(env('VIMEO_CLIENT_ID'), env('VIMEO_CLIENT_SECRET'));
        $vimeo->setToken(env('VIMEO_ACCESS_TOKEN'));
        $response = $vimeo->request('/me/videos/' . $req->vimeoVideoId);
        $duration = $response['body']['duration'];
        $timescale = $duration;

        $video = $section->videos()->create([
            'title' => $req->title,
            'vimeoVideoId' => $req->vimeoVideoId,
            'preview' => $req->preview,
            'timescale' => $timescale
        ]);

        $totalSectionDuration = 0;
        $section->videos->each(function ($sectionVideo) use (&$totalSectionDuration) {
            $totalSectionDuration += (int) ($sectionVideo->timescale);
        });
        $section->update([
            'timescale' => $totalSectionDuration
        ]);

        $totalCourseDuration = 0;
        $course->sections->each(function ($courseSection) use (&$totalCourseDuration) {
            $totalCourseDuration += (int) ($courseSection->timescale);
        });
        $course->update([
            'timescale' => $totalCourseDuration
        ]);

        return response()->json(['code' => 'done', 'videoId' => $video->id]);

        //
    }

    public function add_resource(Request $req, Course $course)
    {
        $validation = Validator::make($req->all(), [
            'title' => 'required|string',
            'fileUrl' => 'required|string',
        ]);
        if ($validation->fails()) return response()->json($validation->errors(), 401);

        $resource = $course->resources()->create([
            'title' => $req->title,
            'file_url' => $req->fileUrl
        ]);

        return response()->json(['code' => 'done', 'resourceId' => $resource->id]);

        //
    }

    public function remove_resource(Request $req, Course $course, CourseResource $resource)
    {
        $resource->delete();
        return response()->json(['code' => 'done']);
    }

    public function favorite_course(Request $req, Course $course)
    {
        $check = StudentWishlist::where('student_id', auth()->user()->id)->where('course_id', $course->id);
        if ($check->count() === 0) {
            StudentWishlist::create([
                'student_id' => auth()->user()->id,
                'course_id' => $course->id
            ]);
            return redirect()->back();
        } else $check->first()->delete();
        return redirect()->back();
    }

    public function discussions_ask(Request $req, Course $course)
    {
        $req->validate([
            'title' => 'required|string',
            'question' => 'required|string'
        ]);

        $discussion = $course->discussions()->create([]);
        $discussion->messages()->create([
            'title' => $req->title,
            'question' => $req->question,
            'user_id' => auth()->user()->id,
        ]);

        Notification::create([
            'to_user_id' => $course->instructor_id,
            'notification' => 'New Question asked',
            'link' => url('course/' . $course->id . '/discussion/' . $discussion->id)
        ]);

        return redirect('course/' . $course->id . '/discussion/' . $discussion->id);
    }


    public function discussion_comment(Request $req, Course $course, CourseDiscussion $discussion)
    {
        $req->validate([
            'comment' => 'required|string'
        ]);

        $discussion->messages()->create([
            'question' => $req->comment,
            'parent_message_id' => $discussion->messages()->first()->id,
            'user_id' => auth()->user()->id,
        ]);

        $to_user_id = $discussion->messages()->first()->user_id;

        Notification::create([
            'to_user_id' => $to_user_id,
            'notification' => 'There is a reply to your question!',
            'link' => url('course/' . $course->id . '/discussion/' . $discussion->id)
        ]);

        return redirect('course/' . $course->id . '/discussion/' . $discussion->id);
    }


    public function add_student_to_paid_course(Request $req)
    {
        $req->validate([
            'course_id' => 'required',
            'subscription_type' => 'required',
            'students' => 'required|array',
        ]);

        $firstLesson = Course::find($req->course_id)->sections->first()->videos->first();

        $lastPaymentDate = [
            'monthly' => Carbon::now()->addMonth(),
            'quarterly' => Carbon::now()->addMonths(3),
            'semi_annually' => Carbon::now()->addMonths(6),
            'annually' => Carbon::now()->addYear(),
        ];

        foreach ($req->students as $studentId) {
            CourseStudent::create([
                'course_id' => $req->course_id,
                'student_id' => $studentId,
                'subscription' => $req->subscription_type,
                'last_lesson_id' => $firstLesson->id,
                'last_payment_date' => Carbon::now(),
                'next_payment_date' => $lastPaymentDate[$req->subscription_type]
            ]);
        }

        return redirect('admin/course/payments');
    }


    //
}
