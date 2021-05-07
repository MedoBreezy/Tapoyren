<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements MustVerifyEmail
{
    use Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token', 'api_token'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    function lessonCompleted($lessonId)
    {
        $check = CourseVideoWatched::where('lesson_id', $lessonId)
            ->where('student_id', $this->id)
            ->count() > 0;

        return $check;
    }

    function examCompleted($examId)
    {
        $check = CourseExamData::where('course_exam_id', $examId)
            ->where('student_id', $this->id)
            ->where('status', 'passed')
            ->count() > 1;

        return $check;
    }

    public function enrolledCourses()
    {
        $studentsOf = CourseStudent::where('student_id', $this->id)->get();
        $data = $studentsOf->map(function ($studentOf) {
            return Course::find($studentOf->course_id);
        })->unique();

        return $data;
    }

    public function courses()
    {
        return $this->hasMany('App\Course', 'instructor_id');
    }

    public function studentCount()
    {
        $totalCount = 0;
        $this->courses->each(function ($course) use (&$totalCount) {
            $totalCount += $course->students->count();
        });

        return $totalCount;
    }

    public function lastConversations()
    {
        // if($this->type==='student'){
        $conversations = Conversation::where('student_id', $this->id)->orderBy('id', 'desc')->get();

        return $conversations->map(function ($conversation) {
            $messages = $conversation->join('conversation_messages', 'conversation_messages.conversation_id', '=', 'conversations.id')
                ->orderBy('conversation_messages.updated_at', 'asc')
                ->get();

            $conversation_url = url("instructor/{$conversation->instructor_id}/conversation/{$conversation->id}");

            return array_merge($conversation->toArray(), ['messages' => $messages, 'conversation_url' => $conversation_url]);
        });


        // }
        // else return null;
    }

    public function favoriteCourses()
    {
        $wishlist = StudentWishlist::where('student_id', $this->id)->get();
        $courses = $wishlist->map(function ($favorite) {
            return Course::find($favorite->course_id);
        });
        return $courses;
    }

    public function favoritedCourse($courseId)
    {
        return StudentWishlist::where('student_id', $this->id)->where('course_id', $courseId)->count() === 1;
    }

    public function canResendEmailVerification()
    {
        $emails = VerifyEmail::where('user_id', $this->id)->where('status', 'pending');

        if ($this->email_verified_at === null && $emails->count() < 3) return true;
        else return false;
    }

    public function verified()
    {
        return $this->email_verified_at !== null;
    }

    public function notifications()
    {
        return $this->hasMany('App\Notification', 'to_user_id');
    }

    public function headNotifications(){
        return $this->notifications()->orderBy('id', 'desc')->take(8)->get();
    }

    public function unreadNotifications()
    {
        return $this->notifications()->where('read', false)->orderBy('id', 'desc')->get();
    }

    public function hasCompany()
    {
        $check = Company::where('owner_id', $this->id)->count() === 1;
        return $check;
    }

    public function isCompanyEmployee()
    {
        $check = CompanyUser::where('user_id', $this->id);

        if ($check->exists()) return $check->first();
        else return false;
    }

    public function company()
    {
        return $this->hasOne('App\Company', 'owner_id');
    }

    //
}
