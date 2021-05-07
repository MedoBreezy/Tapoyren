<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Course extends Model
{
    use SoftDeletes;

    protected $guarded = ['id'];
    protected $hidden = ['created_at', 'updated_at'];

    public function scopeIsActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeIsDeactive($query)
    {
        return $query->where('status', 'pending');
    }

    public function sections()
    {
        return $this->hasMany('App\CourseSection');
    }

    public function resources()
    {
        return $this->hasMany('App\CourseResource');
    }

    public function exams()
    {
        return $this->hasMany('App\CourseExam');
    }

    public function students()
    {
        return $this->hasMany('App\CourseStudent', 'course_id');
    }

    public function whatYoulearnList()
    {
        return $this->hasMany('App\CourseLearnList');
    }

    public function instructor()
    {
        return $this->belongsTo('App\User', 'instructor_id');
    }

    public function ratings()
    {
        return $this->hasMany('App\CourseRating');
    }

    public function enrolled(User $student)
    {
        return CourseStudent::where('course_id', $this->id)->where('student_id', $student->id)->count() > 0;
    }

    public function activeEnrolled(User $student)
    {
        return CourseStudent::where('course_id', $this->id)->where('student_id', $student->id)->where('status', 'active')->count() > 0;
    }

    public function checkPackageSubscription($user = null)
    {
        $checkSubscriber = false;
        return true;

        if (!$user && auth()->check()) $user = auth()->user();
        if (!$user instanceof User) return false;

        PackageCourse::where('course_id', $this->id)->get()->each(function ($packageCourse) use (&$checkSubscriber, $user) {

            $package = Package::find($packageCourse);
            $checkSubscription = PackageSubscriber::where('package_id', $package->id)
                ->where('student_id', $user->id)->where('status', 'active')->count() === 1;
            if ($package->status === 'active' && $checkSubscription) $checkSubscriber = true;

            //
        });
        return $checkSubscriber;
    }

    public function checkEnrollment()
    {
        if (auth()->check() && $this->activeEnrolled(auth()->user())) return false;
        else if (auth()->check() && auth()->user()->isCompanyEmployee()) return true;
        // elseif(auth()->check() && !$this->checkPackageSubscription()) return false;
        elseif (auth()->check() && !$this->activeEnrolled(auth()->user())) return true;
        elseif (!auth()->check()) return true;
        else return false;
    }

    public function myRating()
    {
        $rating = auth()->check() ? $this->ratings->where('student_id', auth()->user()->id)->first() : null;
        if ($rating) return $rating;
        else return null;
    }

    public function ratingPercent($rating)
    {
        $total = $this->ratings->count();
        $selected = $this->ratings->where('rating', $rating)->count();

        if ($selected !== 0) return (int) (($selected / $total) * 100);
        else return 0;
    }

    public function videos_count()
    {
        $count = 0;
        $this->sections->each(function ($section) use (&$count) {
            $count += $section->videos->count();
        });

        return $count;
    }

    public function isNew()
    {
        $now = Carbon::now();
        return ($this->created_at->diff($now)->days < 15);
    }

    public function currentVideo()
    {
        $section = $this->sections->first();
        return $section->videos->first();
    }

    public function discussions()
    {
        return $this->hasMany('App\CourseDiscussion', 'course_id');
    }

    public function nextSection($currentSectionId)
    {
        $section = $this->sections->where('id', '>', $currentSectionId)->first();
        while ($section && $section->videos->count() === 0) {
            $section = $this->sections->where('id', '>', $section->id)->first();
        }
        return $section;
    }

    public function nextSectionUrl($currentSectionId)
    {
        $nextSection = $this->nextSection($currentSectionId);
        $nextLesson = $nextSection ? $nextSection->videos->first() : null;

        while (!$nextLesson) {
            $nextSection = $this->nextSection($nextSection->id);
            if ($nextSection) $nextLesson = $nextSection->videos->first();
        }

        $url = url('course/' . $this->id . '/lesson/' . $nextLesson->id);
        return $url;
    }

    public function previousLessonId($currentLesson)
    {
        $found = null;

        $thisSection = CourseSection::find($currentLesson->section_id);
        $thisVideo = $thisSection->videos->where('id', '<', $currentLesson->id);

        if ($thisVideo->count() > 0) $found = $thisVideo->last();
        else {
            $previousSection = $this->sections->where('id', '<', $thisSection->id);
            if ($previousSection->count() > 0) $found = $previousSection->last()->videos->last();
        }

        if ($found) return $found->id;
        else return null;
    }

    public function nextLessonId($currentLesson)
    {
        $found = null;

        $thisSection = CourseSection::find($currentLesson->section_id);
        $thisVideo = $thisSection->videos->where('id', '>', $currentLesson->id);

        if ($thisVideo->count() > 0) $found = $thisVideo->first();
        else {
            $nextSection = $this->sections->where('id', '>', $thisSection->id);
            if ($nextSection->count() > 0) $found = $nextSection->first()->videos->first();
        }

        if ($found) return $found->id;
        else return null;
    }

    public function timescaleView($onlyHours = false)
    {
        $duration = $this->timescale;
        $hours = number_format($duration / 3600, 2);

        if (!$onlyHours) return "{$hours} " . trans('main.hour');
        else return $hours;
    }

    public function difficulty()
    {
        $level = $this->difficulty;
        $result = null;

        if ($level == 0) $result = 'Beginner';
        else if ($level == 1) $result = 'Intermediate';
        else if ($level == 2) $result = 'Advanced';

        return $result;
    }

    public function createPackageStudent(Package $package, User $student, $subscription, $last_lesson_id, $last_payment_date, $next_payment_date)
    {

        CourseStudent::create([
            'course_id' => $this->id,
            'student_id' => $student->id,
            'subscription' => $subscription,
            'last_lesson_id' => $last_lesson_id,
            'last_payment_date' => $last_payment_date,
            'next_payment_date' => $next_payment_date,
            'by_package_id' => $package->id
        ]);

        return true;
    }

    public function completedPayments()
    {
        return CoursePayment::where('course_id', $this->id)->where('status', 'completed');
    }


    //
}
