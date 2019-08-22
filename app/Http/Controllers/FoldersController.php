<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Folder;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class FoldersController extends Controller
{


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return response()->json(Folder::all(), 200);
    }

    
    /**
     * Return the course of the folder.
     *
     * @return \Illuminate\Http\Response
     */
    public function course($id)
    {
        $jsonResponse = [
            'content' => null,
            'error' => null
        ];

        $folder = Folder::find($id);
        if ($folder === null) {
            $jsonResponse['error'] = 'Folder not found';
            return response()->json($jsonResponse, 404);
        }

        $jsonResponse['content'] = $folder->course;
        return response()->json($jsonResponse, 200);
    }
    
    
    
    public function files($id)
    {
        $jsonResponse = [
            'content' => null,
            'error' => null
        ];

        $folder = Folder::find($id);
        if ($folder === null) {
            $jsonResponse['error'] = 'Folder not found';
            return response()->json($jsonResponse, 404);
        }

        $jsonResponse['content'] = $folder->files;
        return response()->json($jsonResponse, 200);
    }



    /**
     * Return the parent folder of the folder.
     *
     * @return \Illuminate\Http\Response
     */
    public function parent($id)
    {
        $jsonResponse = [
            'content' => null,
            'error' => null
        ];

        $folder = Folder::find($id);
        if ($folder === null) {
            $jsonResponse['error'] = 'Folder not found';
            return response()->json($jsonResponse, 404);
        }

        $jsonResponse['content'] = $folder->parent;
        return response()->json($jsonResponse, 200);
    }


    /**
     * Return the subfolders of the folder.
     *
     * @return \Illuminate\Http\Response
     */
    public function subfolders($id)
    {
        $jsonResponse = [
            'content' => null,
            'error' => null
        ];

        $folder = Folder::find($id);
        if ($folder === null) {
            $jsonResponse['error'] = 'Folder not found';
            return response()->json($jsonResponse, 404);
        }

        $jsonResponse['content'] = $folder->subfolders;
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
            'display_name' => 'required|string|regex:/^[a-zA-Z0-9\s]+$/|max:255|unique:folders,display_name',
            'subfolder_of' => 'numeric|nullable|exists:folders,id',
            'course_id' => 'required|numeric|exists:courses,id'
        ]);
    
        if ($validation->fails()) {
            $jsonResponse['message'] = $validation->errors();
            return response()->json($jsonResponse, 400);
        }

        $validStorageName = str_replace(' ', '_', $request->input('display_name'));
        $response = Storage::disk('local')->makeDirectory($validStorageName);

        if (!$response) {
            $jsonResponse['message'] = 'Server Error, contact the sysAdmin';
            return response()->json($jsonResponse, 500);
        }

        $folder = new Folder();
        $folder->display_name = $request->input('display_name');
        $folder->storage_name = $validStorageName;
        $folder->influence = 0;
        $folder->subfolder_of = $request->input('subfolder_of') ?? null;
        $folder->course_id = $request->input('course_id');
        $response = $folder->save();

        if (!$response) {
            $jsonResponse['message'] = 'Database Error, contact the sysAdmin';
            return response()->json($jsonResponse, 500);
        }

        $jsonResponse['message'] = 'Folder successfully created';
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

        $folder = Folder::find($id);
        if ($folder === null) {
            $jsonResponse['error'] = 'Folder not found';
            return response()->json($jsonResponse, 404);
        }

        $folder->increaseInfluence();
        $jsonResponse['content'] = $folder;
        return response()->json($jsonResponse, 200);
    }



    /**
     * Update the specified resource in storage.
     * If subfolder_of param is -1, then the folder will be
     * updated to a root folder of the course.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $jsonResponse = ['message' => null];

        $folder = Folder::find($id);
        if ($folder === null) {
            $jsonResponse['message'] = 'Folder not found';
            return response()->json($jsonResponse, 404);
        }

        $validation = Validator::make($request->all(), [
            'display_name' => 'required_without_all:subfolder_of,course_id|string|regex:/^[a-zàèéìòùA-Z0-9\s]+$/|max:255|unique:folders,display_name',
            'subfolder_of' => 'required_without_all:display_name,course_id|numeric|nullable',
            'course_id' => 'required_without_all:display_name,subfolder_of|numeric|exists:courses,id'
        ]);

        if ($validation->fails()) {
            $jsonResponse['message'] = $validation->errors();
            return response()->json($jsonResponse, 400);
        }

        if ($request->input('subfolder_of')) {
            $id = $request->input('subfolder_of');
            if ($id == -1) {
                $folder->subfolder_of = null;
            } elseif ($folder->id == $id || !Folder::find($id)) {
                $jsonResponse['message'] = 'This folder cannot be a subfolder of itself or of folders that doesn\'t exists';
                return response()->json($jsonResponse, 400);
            } else {
                $folder->subfolder_of = $id;
            }
        }

        if ($request->input('display_name')) {
            $folder->display_name = $request->input('display_name');
            $validStorageName = str_replace(' ', '_', $request->input('display_name'));
            if (!Storage::disk('local')->exists($validStorageName)) {
                $response = Storage::disk('local')->move($folder->storage_name, $validStorageName);
                if (!$response) {
                    $jsonResponse['message'] = 'Server Error, contact the sysAdmin';
                    return response()->json($jsonResponse, 500);
                } else {
                    $folder->storage_name = $validStorageName;
                }
            } else {
                $jsonResponse['message'] = 'This folder name conflicts with server storage folders.';
                return response()->json($jsonResponse, 500);
            }
        }

        if ($request->input('course_id')) {
            $folder->course_id = $request->input('course_id');
        }

        $response = $folder->save();

        if (!$response) {
            $jsonResponse['message'] = 'Database Error, contact the SysAdmin';
            return response()->json($jsonResponse, 500);
        }

        $jsonResponse['message'] = 'Folder updated successfully';
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

        $folder = Folder::find($id);
        if ($folder === null) {
            $jsonResponse['message'] = 'Folder not found';
            return response()->json($jsonResponse, 404);
        }

        if (Storage::disk('local')->exists($folder->storage_name)) {
            $response = Storage::disk('local')->deleteDirectory($folder->storage_name);
            if (!$response) {
                $jsonResponse['message'] = 'Server error, contact the sysAdmin';
                return response()->json($jsonResponse, 500);
            }
        }

        $response = $folder->delete();
        if (!$response) {
            $jsonResponse['message'] = 'Database error, contact the sysAdmin';
            return response()->json($jsonResponse, 500);
        }

        $jsonResponse['message'] = 'The folder has been successfully removed';
        return response()->json($jsonResponse, 200);
    }
}
