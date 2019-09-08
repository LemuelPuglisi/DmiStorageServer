<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\FolderRequest;
use App\Models\Folder;

class FolderRequestController extends Controller
{
    private function checkJsonSchema(string $json)
    {
        $data = json_decode($json, true);

        if (!isset($data['manage']) || !is_bool($data['manage']) ||
            !isset($data['remove']) || !is_bool($data['remove'])) {
            return false;
        }

        if (!$data['manage'] && !$data['remove']) {
            return false;
        }
        return true;
    }
    
    /**
     * All this functions @return Response
     */

    public function index()
    {
        if (Auth::user()->cant('viewAll', FolderRequest::class)) {
            $json['error'] = 'Unauthorized';
            $json['content'] = null;
            return response()->json($json, 403);
        }

        $json['error'] = null;
        $json['content'] = FolderRequest::all()->each(function ($request) {
            $request->permissions = json_decode($request->permissions, true);
        });
        return response()->json($json, 200);
    }


    public function store(Request $request)
    {
        $json['error'] = null;
        $json['message'] = 'Folder request not stored successfully';

        if (Auth::user()->cant('create', FolderRequest::class)) {
            $json['error'] = 'Unauthorized';
            return response()->json($json, 403);
        }

        $validation = Validator::make($request->all(), [
            'folder_id' => 'required|numeric|exists:folders,id',
            'permissions' => 'required|json',
            'notes' => 'required|string|between:10,400',
            'lifespan' => 'required|numeric|between:7,365'
        ]);

        if ($validation->fails()) {
            $json['error'] = $validation->errors();
            return response()->json($json, 400);
        }

        if (!$this->checkJsonSchema($request->permissions)) {
            $json['error'] = 'Invalid permissions json schema';
            return response()->json($json, 400);
        }

        $existingRequest = FolderRequest::where('user_id', Auth::user()->id)
        ->where('folder_id', $request->folder_id)->get();

        if (!$existingRequest->isEmpty()) {
            $json['error'] = 'You already have the permissions to this folder';
            return response()->json($json, 400);
        }

        $folderRequest = new FolderRequest;
        $folderRequest->status = 'pending';
        $folderRequest->folder_id = $request->folder_id;
        $folderRequest->user_id = Auth::user()->id;
        $folderRequest->permissions = $request->permissions;
        $folderRequest->notes = $request->notes;
        $folderRequest->lifespan = $request->lifespan;
        $response = $folderRequest->save();
    
        if (!$response) {
            $json['error'] = 'Database error, contact the sysAdmin';
            return response()->json($json, 500);
        }

        $json['message'] = 'Folder request successfully stored';
        return response()->json($json, 200);
    }


    public function show($id)
    {
        $request = FolderRequest::find($id);
        if ($request === null) {
            $json['error'] = 'Folder request not found';
            $json['content'] = null;
            return response()->json($json, 404);
        }

        if (Auth::user()->cant('view', $request)) {
            $json['error'] = 'Unauthorized';
            $json['content'] = null;
            return response()->json($json, 403);
        }

        $request->permissions = json_decode($request->permissions, true);
        $json['error'] = null;
        $json['content'] = $request;
        return response()->json($json, 200);
    }


    
    public function upgrade(Request $request, $id)
    {
        $json['error'] = null;
        $json['message'] = 'Upgrade not sent successfully';

        $existingRequest = FolderRequest::find($id);
        if ($existingRequest === null) {
            $json['error'] = 'Folder request not found';
            return response()->json($json, 404);
        }

        if (Auth::user()->cant('upgrade', $existingRequest)) {
            $json['error'] = 'Unauthorized';
            return response()->json($json, 403);
        }

        $existingUpgradeRequests = FolderRequest::where('user_id', Auth::user()->id)
        ->where('folder_id', $existingRequest->folder_id)
        ->where('is_upgrade_of', '!=', null)->get();

        if (!$existingUpgradeRequests->isEmpty()) {
            $json['error'] = 'Upgrade request already sent';
            return response()->json($json, 400);
        }

        $validation = Validator::make($request->all(), [
            'permissions' => 'required|json',
            'notes' => 'required|string|between:10,400',
            'lifespan' => 'numeric|between:7,365'
        ]);

        if ($validation->fails()) {
            $json['error'] = $validation->errors();
            return response()->json($json, 400);
        }

        if (!$this->checkJsonSchema($request->permissions)) {
            $json['error'] = 'Invalid permissions json schema';
            return response()->json($json, 400);
        }

        if ($existingRequest->permissions == $request->permissions) {
            $json['error'] = 'Permissions must be different';
            return response()->json($json, 400);
        }

        $folderRequest = new FolderRequest;
        $folderRequest->status = 'pending';
        $folderRequest->folder_id = $existingRequest->folder_id;
        $folderRequest->user_id = Auth::user()->id;
        $folderRequest->is_upgrade_of = $existingRequest->id;
        $folderRequest->permissions = $request->permissions;
        $folderRequest->notes = $request->notes;
        $folderRequest->lifespan = $request->lifespan ?? $existingRequest->lifespan;
        $response = $folderRequest->save();
    
        if (!$response) {
            $json['error'] = 'Database error, contact the sysAdmin';
            return response()->json($json, 500);
        }

        $json['message'] = 'Upgrade successfully stored';
        return response()->json($json, 200);
    }

    
    public function destroy($id)
    {
        $json['error'] = null;
        $json['message'] = 'Folder request not removed successfully';

        $request = FolderRequest::find($id);
        if ($request === null) {
            $json['error'] = 'Folder not found';
            return response()->json($json, 404);
        }

        if (Auth::user()->cant('delete', $request)) {
            $json['error'] = 'Unauthorized';
            return response()->json($json, 403);
        }

        if ($request->upgrade !== null) {
            $request->upgrade->delete();
        }

        $response = $request->delete();
        if (!$response) {
            $json['error'] = 'Database error, contact the sysAdmin';
            return response()->json($json, 500);
        }

        $json['message'] = 'Folder request deleted successfully';
        return response()->json($json, 200);
    }

    
    public function manage($id, Request $request)
    {
        $json['error'] = null;
        $json['message'] = 'Folder request not managed successfully';

        if (Auth::user()->cant('manage', FolderRequest::class)) {
            $json['error'] = 'Unauthorized';
            return response()->json($json, 403);
        }

        $folderRequest = FolderRequest::find($id);
        if ($folderRequest === null) {
            $json['error'] = 'Folder request not found';
            return response()->json($json, 404);
        }

        $validation = Validator::make($request->all(), ['choice' => 'required|boolean']);
        if ($validation->fails()) {
            $json['error'] = $validation->errors();
            return response()->json($json, 400);
        }

        $checkDBResponse = true;
        if ($request->choice) {
            $folderRequest->status = 'active';
            $folderRequest->authorized = true;
            $folderRequest->authorizer_id = Auth::user()->id;
            $folderRequest->authorized_at = now();
            $folderRequest->expiration_date = now()->addDays($folderRequest->lifespan);

            if ($folderRequest->is_upgrade_of !== null) {
                $prevRequest = $folderRequest->previousRequest;
                $folderRequest->is_upgrade_of = null;
                $checkDBResponse = $folderRequest->save();
                $prevRequest->delete();
            } else {
                $checkDBResponse = $folderRequest->save();
            }
        } else {
            $folderRequest->status = 'refused';
            $folderRequest->authorized = false;
            $folderRequest->authorizer_id = Auth::user()->id;
            $folderRequest->authorized_at = null;
            $folderRequest->expiration_date = null;
            $checkDBResponse = $folderRequest->save();
        }

        if (!$checkDBResponse) {
            $json['error'] = 'Database error, contact the sysAdmin';
            return response()->json($json, 500);
        }

        $json['message'] = 'Request managed to be ' . $folderRequest->status;
        return response()->json($json, 200);
    }
}
