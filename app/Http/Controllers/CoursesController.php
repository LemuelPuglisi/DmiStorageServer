<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Course;
use App\Models\Folder;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Auth;

class CoursesController extends Controller
{

    /**
     *  Initialize an Instance of the controller
     */
    public function __construct() { }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return response()->json(Course::all(), 200);
    }


    /**
     * Display an ordered listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function orderedIndex($param, $order)
    {
        if (!in_array($param, Course::$sortableFields) || ($order !== 'desc' && $order !== 'asc')) {
            $json['error'] = 'Params must be year, id, cfu or timestamps and order must be desc or asc'; 
            $json['content'] = null; 
            return response()->json($json, 400);
        }

        $json['error'] = null;  
        $json['content'] = Course::orderBy($param, $order)->get(); 
        return response()->json($json, 200);
    }



    /**
     * Display the folders contained in the course.
     * If root parameter is true, then it will display only root folders
     *
     * @return \Illuminate\Http\Response
     */
    public function folders($id, Request $request)
    {
        $course = Course::find($id);
        if ($course === null) {
            $json['content'] = null; 
            $json['error'] = 'Course not found';
            return response()->json($json, 404);
        }

        if ($request->input('root')) {
            $json['error'] = null; 
            $json['content'] = $course->rootFolders;
            return response()->json($json, 200);
        }

        $json['error'] = null; 
        $json['content'] = $course->folders;
        return response()->json($json, 200);
    }


    /**
     * Display an ordered list of the folders contained
     * in a Course, if you pass root=true then it will
     * display only the root ones.
     *
     * @return \Illuminate\Http\Response
     */
    public function orderedFolders($id, $param, $order, Request $request)
    {
        $course = Course::find($id);
        if ($course === null) {
            $json['content'] = null; 
            $json['error'] = 'Course not found';
            return response()->json($json, 404);
        }

        if (!in_array($param, Folder::$sortableFields) || ($order !== 'desc' && $order !== 'asc')) {
            $json['content'] = null;
            $json['error'] = 'Params must be id, influence or timestamps and order must be desc or asc';
            return response()->json($json, 400);
        }

        $folders = $course->orderedFolders($param, $order);
        if ($request->input('root')) {
            $folders = $folders->where('subfolder_of', null);
        }

        $json['error'] = null;
        $json['content'] = $folders;
        return response()->json($json, 200);
    }


    /**
     * Return a choosen number of most viewed files
     * from a course.
     *
     * @return \Illuminate\Http\Response
    */
    public function getMostViewedFiles($id, $limit)
    {
        $course = Course::find($id);
        if ($course === null) {
            $json['content'] = null;
            $json['error'] = 'Course not found';
            return response()->json($json, 404);
        }

        if (!ctype_digit($limit)) {
            $json['content'] = null;
            $json['error'] = 'Limit must be an integer';
            return response()->json($json, 400);
        }

        $json['error'] = null;
        $json['content'] = $course->mostViewedFiles($limit);
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
        $validation = Validator::make($request->all(), [
            'name' => 'required|max:255|min:3',
            'year' => 'required|max:2',
            'cfu' => 'required|numeric'
        ]);
        
        if (Auth::user()->cant('create', Course::class)) {
            $json['message'] = 'Course not created successfully';
            $json['error'] = 'Unauthorized';
            return response()->json($json, 403);
        }

        if ($validation->fails()) {
            $json['message'] = 'Course not created successfully';
            $json['error'] = $validation->errors();
            return response()->json($json, 400);
        }

        $course = new Course();
        $course->name = $request->input('name');
        $course->year = $request->input('year');
        $course->cfu = $request->input('cfu');
        $course->save();

        $json['message'] = 'Course created successfully';
        $json['error'] = null;
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
        $course = Course::find($id);
        if ($course === null) {
            return response()->json(null, 404);
        }
        return response()->json(Course::find($id), 200);
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
        $course = Course::find($id);

        if (Auth::user()->cant('update', $course)) {
            $json['message'] = 'Course not updated successfully';
            $json['error'] = 'Unauthorized';
            return response()->json($json, 403);
        }

        if ($course === null) {
            $json['message'] = 'Course not updated successfully';
            $json['error'] = 'Course not found';
            return response()->json($json, 404);
        }

        $validation = Validator::make($request->all(), [
            'name' => 'required_without_all:year,cfu|max:255|min:3',
            'year' => 'required_without_all:name,cfu|max:2',
            'cfu' => 'required_without_all:name,year|numeric'
        ]);

        if ($validation->fails()) {
            $json['message'] = 'Course not updated successfully';
            $json['error'] = $validation->errors();
            return response()->json($json, 400);
        }

        $response = $course->update($request->all());

        if (!$response) {
            $json['message'] = 'Course not updated successfully';
            $json['error'] = 'Server Error, contact the SysAdmin';
            return response()->json($json, 500);
        }

        $json['error'] = null;
        $json['message'] = 'Course updated successfully';
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
        $course = Course::find($id);

        if ($course === null) {
            $json['message'] = 'Course not deleted successfully';
            $json['error'] = 'Course not found';
            return response()->json($json, 404);
        }

        if (Auth::user()->cant('update', $course)) {
            $json['message'] = 'Course not deleted successfully';
            $json['error'] = 'Unauthorized';
            return response()->json($json, 403);
        }

        $response = $course->delete();
        if (!$response) {
            $json['message'] = 'Course not deleted successfully';
            $json['error'] = 'Server Error, contact the SysAdmin';
            return response()->json($json, 500);
        }

        $json['message'] = 'Course successfully deleted';
        $json['error'] = null;
        return response()->json($json, 200);
    }
}
