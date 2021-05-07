<?php

use App\Category;
use App\Course;
use App\CourseVideoWatched;
use App\Language;

if (!function_exists('translate')) {

    function translate($key)
    {
        $locale = app()->getLocale();
        $language = Language::where('slug', $locale)->first();
        $translation = $language ? $language->translations
            ->where('key', $key)->first() : null;

        if ($translation) return $translation->value;
        else return null;
    }

    //
}

if (!function_exists('priceSymbolByLocale')) {

    function priceSymbolByLocale()
    {
        //   $locale = app()->getLocale();

        $symbol = '₼';

        //   if ($locale === 'en') $symbol = '$';

        return $symbol;
    }

    //
}

if (!function_exists('allCategoryData')) {

    function allCategoryData()
    {
        $categories = Category::where('parent_id', null)->get();

        $categories = $categories->map(function ($category) {

            $subCategories = $category->sub_categories->map(function ($subCat) {

                $courses = Course::where('category_id', $subCat->id)->isActive()->get();

                return [
                    'id' => $subCat->id,
                    'title' => $subCat->__('title'),
                    'courses' => $courses
                ];
            });

            return [
                'id' => $category->id,
                'title' => $category->__('title'),
                'sub_categories' => $subCategories
            ];
        });

        return $categories->toArray();
    }

    //
}
