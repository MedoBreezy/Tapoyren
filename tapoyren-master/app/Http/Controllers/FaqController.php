<?php

namespace App\Http\Controllers;

use App\FaqQuestion;
use App\FaqQuestionTranslation;
use App\Language;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class FaqController extends Controller
{
    public function add(Request $req)
    {
        $langs = Language::all()->map(function ($lang) {
            return $lang->slug;
        })->toArray();

        $validationKeys = [];
        foreach ($langs as $lang) {
            $validationKeys['title_' . $lang] = 'required|string';
            $validationKeys['description_' . $lang] = 'required|string';
        }

        $validator = Validator::make($req->all(), $validationKeys);
        if ($validator->fails()) return response()->json(['errors' => $validator->errors()], 401);

        $faq = FaqQuestion::create([]);

        foreach (Language::all() as $lang) FaqQuestionTranslation::create([
            'faq_question_id' => $faq->id,
            'language_id' => $lang->id,
            'question' => $req->{"title_" . $lang->slug},
            'description' => $req->{"description_" . $lang->slug}
        ]);

        return response()->json(['code' => 'done']);
    }

    public function faq_info(Request $req, FaqQuestion $faq)
    {
        $data = [];

        foreach (Language::all() as $lang) {
            $translation = $faq->translations->where('language_id', $lang->id)->first();
            $data[$lang->slug] = [
                'title' => $translation->question,
                'description' => $translation->description,
            ];
        }

        return response()->json(['faq' => $data]);
    }

    public function update(Request $req, FaqQuestion $faq)
    {
        $langs = Language::all()->map(function ($lang) {
            return $lang->slug;
        })->toArray();

        $validationKeys = [];
        foreach ($langs as $lang) {
            $validationKeys['title_' . $lang] = 'required|string';
            $validationKeys['description_' . $lang] = 'required|string';
        }

        $validator = Validator::make($req->all(), $validationKeys);
        if ($validator->fails()) return response()->json(['errors' => $validator->errors()], 401);

        foreach (Language::all() as $lang) $faq->translations->where('language_id', $lang->id)->first()->update([
            'question' => $req->{"title_" . $lang->slug},
            'description' => $req->{"description_" . $lang->slug}
        ]);

        return response()->json(['code' => 'done']);
    }

    public function view_update(Request $req, FaqQuestion $faq)
    {
        return view('admin.faq.update')->with(['faq' => $faq]);
    }

    public function delete(Request $req, FaqQuestion $faq)
    {
        $faq->delete();

        return redirect('admin/faq');
    }
    //
}
