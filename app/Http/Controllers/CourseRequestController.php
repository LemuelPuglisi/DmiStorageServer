<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\CourseRequest;
use App\Models\Course; 

class CourseRequestController extends Controller
{
    /**
     *  Utility functions 
    */

    /**
     *  All this functions @return response()
    */


    public function index()
    {
        if (Auth::user()->cant('viewAll', CourseRequest::class)) {
            $json['error'] = 'Unauthorized'; 
            $json['content'] = null; 
            return response()->json($json, 403); 
        }    

        $json['error'] = null;
        $json['content'] = CourseRequest::all();
        return response()->json($json, 200);
    }


    public function show($id)
    {
        $courseRequest = CourseRequest::find($id); 
        if ($courseRequest === null) {
            $json['error'] = 'Request not found'; 
            $json['content'] = null; 
            return response()->json($json, 404); 
        }

        if (Auth::user()->cant('view', $courseRequest)) {
            $json['error'] = 'Unauthorized'; 
            $json['content'] = null; 
            return response()->json($json, 403); 
        } 

        $json['error'] = null; 
        $json['content'] = $courseRequest;
        return response()->json($json, 200); 
    }


    public function store(Request $request)
    {
        $json['error'] = null; 
        $json['message'] = 'Course request not stored successfully';
        
        if (Auth::user()->cant('create', CourseRequest::class)) {
            $json['error'] = 'Unauthorized';  
            return response()->json($json, 403); 
        }

        $validation = Validator::make($request->all(), [
            'course_id' => 'required|numeric|exists:courses,id', 
            'notes' => 'required|string|between:10,400',
            'lifespan' => 'required|numeric|between:7,365', 
        ]); 

        if ($validation->fails()) {
            $json['error'] = $validation->errors(); 
            return response()->json($json, 400); 
        }

        $existingRequest = CourseRequest::where('user_id', Auth::user()->id)
        ->where('course_id', $request->course_id)->get();

        if (!$existingRequest->isEmpty()) {
            $json['error'] = 'You already have the permissions in this course.'; 
            return response()->json($json, 400);
        }

        $courseRequest = new CourseRequest; 
        $courseRequest->status = 'pending'; 
        $courseRequest->course_id = $request->course_id; 
        $courseRequest->user_id = Auth::user()->id; 
        $courseRequest->notes = $request->notes; 
        $courseRequest->lifespan = $request->lifespan; 
        $response = $courseRequest->save(); 

        if (!$response) {
            $json['error'] = 'Database error, contact the sysAdmin.'; 
            return response()->json($json, 500);
        }

        $json['message'] = 'Course request successfully stored'; 
        return response()->json($json, 200);
    }


    public function destroy($id)
    {
        $json['error'] = null; 
        $json['message'] = 'Course request not deleted successfully';

        $courseRequest = CourseRequest::find($id); 
        if ($courseRequest === null) {
            $json['error'] = 'Course request not found'; 
            return response()->json($json, 404); 
        }    

        if (Auth::user()->cant('delete', $courseRequest)) {
            $json['error'] = 'Unauthorized'; 
            return response()->json($json, 403); 
        }

        $response = $courseRequest->delete(); 
        if (!$response) {
            $json['error'] = 'Database error, contact the sysAdmin'; 
            return response()->json($json, 500); 
        }
        
        $json['message'] = 'Course request deleted successfully'; 
        return response()->json($json, 200);
    }

    public function manage($id, Request $request) 
    {
        $json['error'] = null;
        $json['message'] = 'Course request not managed successfully';

        if (Auth::user()->cant('manage', CourseRequest::class)) {
            $json['error'] = 'Unauthorized';
            return response()->json($json, 403);
        }

        $courseRequest = CourseRequest::find($id); 
        if ($courseRequest === null) {
            $json['error'] = 'Course request not found';
            return response()->json($json, 404);
        }

        $validation = Validator::make($request->all(), ['choice' => 'required|boolean']);
        if ($validation->fails()) {
            $json['error'] = $validation->errors(); 
            return response()->json($json, 400); 
        }

        $checkDBResponse = true; 
        if ($request->choice) {
            $courseRequest->status = 'active'; 
            $courseRequest->authorized = true; 
            $courseRequest->authorizer_id = Auth::user()->id; 
            $courseRequest->authorized_at = now(); 
            $courseRequest->expiration_date = now()->addDays($courseRequest->lifespan); 
            $checkDBResponse = $courseRequest->save(); 
        }
        else {
            $courseRequest->status = 'refused'; 
            $courseRequest->authorized = false; 
            $courseRequest->authorizer_id = Auth::user()->id;
            $courseRequest->authorized_at = null;
            $courseRequest->expiration_date = null; 
            $checkDBResponse = $courseRequest->save(); 
        }

        if (!$checkDBResponse) {
            $json['error'] = 'Database error, contact the sysAdmin'; 
            return response()->json($json, 500); 
        }

        $json['message'] = 'Request managed to be ' . $courseRequest->status; 
        return response()->json($json, 200);         
    }
}
