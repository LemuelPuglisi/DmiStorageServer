<?php
/**
 *  All the functions returns a json response.
 */
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\CourseRequest;
use App\Models\FolderRequest;

class UserController extends Controller
{
    const ROLES = [
        'user' => 1,
        'admn' => 2,
        'sysa' => 3
    ];


    public function show($id)
    {
        $user = User::find($id);
        if ($user === null) {
            $json['error'] = 'User not found';
            $json['content'] = null; 
            return response()->json($json, 404);
        }

        if (Auth::user()->cant('show', $user)) {
            $json['error'] = 'Unauthorized';
            $json['content'] = null; 
            return response()->json($json, 403);
        }

        $json['error'] = null; 
        $json['content'] = $user; 
        return response()->json($json, 200); 
    }


    public function index()
    {
        if (Auth::user()->cant('index', User::class)) {
            $json['error'] = 'Unauthorized';
            $json['content'] = null;
            return response()->json($json, 403);
        }

        $json['error'] = null;
        $json['content'] = User::all()
        ->each(function ($user) {
            $user->addHidden(['role']);
        });
        return response()->json($json, 200);
    }


    public function indexAdmins()
    {
        if (Auth::user()->cant('indexAdmins', User::class)) {
            $json['error'] = 'Unauthorized';
            $json['content'] = null;
            return response()->json($json, 403);
        }

        $json['error'] = null;
        $json['content'] = User::all()->where('role', 2);
        return response()->json($json, 200);
    }


    public function changeRole(Request $request, $id)
    {
        $user = User::find($id);
        if ($user === null) {
            $json['error'] = 'User not found';
            $json['message'] = 'Role not successfully updated';
            return response()->json($json, 404);
        }

        $validation = Validator::make($request->all(), [
            'role' => 'required|numeric|between:1,3'
        ]);

        if ($validation->fails()) {
            $json['error'] = $validation->errors();
            $json['message'] = 'Role not successfully updated';
            return response()->json($json, 400);
        }

        if (Auth::user()->cant('changeRoles', $user)) {
            $json['error'] = 'Unauthorized';
            $json['message'] = 'Role not successfully updated';
            return response()->json($json, 403);
        }

        $user->role = $request->input('role');
        $user->save();

        $json['error'] = null;
        $json['message'] = 'Role successfully updated';
        return response()->json($json, 200);
    }

    
    public function destroy($id)
    {
        $user = User::find($id);
        if ($user === null) {
            $json['error'] = 'User not found';
            $json['message'] = 'User not deleted successfully';
            return response()->json($json, 404);
        }

        if (Auth::user()->cant('delete', $user)) {
            $json['error'] = 'Unauthorized';
            $json['message'] = 'User not deleted successfully';
            return response()->json($json, 403);
        }

        $response = $user->delete();
        if (!$response) {
            $json['error'] = 'Database error, contact the sysAdmin';
            $json['message'] = 'User not deleted successfully';
            return response()->json($json, 500);
        }

        $json['error'] = null;
        $json['message'] = 'User successfully deleted';
        return response()->json($json, 200);
    }


    public function portability($id)
    {
        $user = User::find($id);
        if ($user === null) {
            $json['error'] = 'User not found';
            $json['content'] = null;
            return response()->json($json, 404);
        }

        if (Auth::user()->cant('getPortability', $user)) {
            $json['error'] = 'Unauthorized';
            $json['content'] = null;
            return response()->json($json, 403);
        }

        $json['content']['details'] = $user;
        $json['content']['files'] = $user->files;
        $json['error'] = null;
        return response()->json($json, 200);
    }


    public function update(Request $request, $id)
    {
        $user = User::find($id);
        if ($user === null) {
            $json['error'] = 'User not found';
            $json['message'] = 'User not updated successfully';
            return response()->json($json, 404);
        }

        if (Auth::user()->cant('update', $user)) {
            $json['error'] = 'Unauthorized';
            $json['message'] = 'User not updated successfully';
            return response()->json($json, 403);
        }

        $validation = Validator::make($request->all(), [
            'name' => 'required_without_all:email,password|string|alpha_num|unique:users,name',
            'email' => 'required_without_all:name,password|string|between:6,60|email|unique:users,email',
            'password' => 'required_without_all:email,name|string|between:1,50',
        ]);

        if ($validation->fails()) {
            $json['error'] = $validation->errors();
            $json['message'] = 'User not updated successfully';
            return response()->json($json, 400);
        }

        $user->name = $request->input('name') ?? $user->name;
        $user->email = $request->input('email') ?? $user->email;
        $user->password = Hash::make($request->input('password')) ?? $user->password;
        $user->save();
        
        $json['error'] = null;
        $json['message'] = 'User successfully updated';
        return response()->json($json, 200);
    }


    public function register(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'name' => 'required|string|alpha_num|unique:users,name',
            'email' => 'required|string|between:6,60|email|unique:users,email',
            'password' => 'required|string|between:1,50',
        ]);

        if ($validation->fails()) {
            $json['message'] = 'Registration failed';
            $json['error'] = $validation->errors();
            return response()->json($json, 400);
        }

        $user = new User;
        $user->name = $request->input('name');
        $user->email = $request->input('email');
        $user->password = Hash::make($request->input('password'));
        $user->role = self::ROLES['user'];
        $response = $user->save();
        
        if (!$response) {
            $json['message'] = 'Registration failed';
            $json['error'] = 'Database error, contact the sysAdmin';
            return response()->json($json, 500);
        }

        $json['error'] = null;
        $json['message'] = 'User registrated successfully';
        return response()->json($json, 200);
    }

    
    public function details(Request $request)
    {
        return $request->user();
    }


    public function uploadedFiles($id)
    {
        $user = User::find($id);
        if ($user === null) {
            $json['content'] = null;
            $json['error'] = 'User not found';
            return response()->json($json, 404);
        }

        $json['error'] = null;
        $json['content'] = $user->files;
        return response()->json($json, 200);
    }


    public function courseRequests($id, Request $request)
    {
        $user = User::find($id);
        if ($user === null) {
            $json['content'] = null;
            $json['error'] = 'User not found';
            return response()->json($json, 404);
        }

        if ($user->isAdmin() || $user->isSuperAdmin()) {
            $json['content'] = null;
            $json['error'] = 'Admins can\'t make requests';
            return response()->json($json, 400);
        }

        if (Auth::user()->cant('getRequests', $user)) {
            $json['content'] = null;
            $json['error'] = 'Unauthorized';
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
            $json['content'] = $user->courseRequests;
        } else {
            $json['content'] = $user->courseRequestsByStatus($request->status);
        }
        
        return response()->json($json, 200);
    }


    public function folderRequests($id, Request $request)
    {
        $user = User::find($id);
        if ($user === null) {
            $json['error'] = 'User not found';
            $json['content'] = null;
            return response()->json($json, 404);
        }

        if ($user->isAdmin() || $user->isSuperAdmin()) {
            $json['content'] = null;
            $json['error'] = 'Admins can\'t make requests';
            return response()->json($json, 400);
        }

        if (Auth::user()->cant('getRequests', $user)) {
            $json['content'] = null;
            $json['error'] = 'Unauthorized';
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
            $json['content'] = $user->folderRequests;
        } else {
            $json['content'] = $user->folderRequestsByStatus($request->status);
        }

        return response()->json($json, 200);
    }

    public function deleteTokens($id) 
    {
        $user = User::find($id); 
        if ($user === null) {
            $json['error'] = 'User not found';
            $json['message'] = 'Tokens have not been invalidated';
            return response()->json($json, 404);
        }
        
        if (Auth::user()->cant('deleteTokens', $user)) {
            $json['error'] = 'Unauthorized';
            $json['message'] = 'Tokens have not been invalidated';
            return response()->json($json, 403);
        }

        $tokens = $user->tokens; 
        foreach ($tokens as $token) {
            $token->delete(); 
        }

        $json['error'] = null; 
        $json['message'] = 'Tokens have been invalidated';
        return response()->json($json, 200); 
    }

}
