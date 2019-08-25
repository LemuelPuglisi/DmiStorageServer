<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\File;
use App\Models\Folder;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use ZipArchive;

class FilesController extends Controller
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
        $jsonResponse = [
            'content' => null,
            'error' => null
        ];

        $file = File::find($id);
        if ($file === null) {
            $jsonResponse['error'] = 'File not found';
            return response()->json($jsonResponse, 404);
        }

        $jsonResponse['content'] = $file->folder;
        return response()->json($jsonResponse, 200);
    }



    public function user($id) 
    {
        $jsonResponse = [
            'username' => null,
            'error' => null
        ];

        $file = File::find($id);
        if ($file === null) {
            $jsonResponse['error'] = 'File not found';
            return response()->json($jsonResponse, 404);
        }

        $jsonResponse['username'] = $file->user->name; 
        return response()->json($jsonResponse, 200);
    }



    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $jsonResponse = ['message' => null];

        $validation = Validator::make($request->all(), [
            'name' => 'required|string|max:255|min:3|regex:/^[a-zA-Z0-9\s]+$/',
            'author' => 'nullable|string|max:255|min:2',
            'folder_id' => 'required|numeric|exists:folders,id',
            'file' => 'required|file'
        ]);

        if ($validation->fails()) {
            $jsonResponse['message'] = $validation->errors();
            return response()->json($jsonResponse, 400);
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
            $jsonResponse['message'] = 'Server Error, contact the sysAdmin';
            return response()->json($jsonResponse, 500);
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
            $jsonResponse['message'] = 'Database Error, contact the sysAdmin';
            return response()->json($jsonResponse, 500);
        }

        $jsonResponse['message'] = 'File successfully uploaded';
        return response()->json($jsonResponse, 200);
    }



    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $jsonResponse = [
            'content' => null,
            'error' => null
        ];

        $file = File::find($id);
        if ($file === null) {
            $jsonResponse['error'] = 'File not found';
            return response()->json($jsonResponse, 404);
        }

        $file->increaseInfluence();
        $jsonResponse['content'] = $file;
        return response()->json($jsonResponse, 200);
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
        $jsonResponse = ['message' => null];

        $file = File::find($id);
        if ($file === null) {
            $jsonResponse['message'] = 'File not found';
            return response()->json($jsonResponse, 404);
        }

        $validation = Validator::make($request->all(), [
            'name' => 'required_without_all:folder_id,author|string|max:255|min:3|regex:/^[a-zA-Z0-9\s]+$/',
            'author' => 'required_without_all:name,folder_id|nullable|string|max:255|min:2',
            'folder_id' => 'required_without_all:name, author|numeric|exists:folders,id',
        ]);

        if ($validation->fails()) {
            $jsonResponse['message'] = $validation->errors();
            return response()->json($jsonResponse, 400);
        }

        $response = $file->update($request->all());
        if (!$response) {
            $jsonResponse['message'] = 'Database error, contact the sysAdmin';
            return response()->json($jsonResponse, 500);
        }

        $jsonResponse['message'] = 'File successfully updated';
        return response()->json($jsonResponse, 200);
    }



    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $jsonResponse = ['message' => null];

        $file = File::find($id);
        if ($file === null) {
            $jsonResponse['message'] = 'File not found';
            return response()->json($jsonResponse, 404);
        }

        $folder = Folder::find($file->folder_id);
        $response = Storage::disk('local')->delete(
            "{$folder->storage_name}/{$file->uid}.{$file->extension}"
        );
        if (!$response) {
            $jsonResponse['message'] = 'Server Error, contact the sysAdmin';
            return response()->json($jsonResponse, 500);
        }

        $response = $file->delete();
        if (!$response) {
            $jsonResponse['message'] = 'Database Error, contact the sysAdmin';
            return response()->json($jsonResponse, 500);
        }

        $jsonResponse['message'] = 'File successfully deleted';
        return response()->json($jsonResponse, 200);
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
