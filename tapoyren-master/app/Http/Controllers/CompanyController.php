<?php

namespace App\Http\Controllers;

use App\Company;
use App\CompanyUser;
use App\Course;
use App\CourseExam;
use App\CourseExamData;
use App\CourseExamDataAnswer;
use App\CourseStudent;
use App\CourseVideoWatched;
use App\User;
use Illuminate\Http\Request;

class CompanyController extends Controller
{

    public function add(Request $req)
    {
        $req->validate([
            'title' => 'required|string',
            'owner_id' => 'required|numeric|exists:users,id'
        ]);

        Company::create([
            'title' => $req->title,
            'owner_id' => $req->owner_id
        ]);

        return redirect('admin/companies');
    }

    public function delete(Request $req, Company $company)
    {
        $company->delete();
        return redirect('admin/companies');
    }

    public function add_user(Request $req, Company $company)
    {

        foreach ($req->users as $user) {
            CompanyUser::create([
                'company_id' => $req->company_id,
                'user_id' => $user
            ]);
        }

        return redirect('admin/companies');
    }

    public function view_dashboard(Request $request)
    {
        $user = auth()->user();

        $company = $user->company;

        $courses = [];

        $company->users()->each(function ($user) use (&$courses) {
            $user->enrolledCourses()->each(function ($course) use (&$courses) {
                array_push($courses, $course);
            });
        });

        $courses = collect($courses)->unique()->map(function ($course) use (&$company) {

            $users = [];

            $company->users()->each(function ($user) use (&$users, $course) {
                $isStudent = CourseStudent::where('student_id', $user->id)->where('course_id', $course->id)->count() > 0;
                if ($isStudent) array_push($users, $user);
            });

            return array_merge($course->toArray(), [
                'users' => $users
            ]);
        });

        $data = [
            'company' => $company,
            'courses' => $courses
        ];

        if ($request->input('user_id')) {
            $user_id = (int) $request->input('user_id');
            $checkUser = $company->users()->where('id', $user_id);

            if ($checkUser->count() === 1) {
                $user = $checkUser->first();

                $data['user'] = $user;

                if ($request->input('user_course_id')) {
                    $checkCourse = $user->enrolledCourses()->where('id', $request->input('user_course_id'));
                    if ($checkCourse->count() === 1) {
                        $course = $checkCourse->first();

                        $totalVideosCount = 0;
                        $course->sections->each(function ($section) use (&$totalVideosCount) {
                            $totalVideosCount += $section->videos->count();
                        });

                        $watchedLessonsCount = CourseVideoWatched::where('course_id', $course->id)->where('student_id', $user->id)->count();

                        $completedPercentage = (int)($watchedLessonsCount / $totalVideosCount * 100);

                        $data['course'] = $course;
                        $data['userCompletedPercentage'] = $completedPercentage;

                        $watchedLessons = [];
                        $course->sections->each(function ($section) use (&$watchedLessons, $user) {

                            $section->videos->each(function ($video) use (&$watchedLessons, $user) {

                                $check = CourseVideoWatched::where('lesson_id', $video->id)->where('student_id', $user->id)->count() === 1;

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
                            $topicData = ['question_count' => 0, 'correct_answers' => 0,'wrong_answers'=>0];
                            return [$topic => $topicData];
                        })->collapse()->toArray();

                        $exams = $course->exams()->get()->unique();

                        $examDatas = [];
                        $exams->each(function ($exam) use (&$examDatas, $user) {

                            $examData = CourseExamData::where('student_id', $user->id)->where('course_exam_id', $exam->id)
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
                                'percentage' => (int) $percentage
                            ]);
                            array_push($topicsData, (object) $newData);
                        }

                        $data['topics'] = $topicsData;
                        $data['exams'] = $exams;
                        $data['watchedLessons'] = (int)(count($watchedLessons) / $course->videos_count() * 100);
                    }
                }

                //
            }
        }

        if($request->input('leaderboard')){
            $leaderboardCourse = Course::find($request->input('leaderboard'));
            $companyUsers = $company->users();

            $studentIds = $leaderboardCourse->pluck('id');

            $enrolledUsers = CourseStudent::where('course_id',$leaderboardCourse->id)->whereIn('student_id',$studentIds)->get();

            $data['leaderboard'] = $enrolledUsers;
            $data['leaderboardCourse'] = $leaderboardCourse;

            $attendedUsers = [];
            $companyUsers->each(function($companyUser) use (&$attendedUsers,&$leaderboardCourse) {
                $checkStudent = CourseStudent::where('student_id',$companyUser->id)->where('course_id',$leaderboardCourse->id)->count() > 0;
                if($checkStudent) array_push($attendedUsers,$companyUser);
            });

            $tableData = collect($attendedUsers)->map(function($employee) use (&$leaderboardCourse){

                $totalLessons = $leaderboardCourse->videos_count();
                $completedLessons = CourseVideoWatched::where('student_id',$employee->id)->where('course_id',$leaderboardCourse->id)->count();

                $lessonsPercentage = (int)($completedLessons / $totalLessons * 100);

                $courseExams = CourseExam::where('course_id',$leaderboardCourse->id)->get()->pluck('id');
                $attendedExams = CourseExamData::where('student_id', $employee->id)->whereIn('course_exam_id', $courseExams);

                $attendedQuiz = $attendedExams->count();
                $failedQuiz = $attendedExams->where('status','failed')->count();


                //
                $allTopics = [];
                $leaderboardCourse->exams->each(function ($_e) use (&$allTopics) {
                    $_e->questions->each(function ($_q) use (&$allTopics) {
                        array_push($allTopics, $_q->topic);
                    });
                });

                $allTopics = collect($allTopics)->unique()->map(function ($topic) {
                    $topicData = ['question_count' => 0, 'correct_answers' => 0];
                    return [$topic => $topicData];
                })->collapse()->toArray();

                $examDatas = [];
                $leaderboardCourse->exams->each(function ($exam) use (&$examDatas, $employee) {

                    $examData = CourseExamData::where('student_id', $employee->id)->where('course_exam_id', $exam->id)
                    ->orderBy('id', 'desc');
                    if ($examData->exists()) array_push($examDatas, $examData->first());
                });
                $examDatas = collect($examDatas);


                $examDatas->each(function ($examData) use (&$allTopics) {

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

                });


                $topicsData = [];
                foreach ($allTopics as $key => $value) {
                    $percentage = 0;
                    if ($value['question_count'] !== 0) $percentage = $value['correct_answers'] / $value['question_count'] * 100;
                    $newData = array_merge($value, [
                        'name' => $key,
                        'percentage' => (int) $percentage
                    ]);
                    array_push($topicsData, (object) $newData);
                }


                $totalPercentage = 0;
                foreach($topicsData as $topicData) $totalPercentage += $topicData->percentage;

                $averageTopic = (int)($totalPercentage/count($topicsData));
                //

                return [
                    'name' => $employee->name,
                    'lessonsPercentage' => $lessonsPercentage,
                    'attendedQuiz' => $attendedQuiz,
                    'failedQuiz' => $failedQuiz,
                    'averageTopic' => $averageTopic
                ];


                //
            });

            $tableData = $tableData
                ->sort(function ($a, $b) {
                    return $a['failedQuiz'] > $b['failedQuiz'];
                })
                ->sort(function ($a, $b) {
                    return $a['attendedQuiz'] < $b['attendedQuiz'];
                })
                ->sort(function ($a, $b) {
                    return $a['averageTopic'] < $b['averageTopic'];
                })
                ->sort(function ($a, $b) {
                    return $a['lessonsPercentage'] < $b['lessonsPercentage'];
                });

            $data['leaderboardTable'] = $tableData;


        }

        return view('company.dashboard.index')->with($data);

        //
    }

    public function view_enrollments(Request $request, Company $company)
    {

        $enrollments = CourseStudent::where('by_company_id', $company->id)->orderBy('id', 'desc')->get();

        $totalPrice = 0;
        $enrollments->each(function ($enrollment) use (&$totalPrice) {
            $totalPrice += $enrollment->price;
        });

        return view('admin.company.enrollment')->with([
            'totalPrice' => $totalPrice,
            'enrollments' => $enrollments
        ]);
    }

    //
}
