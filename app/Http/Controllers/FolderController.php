<?php
/**
 *  All the functions returns a json response.
 */
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Folder;
use App\Models\File;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class FolderController extends Controller
{
    public function index()
    {
        return response()->json(Folder::all(), 200);
    }


    public function course($id)
    {
        $folder = Folder::find($id);
        if ($folder === null) {
            $json['content'] = null;
            $json['error'] = 'Folder not found';
            return response()->json($json, 404);
        }

        $json['error'] = null;
        $json['content'] = $folder->course;
        return response()->json($json, 200);
    }
    
   
    public function files($id)
    {
        $folder = Folder::find($id);
        if ($folder === null) {
            $json['content'] = null;
            $json['error'] = 'Folder not found';
            return response()->json($json, 404);
        }

        $json['error'] = null;
        $json['content'] = $folder->files;
        return response()->json($json, 200);
    }


    public function orderedFiles($id, $param, $order)
    {
        $folder = Folder::find($id);
        if ($folder === null) {
            $json['content'] = null;
            $json['error'] = 'Folder not found';
            return response()->json($json, 404);
        }

        if (!in_array($param, File::$sortableFields) || ($order !== 'desc' && $order !== 'asc')) {
            $json['content'] = null;
            $json['error'] = 'Params must be id, influence or timestamps and order must be desc or asc';
            return response()->json($json, 400);
        }

        $json['error'] = null;
        $json['content'] = $folder->orderedFiles($param, $order);
        return response()->json($json, 200);
    }

    
    public function getFileByExt($id, $ext)
    {
        $folder = Folder::find($id);
        if ($folder === null) {
            $json['content'] = null;
            $json['error'] = 'Folder not found';
            return response()->json($json, 404);
        }

        $json['error'] = null;
        $json['content'] = $folder->files->where('extension', $ext);
        return response()->json($json, 200);
    }


    public function parent($id)
    {
        $folder = Folder::find($id);
        if ($folder === null) {
            $json['content'] = null;
            $json['error'] = 'Folder not found';
            return response()->json($json, 404);
        }

        $json['error'] = null;
        $json['content'] = $folder->parent;
        return response()->json($json, 200);
    }


    public function subfolders($id)
    {
        $folder = Folder::find($id);
        if ($folder === null) {
            $json['content'] = null;
            $json['error'] = 'Folder not found';
            return response()->json($json, 404);
        }

        $json['error'] = null;
        $json['content'] = $folder->subfolders;
        return response()->json($json, 200);
    }


    public function user($id)
    {
        $folder = Folder::find($id);
        if ($folder === null) {
            $json['username'] = null;
            $json['error'] = 'Folder not found';
            return response()->json($json, 404);
        }

        $json['error'] = null;
        $json['username'] = $folder->user->name;
        return response()->json($json, 200);
    }


    public function store(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'display_name' => 'required|string|regex:/^[a-zA-Z0-9\s]+$/|max:255|unique:folders,display_name',
            'subfolder_of' => 'numeric|nullable|exists:folders,id',
            'course_id' => 'required|numeric|exists:courses,id'
        ]);
    
        if ($validation->fails()) {
            $json['message'] = 'Folder not created successfully';
            $json['error'] = $validation->errors();
            return response()->json($json, 400);
        }

        if (Auth::user()->cant('create', [Folder::class, $request->course_id])) {
            $json['message'] = 'Folder not created successfully';
            $json['error'] = 'Unauthorized';
            return response()->json($json, 403);
        }

        $parent = Folder::find($request->subfolder_of); 
        if ($parent->course_id != $request->course_id) {
            $json['message'] = 'Folder not created successfully'; 
            $json['error'] = 'Parent folder must be on the same course.'; 
            return response()->json($json, 400); 
        }

        $validStorageName = str_replace(' ', '_', $request->input('display_name'));
        $response = Storage::disk('local')->makeDirectory($validStorageName);

        if (!$response) {
            $json['message'] = 'Folder not created successfully';
            $json['error'] = 'Server Error, contact the sysAdmin';
            return response()->json($json, 500);
        }

        $folder = new Folder();
        $folder->display_name = $request->input('display_name');
        $folder->storage_name = $validStorageName;
        $folder->influence = 0;
        $folder->subfolder_of = $request->input('subfolder_of') ?? null;
        $folder->course_id = $request->input('course_id');
        $folder->creator_id = Auth::user()->id;
        $response = $folder->save();

        if (!$response) {
            $json['message'] = 'Folder not created successfully';
            $json['error'] = 'Database Error, contact the sysAdmin';
            return response()->json($json, 500);
        }

        $json['message'] = 'Folder successfully created';
        $json['error'] = null;
        return response()->json($json, 200);
    }


    public function show($id)
    {
        $folder = Folder::find($id);
        if ($folder === null) {
            $json['content'] = null;
            $json['error'] = 'Folder not found';
            return response()->json($json, 404);
        }

        $folder->increaseInfluence();
        $json['error'] = null;
        $json['content'] = $folder;
        return response()->json($json, 200);
    }


    public function update(Request $request, $id)
    {
        $folder = Folder::find($id);
        if ($folder === null) {
            $json['message'] = 'Folder not updated successfully';
            $json['error'] = 'Folder not found';
            return response()->json($json, 404);
        }

        if (Auth::user()->cant('update', $folder)) {
            $json['message'] = 'Folder not updated successfully';
            $json['error'] = 'Unauthorized';
            return response()->json($json, 403);
        }

        $validation = Validator::make($request->all(), [
            'display_name' => 'required_without_all:subfolder_of,course_id|string|regex:/^[a-zàèéìòùA-Z0-9\s]+$/|max:255|unique:folders,display_name',
            'subfolder_of' => 'required_without_all:display_name,course_id|numeric|nullable',
            'course_id' => 'required_without_all:display_name,subfolder_of|numeric|exists:courses,id'
        ]);

        if ($validation->fails()) {
            $json['message'] = 'Folder not updated successfully';
            $json['error'] = $validation->errors();
            return response()->json($json, 400);
        }

        if ($request->input('subfolder_of')) {
            $id = $request->input('subfolder_of');
            if ($id == -1) {
                $folder->subfolder_of = null;
            } elseif ($folder->id == $id || !Folder::find($id)) {
                $json['message'] = 'Folder not updated successfully';
                $json['error'] = 'This folder cannot be a subfolder of itself or of folders that doesn\'t exists';
                return response()->json($json, 400);
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
                    $json['error'] = 'Server Error, contact the sysAdmin';
                    $json['message'] = 'Folder not updated successfully';
                    return response()->json($json, 500);
                } else {
                    $folder->storage_name = $validStorageName;
                }
            } else {
                $json['error'] = 'This folder name conflicts with server storage folders.';
                $json['message'] = 'Folder not updated successfully';
                return response()->json($json, 500);
            }
        }

        if ($request->input('course_id')) {
            $folder->course_id = $request->input('course_id');
        }

        $response = $folder->save();

        if (!$response) {
            $json['error'] = 'Database Error, contact the SysAdmin';
            $json['message'] = 'Folder not updated successfully';
            return response()->json($json, 500);
        }

        $json['error'] = null;
        $json['message'] = 'Folder updated successfully';
        return response()->json($json, 200);
    }


    public function destroy($id)
    {
        $folder = Folder::find($id);
        if ($folder === null) {
            $json['error'] = 'Folder not found';
            $json['message'] = 'Folder not deleted successfully';
            return response()->json($json, 404);
        }

        if (Storage::disk('local')->exists($folder->storage_name)) {
            $response = Storage::disk('local')->deleteDirectory($folder->storage_name);
            if (!$response) {
                $json['message'] = 'Folder not deleted successfully';
                $json['error'] = 'Server error, contact the sysAdmin';
                return response()->json($json, 500);
            }
        }

        $response = $folder->delete();
        if (!$response) {
            $json['message'] = 'Folder not deleted successfully';
            $json['error'] = 'Database error, contact the sysAdmin';
            return response()->json($json, 500);
        }

        $json['error'] = null;
        $json['message'] = 'The folder has been successfully removed';
        return response()->json($json, 200);
    }


    public function requests($id, Request $request)
    {
        $folder = Folder::find($id);
        if ($folder === null) {
            $json['error'] = 'Folder not found';
            $json['content'] = null;
            return response()->json($json, 404);
        }

        if (Auth::user()->cant('getRequests', Folder::class)) {
            $json['error'] = 'Unauthorized';
            $json['content'] = null;
            return response()->json($json, 403);
        }

        $validation = Validator::make($request->all(), [
            'status' => 'string|in:pending,active,refused,expired'
        ]);

        if ($validation->fails()) {
            $json['content'] = null;
            $json['error'] = $validation->errors();
            return response()->json($json, 400);
        }

        $json['error'] = null;
        $json['content'] = null;

        if (!$request->has('status')) {
            $json['content'] = $folder->requests;
        } else {
            $json['content'] = $folder->requestsByStatus($request->status);
        }
        
        return response()->json($json, 200);
    }
}
