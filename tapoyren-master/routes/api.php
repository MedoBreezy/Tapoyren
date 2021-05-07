<?php

if (isset($_SERVER['HTTP_ORIGIN'])) {
    switch ($_SERVER['HTTP_ORIGIN']) {
        case 'https://tapoyren.com':
            header('Access-Control-Allow-Origin: https://tapoyren.com');
            break;

        case 'https://greencard.az':
            header('Access-Control-Allow-Origin: https://greencard.az');
            break;

        default:
            break;
    }
}

use Illuminate\Http\Request;

Route::get('user', function (Request $request) {
    return $request->user();
})->middleware('auth:api');

Route::post('greencard/register', 'AuthController@register_from_greencard');

Route::get('translations', 'MainController@translations_api')->middleware('auth:api');

Route::middleware('auth:api')->group(function () {
    Route::get('course/{course}/exam/{exam}/take/data', 'ExamController@get_data_for_user');
    Route::post('course/{course}/exam/{exam}/take/submit', 'ExamController@submit_answers');
});

Route::middleware(['admin_api'])->group(function () {

    Route::post('language/{language}/translations', 'LanguageController@add_translations');

    Route::post('course/{course}/section/{section}/update', 'CourseController@update_section');
    Route::post('course/{course}/section/create', 'CourseController@create_section');
    Route::post('course/{course}/section/{section}/delete', 'CourseController@delete_section');
    Route::post('course/{course}/section/{section}/video/add', 'CourseController@add_video');
    Route::post('course/{course}/section/{section}/video/{video}/update', 'CourseController@update_video');
    Route::post('course/{course}/section/{section}/video/{video}/delete', 'CourseController@delete_video');
    Route::post('course/{course}/resource/add', 'CourseController@add_resource');
    Route::post('course/{course}/resource/{resource}/remove', 'CourseController@remove_resource');

    Route::post('course/{course}/exam/add', 'ExamController@add');
    Route::post('course/{course}/exam/{exam}/update', 'ExamController@update');
    Route::post('course/{course}/exam/{exam}/question/add', 'ExamController@add_question');
    Route::get('course/{course}/exam/{exam}/data', 'ExamController@get_data');


    Route::post('course/{course}/lesson/{lesson}/resource/add', 'CourseResourceController@lesson_resource_add');

    Route::post('course/{course}/exam/{exam}/question/{question}/update', 'ExamController@update_question');
    Route::post('course/{course}/exam/{exam}/question/{question}/remove', 'ExamController@remove_question');
    Route::post('course/{course}/exam/{exam}/question/{question}/answer/add', 'ExamController@add_answer');
    Route::post('course/{course}/exam/{exam}/question/{question}/assignment_answer/update', 'ExamController@update_assignment_answer');
    Route::post('course/{course}/exam/{exam}/question/{question}/answer/{answer}/correct', 'ExamController@correct_answer');
    Route::post('course/{course}/exam/{exam}/question/{question}/answer/{answer}/remove', 'ExamController@remove_answer');
    Route::post('course/{course}/exam/{exam}/question/{question}/answers/reset', 'ExamController@reset_answers');
    Route::post('course/{course}/exam/{exam}/question/{question}/answers/delete', 'ExamController@delete_answers');
    Route::post('course/{course}/exam/{exam}/question/{question}/answer/{answer}/update', 'ExamController@update_answer');


    Route::get('course/list', 'CourseController@list');
    Route::get('languages/list', 'LanguageController@list');
    Route::get('course/{course}/sections/list', 'CourseController@sections');
    Route::get('category/list', 'CategoryController@list');
    Route::get('instructor/list', 'InstructorController@list');

    Route::post('course/add', 'CourseController@add');
    Route::post('course/{course}/update', 'CourseController@update');
    Route::post('course/{course}/update_data', 'CourseController@update_data');
    Route::get('course/{course}/data', 'CourseController@get_data');

    Route::get('language/{language}/data', 'LanguageController@get_data');

    Route::post('course/{course}/add_data', 'CourseController@add_data');
    Route::post('upload/image', 'FileController@uploadImage');
    Route::post('upload/video', 'FileController@uploadVideo');
    Route::post('upload/doc', 'FileController@uploadDoc');

    Route::post('faq/add', 'FaqController@add');
    Route::post('faq/{faq}/update', 'FaqController@update');
    Route::get('faq/{faq}', 'FaqController@faq_info');

    Route::get('instructor/{instructor}', 'InstructorController@instructor_info');
    Route::post('instructor/{instructor}/update', 'InstructorController@update');

    //
});

Route::post('upload/editor/image', 'FileController@uploadEditorImage');

Route::get('categories/all_data', function () {
    return response()->json(['categories' => allCategoryData()]);
});
