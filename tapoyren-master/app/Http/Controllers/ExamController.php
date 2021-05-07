<?php

namespace App\Http\Controllers;

use App\Course;
use App\CourseExam;
use App\CourseExamAnswer;
use App\CourseExamData;
use App\CourseExamDataAnswer;
use App\CourseExamQuestion;
use App\CourseVideo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class ExamController extends Controller
{
    public function add(Request $req, Course $course)
    {
        $validation = Validator::make($req->all(), [
            'title' => 'required|string',
            'description' => 'required|string',
            'minimumPoint' => 'required|numeric',
            'type' => 'required|in:time,timeless',
            'orderLectureId' => 'numeric|nullable',
            'time' => 'numeric|nullable',
        ]);
        if ($validation->fails()) return response()->json($validation->errors(), 401);



        $exam = $course->exams()->create([
            'title' => $req->title,
            'description' => $req->description,
            'minimum_point' => $req->minimumPoint,
            'order_lecture_id' => $req->orderLectureId,
            'type' => $req->type,
            'time' => $req->type === 'time' ? $req->time : null,
        ]);

        return response()->json(['code' => 'done', 'examId' => $exam->id]);
    }

    public function update(Request $req, Course $course, CourseExam $exam)
    {
        $validation = Validator::make($req->all(), [
            'title' => 'required|string',
            'description' => 'required|string',
            'minimumPoint' => 'required|numeric',
            'type' => 'required|in:time,timeless',
            'time' => 'numeric|nullable',
            'orderLectureId' => 'numeric|nullable',
        ]);
        if ($validation->fails()) return response()->json($validation->errors(), 401);

        $exam->update([
            'title' => $req->title,
            'description' => $req->description,
            'minimum_point' => $req->minimumPoint,
            'type' => $req->type,
            'time' => $req->type === 'time' ? $req->time : null,
            'order_lecture_id' => $req->orderLectureId
        ]);

        return response()->json(['code' => 'done']);
    }

    public function add_question(Request $req, Course $course, CourseExam $exam)
    {
        $validation = Validator::make($req->all(), [
            'title' => 'required|string',
            'topic' => 'nullable|string',
            'answerType' => 'required|in:single_choice,multiple_choice,assignment',
            'relatedLectureId' => 'required|numeric',
        ]);
        if ($validation->fails()) return response()->json($validation->errors(), 401);

        $question = $exam->questions()->create([
            'title' => $req->title,
            'topic' => $req->topic,
            'answer_type' => $req->answerType,
            'explanation' => $req->explanation,
            'question_vimeoVideoId' => $req->questionVVI,
            'explanation_vimeoVideoId' => $req->explanationVVI,
            'relatedLectureId' => $req->relatedLectureId
        ]);

        return response()->json(['code' => 'done', 'questionId' => $question->id]);
    }

    public function update_question(Request $req, Course $course, CourseExam $exam, CourseExamQuestion $question)
    {
        $validation = Validator::make($req->all(), [
            'title' => 'required|string',
            'topic' => 'nullable|string',
            'answerType' => 'required|in:single_choice,multiple_choice,assignment',
            'relatedLectureId' => 'required|numeric',
        ]);
        if ($validation->fails()) return response()->json($validation->errors(), 401);

        $question->update([
            'title' => $req->title,
            'topic' => $req->topic,
            'answer_type' => $req->answerType,
            'assignmentAnswer' => $req->assignmentAnswer,
            'explanation' => $req->explanation,
            'question_vimeoVideoId' => $req->questionVVI,
            'explanation_vimeoVideoId' => $req->explanationVVI,
            'relatedLectureId' => $req->relatedLectureId
        ]);

        return response()->json(['code' => 'done']);
    }

    public function remove_question(Request $req, Course $course, CourseExam $exam, CourseExamQuestion $question)
    {
        $question->delete();

        return response()->json(['code' => 'done']);
    }

    public function remove_answer(Request $req, Course $course, CourseExam $exam, CourseExamQuestion $question, CourseExamAnswer $answer)
    {
        $answer->delete();

        return response()->json(['code' => 'done']);
    }

    public function get_data(Request $req, Course $course, CourseExam $exam)
    {
        $questions = ['questions' => $exam->questions->map(function ($q) {
            return array_merge($q->toArray(), [
                'answers' => $q->answers
            ]);
        })->toArray()];

        $data = array_merge($exam->toArray(), $questions);

        return response()->json($data);
    }

    public function get_data_for_user(Request $req, Course $course, CourseExam $exam)
    {
        $questions = ['questions' => $exam->questions->map(function ($q) {

            $question = [
                'id' => $q->id,
                'title' => $q->title,
                'answer_type' => $q->answer_type,
                'explanation' => $q->explanation,
                'explanation_VVI' => $q->explanation_vimeoVideoId,
                'question_VVI' => $q->question_vimeoVideoId,
                'relatedLectureId' => $q->relatedLectureId,
                'relatedLectureTitle' => CourseVideo::find($q->relatedLectureId)->title,
            ];

            if ($q->answer_type === 'multiple_choice') {
                $answerIds = json_decode($q->multipleChoiceAnswerIds);
                $question['max_answer_count'] = count($answerIds);
            }

            $answers = $q->answers->map(function ($answer) {
                return ['id' => $answer->id, 'title' => $answer->title];
            });

            return array_merge($question, ['answers' => $answers->toArray()]);
        })->toArray()];

        $examData = [
            'title' => $exam->title,
            'description' => $exam->description,
            'minimum_point' => $exam->minimum_point,
            'type' => $exam->type,
        ];
        if ($exam->type === 'time') $examData['time'] = $exam->time;

        $data = array_merge($examData, $questions);

        return response()->json($data);
    }

    public function add_answer(Request $req, Course $course, CourseExam $exam, CourseExamQuestion $question)
    {
        $validation = Validator::make($req->all(), [
            'title' => 'required|string',
        ]);
        if ($validation->fails()) return response()->json($validation->errors(), 401);

        $answerId = null;

        if ($question->answer_type === 'assignment') {
            $question->update([
                'assignmentAnswer' => $req->title,
            ]);
        } else {
            $answer = $question->answers()->create([
                'title' => $req->title,
            ]);
            $answerId = $answer->id;
        }

        return response()->json(['code' => 'done', 'answerId' => $answerId]);
    }

    public function correct_answer(Request $req, Course $course, CourseExam $exam, CourseExamQuestion $question, CourseExamAnswer $answer)
    {
        $validation = Validator::make($req->all(), [
            'correct' => 'required|boolean',
        ]);
        if ($validation->fails()) return response()->json($validation->errors(), 401);

        if ($question->answer_type === 'single_choice') $question->update([
            'singleChoiceAnswerId' => $req->correct ? $answer->id : ''
        ]);
        elseif ($question->answer_type === 'multiple_choice') {
            $correctAnswers = (array) (json_decode($question->multipleChoiceAnswerIds) ?? []);

            if (!$req->correct && in_array($answer->id, $correctAnswers)) {
                $key = array_search($answer->id, $correctAnswers);
                unset($correctAnswers[$key]);
            } elseif ($req->correct && !in_array($answer->id, $correctAnswers)) array_push($correctAnswers, $answer->id);

            $question->update([
                'singleChoiceAnswerId' => null,
                'multipleChoiceAnswerIds' => json_encode(array_values($correctAnswers)),
            ]);

            //
        }

        return response()->json(['code' => 'done']);
    }

    public function reset_answers(Request $req, Course $course, CourseExam $exam, CourseExamQuestion $question)
    {
        $question->update([
            'singleChoiceAnswerId' => null,
            'multipleChoiceAnswerIds' => json_encode([]),
            'assignmentAnswer' => null,
            'assignmentExplanationVimeoVideoId' => null
        ]);

        return response()->json(['code' => 'done']);
    }

    public function delete_answers(Request $req, Course $course, CourseExam $exam, CourseExamQuestion $question)
    {
        $question->update([
            'singleChoiceAnswerId' => null,
            'multipleChoiceAnswerIds' => json_encode([]),
            'assignmentAnswer' => null,
        ]);
        $question->answers()->delete();

        return response()->json(['code' => 'done']);
    }

    public function update_answer(Request $req, Course $course, CourseExam $exam, CourseExamQuestion $question, CourseExamAnswer $answer)
    {
        $answer->update([
            'title' => $req->title
        ]);

        return response()->json(['code' => 'done']);
    }

    public function update_assignment_answer(Request $req, Course $course, CourseExam $exam, CourseExamQuestion $question)
    {
        $validation = Validator::make($req->all(), [
            'title' => 'nullable|string',
        ]);
        if ($validation->fails()) return response()->json($validation->errors(), 401);

        $question->update([
            'assignmentAnswer' => $req->title,
        ]);

        return response()->json(['code' => 'done']);
    }

    public function submit_answers(Request $req, $course, $courseExam)
    {
        $validation = Validator::make($req->all(), [
            'answers' => 'required|json',
            'times' => 'required|json'
        ]);
        if ($validation->fails()) return response()->json(['errors' => $validation->errors()], 401);

        $course = Course::find($course);
        $courseExam = CourseExam::find($courseExam);

        $totalExamTime = 0;

        $answers = json_decode($req->answers);
        $times = json_decode($req->times);


        $correctAnsweredQuestions = [];
        $wrongAnsweredQuestions = [];

        $wrongQuestionAnswers = [];

        foreach ($answers as $questionAnswer) {
            $originalQuestion = CourseExamQuestion::find($questionAnswer->question_id);

            if ($originalQuestion->answer_type === 'single_choice') {
                if ($questionAnswer->answer === $originalQuestion->singleChoiceAnswerId) array_push($correctAnsweredQuestions, $questionAnswer->question_id);
                else {
                    $wrongQuestionAnswers[$questionAnswer->question_id] = ['type' => 'single_choice', 'answer' => $questionAnswer->answer];
                    array_push($wrongAnsweredQuestions, $questionAnswer->question_id);
                }

                //
            } else if ($originalQuestion->answer_type === 'multiple_choice') {
                $originalAnswers = json_decode($originalQuestion->multipleChoiceAnswerIds);

                if (count($questionAnswer->answer) === count($originalAnswers)) {
                    $check = true;

                    foreach ($questionAnswer->answer as $checkAnswer) if (!in_array($checkAnswer, $originalAnswers)) $check = false;

                    if ($check) array_push($correctAnsweredQuestions, $questionAnswer->question_id);
                    else {
                        $wrongQuestionAnswers[$questionAnswer->question_id] = ['type' => 'multiple_choice', 'answer' => json_encode($questionAnswer->answer)];
                        array_push($wrongAnsweredQuestions, $questionAnswer->question_id);
                    }

                    //
                } else {
                    $wrongQuestionAnswers[$questionAnswer->question_id] = ['type' => 'multiple_choice', 'answer' => json_encode($questionAnswer->answer)];
                    array_push($wrongAnsweredQuestions, $questionAnswer->question_id);
                }

                //
            } else if ($originalQuestion->answer_type === 'assignment') {
                if (strtolower($questionAnswer->answer) === strtolower($originalQuestion->assignmentAnswer)) array_push($correctAnsweredQuestions, $questionAnswer->question_id);
                else {
                    $wrongQuestionAnswers[$questionAnswer->question_id] = ['type' => 'assignment', 'answer' => strtolower($questionAnswer->answer)];
                    array_push($wrongAnsweredQuestions, $questionAnswer->question_id);
                }
            }
        }

        $maximumPoints = $courseExam->questions->count();
        $studentPoints = count($correctAnsweredQuestions);

        $minimumPointPercent = (int) (($courseExam->minimum_point / $maximumPoints) * 100);
        $studentPointPercent = (int) (($studentPoints / $maximumPoints) * 100);

        $examStatus = ($studentPointPercent >= $minimumPointPercent) ? 'passed' : 'failed';

        $questionTimes = [];

        foreach ($times as $questionTime) {
            $questionTimes[$questionTime->question_id] = $questionTime->time;
            $totalExamTime += $questionTime->time;
        }

        $examData = CourseExamData::create([
            'course_exam_id' => $courseExam->id,
            'student_id' => $req->user('api')->id,
            'points' => $studentPoints,
            'total_exam_time' => $totalExamTime,
            'status' => $examStatus,
        ]);

        foreach ($correctAnsweredQuestions as $correctAnswered) CourseExamDataAnswer::create([
            'course_exam_data_id' => $examData->id,
            'course_exam_question_id' => $correctAnswered,
            'status' => 'correct',
            'time' => $questionTimes[$correctAnswered],
        ]);
        foreach ($wrongAnsweredQuestions as $wrongAnswered) CourseExamDataAnswer::create([
            'course_exam_data_id' => $examData->id,
            'course_exam_question_id' => $wrongAnswered,
            'status' => 'incorrect',
            'time' => $questionTimes[$wrongAnswered],
            'singleChoiceAnswerId' => $wrongQuestionAnswers[$wrongAnswered]['type'] === 'single_choice' ? $wrongQuestionAnswers[$wrongAnswered]['answer'] : null,
            'multipleChoiceAnswerIds' => $wrongQuestionAnswers[$wrongAnswered]['type'] === 'multiple_choice' ? $wrongQuestionAnswers[$wrongAnswered]['answer'] : null,
            'assignmentAnswer' => $wrongQuestionAnswers[$wrongAnswered]['type'] === 'assignment' ? $wrongQuestionAnswers[$wrongAnswered]['answer'] : null,
        ]);

        $wrongQuestionsData = [];

        $correctQuestionAnswers = $courseExam->questions->map(function ($eQuestion) {
            $data = [
                'id' => $eQuestion->id
            ];

            if ($eQuestion->answer_type === 'single_choice') $data['singleChoiceAnswerId'] = $eQuestion->singleChoiceAnswerId;
            else if ($eQuestion->answer_type === 'multiple_choice') $data['multipleChoiceAnswerIds'] = $eQuestion->multipleChoiceAnswerIds;
            else if ($eQuestion->answer_type === 'assignment') $data['assignmentAnswer'] = $eQuestion->assignmentAnswer;

            return $data;
        });

        collect($wrongAnsweredQuestions)->each(function ($wrongAnswered) use (&$wrongQuestionsData) {

            $eq = CourseExamQuestion::find($wrongAnswered);

            $relatedLecture = CourseVideo::find($eq->relatedLectureId);
            $relatedLecture = [
                'id' => $relatedLecture->id,
                'title' => $relatedLecture->title,
            ];
            $wrongQuestionsData[$wrongAnswered] = ['relatedLecture' => $relatedLecture, 'explanation' => $eq->explanation, 'explanationVVI' => $eq->explanation_vimeoVideoId];
        });

        return response()->json([
            'minimum_point_percent' => $minimumPointPercent,
            'student_point_percent' => $studentPointPercent,
            'exam_status' => $examStatus,
            'wrong_answered_questions' => $wrongAnsweredQuestions,
            'wrong_questions_data' => $wrongQuestionsData,
            'correct_question_answers' => $correctQuestionAnswers->toArray(),
            'total_exam_time' => $totalExamTime,
            'correctCount' => $courseExam->questions->count() - count($wrongAnsweredQuestions),
            'wrongCount' => count($wrongAnsweredQuestions)
        ]);


        //
    }

    public function admin_edit_exam(Request $req, Course $course, CourseExam $exam)
    {
        return view('admin.exam.edit')->with([
            'course' => $course,
            'exam' => $exam
        ]);
    }

    public function draft(Request $request, Course $course, CourseExam $exam)
    {
        $exam->update(['status' => 'deactive']);
        return redirect()->back();
    }

    public function publish(Request $request, Course $course, CourseExam $exam)
    {
        $exam->update(['status' => 'active']);
        return redirect()->back();
    }

    //
}
