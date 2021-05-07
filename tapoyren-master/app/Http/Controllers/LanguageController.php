<?php

namespace App\Http\Controllers;

use App\Language;
use App\LanguageTranslation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class LanguageController extends Controller
{

    public function list(Request $req)
    {
        $languages = Language::all();

        return response()->json(['languages' => $languages]);
    }

    public function add_language(Request $req)
    {
        $req->validate([
            'title' => 'required|string',
            'slug' => 'required|string'
        ]);

        Language::create([
            'title' => $req->title,
            'slug' => $req->slug,
        ]);

        return redirect('admin/languages');
    }

    public function view_add_translations(Request $req, Language $language)
    {
        return view('admin.languages.translations')->withLanguage($language);
    }

    public function add_translations(Request $req, $lang)
    {
        $validation = Validator::make($req->all(), [
            'string_translations' => 'array|nullable',
            'text_translations' => 'array|nullable'
        ]);
        if ($validation->fails()) return response()->json(['errors' => $validation->errors()], 401);

        $lang = Language::find($lang);

        foreach ($req->string_translations as $translation) {
            $thisKey = $lang->translations->where('type', 'string')->where('key', $translation['key']);

            if ($thisKey->count() === 0) {
                $lang->translations()->create([
                    'key' => $translation['key'],
                    'value' => $translation['value'],
                    'type' => 'string',
                    'language_id' => $lang->id
                ]);
            } elseif ($thisKey->count() === 1) $thisKey->first()->update(['value' => $translation['value']]);
        }

        foreach ($req->text_translations as $translation) {
            $thisKey = $lang->translations->where('type', 'text')->where('key', $translation['key']);

            if ($thisKey->count() === 0) {
                $lang->translations()->create([
                    'key' => $translation['key'],
                    'value' => $translation['value'],
                    'type' => 'text',
                    'language_id' => $lang->id
                ]);
            } elseif ($thisKey->count() === 1) $thisKey->first()->update(['value' => $translation['value']]);
        }


        return response()->json(['code' => 'done']);
        // 
    }

    public function get_data(Request $req, Language $language)
    {
        $strings = config('language.strings');
        $string_keys = [];
        foreach ($strings as $key => $value) array_push($string_keys, [
            'key' => $key,
            'name' => $value
        ]);

        $texts = config('language.texts');
        $text_keys = [];
        foreach ($texts as $key => $value) array_push($text_keys, [
            'key' => $key,
            'name' => $value,
        ]);

        $stringTranslations = LanguageTranslation::where('language_id', $language->id)->where('type', 'string')->get();
        $textTranslations = LanguageTranslation::where('language_id', $language->id)->where('type', 'text')->get();

        return response()->json([
            'language' => $language,
            'keys' => [
                'strings' => $string_keys,
                'texts' => $text_keys
            ],
            'translations' => [
                'strings' => $stringTranslations,
                'texts' => $textTranslations,
            ]
        ]);
    }

    public function delete_language(Request $req, Language $language)
    {
        $language->delete();

        return redirect('admin/languages');
    }

    //
}
