<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use App\Models\User; 

class UsersController extends Controller
{
    
    /**
     * User roles ID
     * 
     */
    const ROLES = [
        'user' => 1, 
        'admn' => 2, 
        'sysa' => 3 
    ]; 


    /**
     * Create an user
     * 
     * @return Illuminate\Http\Response;
     */
    public function register(Request $request)
    {
        $jsonResponse = [
            'message' => null, 
            'errors' => null, 
        ]; 

        $validation = Validator::make($request->all(), [
            'name' => 'required|string|alpha_num|unique:users,name', 
            'email' => 'required|string|between:6,60|email|unique:users,email',
            'password' => 'required|string|between:1,50', 
        ]); 

        if ($validation->fails()) {
            $jsonResponse['message'] = 'Registration failed';
            $jsonResponse['errors'] = $validation->errors(); 
            return response()->json($jsonResponse, 400); 
        }

        $user = new User;
        $user->name = $request->input('name');  
        $user->email = $request->input('email'); 
        $user->password = Hash::make($request->input('password')); 
        $user->role = self::ROLES['user']; 
        $response = $user->save(); 
        
        if (!$response) {
            $jsonResponse['message'] = 'Registration failed';
            $jsonResponse['errors'] = 'Database error, contact the sysAdmin'; 
            return response()->json($jsonResponse, 500); 
        }

        $jsonResponse['message'] = 'User registrated successfully'; 
        return response()->json($jsonResponse, 200);
    }

    
    /**
     * Show authenticated user details
     * 
     * @return App\Models\User; 
     */
    public function details(Request $request)
    {
        return $request->user();
    }

    /**
     *  Show user uploaded files
     */
    public function uploadedFiles($id)
    {
        $jsonResponse = [
            'content' => null, 
            'error' => null
        ]; 

        $user = User::find($id); 
        if ($user === null) {
            $jsonResponse['error'] = 'User not found'; 
            return response()->json($jsonResponse, 404); 
        }

        $jsonResponse['content'] = $user->files; 
        return response()->json($jsonResponse, 200); 

    }

}
