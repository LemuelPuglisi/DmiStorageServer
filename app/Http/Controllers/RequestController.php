<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request as HttpRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\Request; 

class RequestController extends Controller
{
    /**
     *  All requests that call functions from this controller 
     *  must be authenticated and authorized by RequestPolicy. 
     *  All of this functions will @return Illuminate\Http\Response;  
     */

    public function index()
    {
        if (Auth::user()->cant('see', Request::class)) {
            $json['content'] = null; 
            $json['error'] = 'Unauthorized';
            return response()->json($json, 403);  
        }

        $json['error'] = null; 
        $json['content'] = Request::all(); 
        return response()->json($json, 200); 
    }

    public function store(HttpRequest $request)
    {
        
    }

    public function show($id)
    {
        $request = Request::find($id); 
        if ($request === null) {
            $json['content'] = null; 
            $json['error'] = 'Request not found'; 
            return response()->json($json, 404);
        }

        if (Auth::user()->cant('view', $request)) {
            $json['content'] = null; 
            $json['error'] = 'Unauthorized';
            return response()->json($json, 403); 
        }

        $json['error'] = null; 
        $json['content'] = $request; 
        return response()->json($json, 200);
    }

    public function update(HttpRequest $request, $id)
    {
        
    }

    public function destroy($id)
    {
        
    }
}
