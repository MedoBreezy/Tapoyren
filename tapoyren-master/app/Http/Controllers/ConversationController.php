<?php

namespace App\Http\Controllers;

use App\Conversation;
use App\Course;
use App\User;
use Illuminate\Http\Request;

class ConversationController extends Controller
{

    public function view_new_message(Request $req, Course $course, User $instructor)
    {
        if ($instructor->type !== 'instructor') abort(403);

        $conversation = Conversation::where('student_id', auth()->user()->id)->where('instructor_id', $instructor->id);
        if($conversation->count()===1) {
            $conversation_id = $conversation->first()->id;
            return redirect("course/{$course->id}/instructor/{$instructor->id}/conversation/{$conversation_id}");
        }

        if ($instructor->type !== 'instructor') abort(403);

        return view('pages.conversation.new_message')->with([
            'instructor' => $instructor,
            'course' => $course
        ]);
    }

    public function new_message(Request $req, Course $course, User $instructor)
    {
        if ($instructor->type !== 'instructor') abort(403);

        $conversation = Conversation::where('student_id', auth()->user()->id)->where('instructor_id', $instructor->id);
        if ($conversation->count() === 1) {
            $conversation_id = $conversation->first()->id;
            return redirect("course/{$course->id}/instructor/{$instructor->id}/conversation/{$conversation_id}");
        }
        
        $req->validate([
            'message' => 'required|string'
        ]);

        $conversation = Conversation::where('student_id',auth()->user()->id)->where('instructor_id',$instructor->id);

        if($conversation->count()===1) {
            $conversation = $conversation->first();
            $conversation->messages()->create([
                'conversation_id' => $conversation->id,
                'message' => $req->message,
                'sender' => 'student'
            ]);
        }
        else {
            $conversation = Conversation::create([
                'student_id' => auth()->user()->id,
                'instructor_id' => $instructor->id,
            ]);
            $conversation->messages()->create([
                'conversation_id' => $conversation->id,
                'message' => $req->message,
                'sender' => 'student'
            ]);
        }

        return redirect("instructor/{$instructor->id}/conversation/{$conversation->id}");
    }

    public function new_conversation_message(Request $req, User $instructor, Conversation $conversation)
    {
        if ($instructor->type !== 'instructor') abort(403);

        $req->validate([
            'message' => 'required|string'
        ]);


        $conversation->messages()->create([
            'conversation_id' => $conversation->id,
            'message' => $req->message,
            'sender' => 'student'
        ]);

        return redirect("instructor/{$instructor->id}/conversation/{$conversation->id}");
    }

    public function view_conversation(Request $req, Course $course, User $instructor, Conversation $conversation){
        return view('pages.conversation.messages')->with([
            'instructor' => $instructor,
            'course' => $course,
            'conversation' => $conversation
        ]);
    }

    //
}
