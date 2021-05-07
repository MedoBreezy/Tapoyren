<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\Facades\Image;

class FileController extends Controller
{

    public function uploadImage(Request $req)
    {
        $validation = Validator::make($req->all(), [
            'file' => 'required|image|mimes:png,jpg,jpeg'
        ]);
        $quality = 60;

        if ($validation->fails()) return response()->json(['errors' => $validation->errors()], 401);

        $file = $req->file('file');


        $path = Storage::putFile('public/images', $file, 'public');

        $imagePath = storage_path('app/' . $path);

        $img = Image::make($imagePath);
        $img->save($imagePath, $quality);

        $path = str_replace('public/', '', $path);

        $path = url('uploads/' . $path);


        return response()->json(['path' => $path], 200);

        //
    }

    public function uploadEditorImage(Request $req)
    {
        $validation = Validator::make($req->all(), [
            'file' => 'required|image|mimes:png,jpg,jpeg'
        ]);
        $quality = 60;

        if ($validation->fails()) return response()->json(['errors' => $validation->errors()], 401);

        $file = $req->file('file');


        $path = Storage::putFile('public/images', $file, 'public');

        $imagePath = storage_path('app/' . $path);

        $img = Image::make($imagePath);
        $img->save($imagePath, $quality);

        $path = str_replace('public/', '', $path);

        $path = url('uploads/' . $path);


        return response()->json(['location' => $path], 200);

        //
    }

    public function uploadVideo(Request $req)
    {

        $validation = Validator::make($req->all(), [
            'file' => 'required|mimes:mp4'
        ]);

        if ($validation->fails()) return response()->json(['errors' => $validation->errors()], 401);

        $file = $req->file('file');

        $path = Storage::putFile('public/videos', $file, 'public');

        $path = str_replace('public/', '', $path);

        $path = url('uploads/' . $path);

        return response()->json(['path' => $path], 200);

        //
    }

    public function uploadDoc(Request $req)
    {

        $validation = Validator::make($req->all(), [
            'file' => 'required|mimes:pdf,docx,xlsx,rar,zip'
        ]);

        if ($validation->fails()) return response()->json(['errors' => $validation->errors()], 401);

        $file = $req->file('file');

        $path = Storage::putFile('public/docs', $file, 'public');

        $path = str_replace('public/', '', $path);

        $path = url('uploads/' . $path);

        return response()->json(['path' => $path], 200);

        //
    }

    //
}
