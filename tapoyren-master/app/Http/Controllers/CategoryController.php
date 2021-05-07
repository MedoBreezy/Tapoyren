<?php

namespace App\Http\Controllers;

use App\Category;
use App\CategoryTranslation;
use App\Language;
use Illuminate\Http\Request;

class CategoryController extends Controller
{

   public function list(Request $req)
   {

      $data = Category::where('parent_id', null)->get()->map(function ($category) {

         $subCategories = $category->sub_categories->map(function ($subCat) {

            return [
               'id' => $subCat->id,
               'title' => $subCat->__('title'),
            ];
         })->toArray();

         return [
            'id' => $category->id,
            'title' => $category->__('title'),
            'sub_categories' => $subCategories
         ];
      });

      return response()->json(['categories' => $data]);
   }

   public function add(Request $req)
   {
      $langs = Language::all()->map(function ($lang) {
         return $lang->slug;
      })->toArray();

      $validationKeys = [];
      foreach ($langs as $lang) $validationKeys['title_' . $lang] = 'required|string';

      $req->validate($validationKeys);

      $category = Category::create([
         'parent_id' => $req->parent_id
      ]);

      foreach (Language::all() as $lang) CategoryTranslation::create([
         'category_id' => $category->id,
         'language_id' => $lang->id,
         'title' => $req->{"title_" . $lang->slug}
      ]);

      return redirect('/admin/categories');
   }

   public function delete(Request $req, Category $category)
   {
      $category->delete();
      return redirect('/admin');
   }

   //
}
