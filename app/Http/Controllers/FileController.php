<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\File;
use App\Models\Folder;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use ZipArchive;

class FileController extends Controller
{


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return response()->json(File::all(), 200);
    }



    /**
     * Display a listing of the resource with
     * a specified extension.
     *
     * @return \Illuminate\Http\Response
     */
    public function getFilesByExt($ext)
    {
        return response()->json(File::all()->where('extension', $ext), 200);
    }


    
    /**
     * Display a listing of the files in a folder
     *
     * @return \Illuminate\Http\Response
     */
    public function folder($id)
    {
        $file = File::find($id);
        if ($file === null) {
            $json['content'] = null;
            $json['error'] = 'File not found';
            return response()->json($json, 404);
        }

        $json['error'] = null;
        $json['content'] = $file->folder;
        return response()->json($json, 200);
    }


    /**
     * Display the uploader of the current file
     *
     * @return \Illuminate\Http\Response
     */
    public function user($id)
    {
        $file = File::find($id);
        if ($file === null) {
            $json['username'] = null;
            $json['error'] = 'File not found';
            return response()->json($json, 404);
        }

        $json['error'] = null;
        $json['username'] = $file->user->name;
        return response()->json($json, 200);
    }



    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (Auth::user()->cant('create', File::class)) {
            $json['message'] = 'File not uploaded successfully';
            $json['error'] = 'Unauthorized';
            return response()->json($json, 403);
        }

        $validation = Validator::make($request->all(), [
            'name' => 'required|string|max:255|min:3|regex:/^[a-zA-Z0-9\s]+$/',
            'author' => 'nullable|string|max:255|min:2',
            'folder_id' => 'required|numeric|exists:folders,id',
            'file' => 'required|file'
        ]);

        if ($validation->fails()) {
            $json['message'] = 'File not uploaded successfully';
            $json['error'] = $validation->errors();
            return response()->json($json, 400);
        }

        $folder = Folder::find($request->input('folder_id'));
        $folderpath = $folder->storage_name;
        $filename = str_replace(' ', '_', $request->input('name'));
        $ext = $request->file->getClientOriginalExtension();

        do {
            $uid = $filename . uniqid();
        } while (Storage::exists("{$folderpath}/{$uid}.{$ext}"));
        
        $response = Storage::disk('local')->putFileAs($folderpath, $request->file('file'), "{$uid}.{$ext}");
        if (!$response) {
            $json['error'] = 'Server Error, contact the sysAdmin';
            $json['message'] = 'File not uploaded successfully';
            return response()->json($json, 500);
        }

        $file = new File();
        $file->uid = $uid;
        $file->name = $request->input('name');
        $file->author = $request->input('author') ?? 'anonymous';
        $file->extension = $ext;
        $file->influence = 0;
        $file->user_id = $request->user()->id;
        $file->folder_id = $request->input('folder_id');
        $response = $file->save();

        if (!$response) {
            Storage::disk('local')->delete("{$folderpath}/{$uid}.{$ext}");
            $json['error'] = 'Database Error, contact the sysAdmin';
            $json['message'] = 'File not uploaded successfully';
            return response()->json($json, 500);
        }

        $json['error'] = null;
        $json['message'] = 'File successfully uploaded';
        return response()->json($json, 200);
    }



    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $file = File::find($id);
        if ($file === null) {
            $json['error'] = 'File not found';
            $json['content'] = null;
            return response()->json($json, 404);
        }

        $file->increaseInfluence();
        $json['error'] = null;
        $json['content'] = $file;
        return response()->json($json, 200);
    }



    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $file = File::find($id);
        if ($file === null) {
            $json['message'] = 'File not updated successfully';
            $json['error'] = 'File not found';
            return response()->json($json, 404);
        }

        if (Auth::user()->cant('update', $file)) {
            $json['message'] = 'File not updated successfully';
            $json['error'] = 'Unauthorized';
            return response()->json($json, 403);
        }

        $validation = Validator::make($request->all(), [
            'name' => 'required_without_all:folder_id,author|string|max:255|min:3|regex:/^[a-zA-Z0-9\s]+$/',
            'author' => 'required_without_all:name,folder_id|nullable|string|max:255|min:2',
            'folder_id' => 'required_without_all:name, author|numeric|exists:folders,id',
        ]);

        if ($validation->fails()) {
            $json['message'] = 'File not updated successfully';
            $json['error'] = $validation->errors();
            return response()->json($json, 400);
        }

        $response = $file->update($request->all());
        if (!$response) {
            $json['message'] = 'File not updated successfully';
            $json['error'] = 'Database error, contact the sysAdmin';
            return response()->json($json, 500);
        }

        $json['error'] = null;
        $json['message'] = 'File successfully updated';
        return response()->json($json, 200);
    }



    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $file = File::find($id);
        if ($file === null) {
            $json['error'] = 'File not found';
            $json['message'] = 'File not deleted successfully';
            return response()->json($json, 404);
        }

        if (Auth::user()->cant('delete', $file)) {
            $json['error'] = 'Unauthorized';
            $json['message'] = 'File not deleted successfully';
            return response()->json($json, 403);
        }

        $folder = Folder::find($file->folder_id);
        $response = Storage::disk('local')->delete(
            "{$folder->storage_name}/{$file->uid}.{$file->extension}"
        );
        if (!$response) {
            $json['error'] = 'Server Error, contact the sysAdmin';
            $json['message'] = 'File not deleted successfully';
            return response()->json($json, 500);
        }

        $response = $file->delete();
        if (!$response) {
            $json['error'] = 'Database Error, contact the sysAdmin';
            $json['message'] = 'File not deleted successfully';
            return response()->json($json, 500);
        }

        $json['error'] = null;
        $json['message'] = 'File successfully deleted';
        return response()->json($json, 200);
    }



    /**
     * Download a file.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function downloadFile($id)
    {
        $file = File::find($id);
        if ($file === null) {
            return response()->json([
                'message' => 'File not found'
            ], 404);
        }

        $folder = Folder::find($file->folder_id);
        $path = "{$folder->storage_name}/{$file->uid}.{$file->extension}";
        return response()->download((storage_path("app/{$path}")));
    }



    /**
     * Stream a file.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function streamFile($id)
    {
        $file = File::find($id);
        if ($file === null) {
            return response()->json([
                'message' => 'File not found'
            ], 404);
        }

        $folder = Folder::find($file->folder_id);
        $path = "{$folder->storage_name}/{$file->uid}.{$file->extension}";
        return response()->file((storage_path("app/{$path}")));
    }
}
