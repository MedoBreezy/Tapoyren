<?php

namespace App\Http\Controllers;

use App\Course;
use App\CourseExam;
use App\CourseExamData;
use App\CourseExamDataAnswer;
use App\CoursePayment;
use App\CourseStudent;
use App\CourseVideoWatched;
use App\Package;
use App\PackagePayment;
use App\PackageSubscriber;
use Illuminate\Http\Request;

class StudentController extends Controller
{

    public function view_dashboard(Request $request)
    {
        if ($request->input('course_id')) {

            $course = Course::findOrFail($request->input('course_id'));
            $examIds = $course->exams->map(function ($exam) {
                return $exam->id;
            })->toArray();

            $watchedLessons = [];
            $course->sections->each(function ($section) use (&$watchedLessons) {

                $section->videos->each(function ($video) use (&$watchedLessons) {

                    $check = CourseVideoWatched::where('lesson_id', $video->id)->where('student_id', auth()->user()->id)->count() === 1;

                    if ($check) array_push($watchedLessons, $video->id);
                });
            });

            $allTopics = [];
            $course->exams->each(function ($_e) use (&$allTopics) {
                $_e->questions->each(function ($_q) use (&$allTopics) {
                    array_push($allTopics, $_q->topic);
                });
            });

            $allTopics = collect($allTopics)->unique()->map(function ($topic) {
                $topicData = ['question_count' => 0, 'correct_answers' => 0];
                return [$topic => $topicData];
            })->collapse()->toArray();

            $exams = $course->exams()->get()->unique();

            $examDatas = [];
            $exams->each(function ($exam) use (&$examDatas) {
                $examData = CourseExamData::where('student_id', auth()->user()->id)->where('course_exam_id', $exam->id)
                    ->orderBy('id', 'desc');
                if ($examData->exists()) array_push($examDatas, $examData->first());
            });
            $examDatas = collect($examDatas);

            $exams = $examDatas->map(function ($examData) use (&$allTopics) {

                $exam = CourseExam::find($examData->course_exam_id);
                $correctAnswers = 0;

                $exam->questions->each(function ($question) use (&$allTopics, $examData, &$correctAnswers) {

                    $allTopics[$question->topic]['question_count'] = $allTopics[$question->topic]['question_count'] + 1;

                    $dataAnswer = CourseExamDataAnswer::where('course_exam_question_id', $question->id)->where('course_exam_data_id', $examData->id);
                    $isCorrect = ($dataAnswer->count() !== 0 && $dataAnswer->first()->status === 'correct');

                    if ($isCorrect) {
                        $allTopics[$question->topic]['correct_answers'] = $allTopics[$question->topic]['correct_answers'] + 1;
                        $correctAnswers++;
                    }

                    //
                });

                $wrongAnswers = $exam->questions->count() - $correctAnswers;

                return collect($examData)->merge([
                    'exam' => $exam,
                    'wrongAnswers' => $wrongAnswers,
                    'created_at' => $examData->created_at,
                ]);
            });

            $topicsData = [];
            foreach ($allTopics as $key => $value) {
                $percentage = 0;
                if ($value['question_count'] !== 0) $percentage = $value['correct_answers'] / $value['question_count'] * 100;
                $newData = array_merge($value, [
                    'name' => $key,
                    'percentage' => $percentage
                ]);
                array_push($topicsData, (object) $newData);
            }

            return view('student.dashboard.index')->with([
                'course' => $course,
                'topics' => $topicsData,
                'exams' => $exams,
                'watchedLessons' => $watchedLessons,
                'countWatchedLessons' => (int)(count($watchedLessons) / $course->videos_count() * 100),
                'hasData' => true
            ]);
        } else return view('student.dashboard.index');
        //
    }

    public function view_payments(Request $request){

        $coursePayments = CourseStudent::where('subscription','!=',null)->where('student_id',auth()->user()->id)->orderBy('id','desc')->get()->map(function ($payment) {
                $course = Course::find($payment->course_id);

                $price = null;

                $paymentData = CoursePayment::where('student_id',auth()->user()->id)->where('course_id',$course->id)->where('status','completed')
                ->whereDate('created_at','=',$payment->created_at);

                if($paymentData->count()>0) $price = $paymentData->first()->price;

                return (object)[
                    'course' => $course,
                    'subscription_type' => $payment->subscription,
                    'price' => $price,
                    'date' => $payment->last_payment_date,
                    'end_date' => $payment->next_payment_date
                ];
            });

        $packagePayments = PackageSubscriber::where('subscription', '!=', null)->where('student_id', auth()->user()->id)->orderBy('id', 'desc')->get()->map(function ($payment) {
            $package = Package::find($payment->package_id);

            $price = null;

            $paymentData = PackagePayment::where('student_id', auth()->user()->id)->where('package_id', $package->id)->where('status', 'completed')
            ->whereDate('created_at', '=', $payment->created_at);

            if ($paymentData->count() > 0) $price = $paymentData->first()->price;

            return (object)[
                'package' => $package,
                'subscription_type' => $payment->subscription,
                'price' => $price,
                'date' => $payment->last_payment_date,
                'end_date' => $payment->next_payment_date
            ];
        });


        return view('pages.account.payments')->with([
            'coursePayments' => $coursePayments,
            'packagePayments' => $packagePayments
        ]);
    }

    //
}
