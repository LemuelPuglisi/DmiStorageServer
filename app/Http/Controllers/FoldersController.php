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
            'display_name' => 'required|string|alpha_num|max:255|unique:folders,display_name',
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
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }



    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
