<?php

// namespace App\Http\Controllers;

// use Illuminate\Http\Request as HttpRequest;
// use Illuminate\Support\Facades\Auth;
// use Illuminate\Support\Facades\Validator;
// use App\Models\Request; 
// use App\Models\Folder; 

// class RequestController extends Controller
// {
//     // Lasciati guidare dal pressappochismo
//     private function checkJsonSchema(string $json) 
//     {
//         $data = json_decode($json, true); 
        
//         if (
//             $data === null || 
//             !isset($data['course']) || 
//             !isset($data['course']['update']) || !is_bool($data['course']['update']) || 
//             !isset($data['course']['delete']) || !is_bool($data['course']['delete']) || 
//             !isset($data['course']['create']) || !is_bool($data['course']['create']) ||
//             !isset($data['course']['global']) || !is_bool($data['course']['global']) ||
//             !isset($data['folder']) ||
//             !isset($data['folder']['update']) || !is_bool($data['folder']['update']) || 
//             !isset($data['folder']['delete']) || !is_bool($data['folder']['delete']) ||
//             !isset($data['folder']['manage']) || !is_bool($data['folder']['manage']) ||
//             !isset($data['folder']['remove']) || !is_bool($data['folder']['remove']) 
//         ) { 
//             return false; 
//         }  
//         return true; 
//     }


//     private function exposePermissions(Request $request) 
//     {
//         $permissions = json_decode($request->permissions, true);    
//         if ($request->folder_id === null) {
//             unset($permissions['folder']); 
//             return $permissions;     
//         }
//         return $permissions; 
//     }

//     /**
//      *  All requests that call functions from this controller 
//      *  must be authenticated and authorized by RequestPolicy. 
//      *  All of this functions will @return Illuminate\Http\Response;  
//      */

//     public function index()
//     {
//         if (Auth::user()->cant('see', Request::class)) {
//             $json['content'] = null; 
//             $json['error'] = 'Unauthorized';
//             return response()->json($json, 403);  
//         }

//         $json['error'] = null; 
//         $json['content'] = Request::all()->each(function ($request) {
//             $request->permissions = $this->exposePermissions($request);  
//         });  
//         return response()->json($json, 200); 
//     }

//     public function store(HttpRequest $request)
//     {
//         if (Auth::user()->cant('create', Request::class)) {
//             $json['error'] = 'Unauthorized. Admins cannot make requests.'; 
//             $json['message'] = 'Requested not created successfully';
//             return response()->json($json, 403); 
//         }

//         $validation = Validator::make($request->all(), [
//             'course_id' => 'required|numeric|exists:courses,id', 
//             'folder_id' => 'nullable|numeric|exists:folders,id',
//             'permissions' => 'required|json', 
//             'notes' => 'required|between:10,400',
//             'lifespan' => 'required|numeric|between:7,365', 
//         ]); 

//         if ($validation->fails()) {
//             $json['error'] = $validation->errors(); 
//             $json['message'] = 'Requested not created successfully';
//             return response()->json($json, 400); 
//         }

//         $global = (json_decode($request->input('permissions'), true))['course']['global']; 
//         if ($global && $request->has('folder_id')) {
//             $json['error'] = 'You cannot request both course global permissions and folder permissions'; 
//             $json['message'] = 'Requested not created successfully';
//             return response()->json($json, 400); 
//         }

//         $existingRequest = Request::where('user_id', Auth::user()->id)
//                 ->where('course_id', $request->input('course_id'))->get(); 
        
//         foreach ($existingRequest as $req) {
//             $permissions = json_decode($req->permissions, true); 
            
//             if ($permissions['course']['global']) {
//                 $json['error'] = 'You already have global permissions to this course'; 
//                 $json['message'] = 'Requested not created successfully';
//                 return response()->json($json, 400); 
//             }

//             if (!$request->has('folder_id') && !$req->folder_id && $req->course_id == $request->input('course_id')) {
//                 $json['error'] = 'You must delete the previous request or upgrade the existing one';
//                 $json['message'] = 'Requested not created successfully';
//                 return response()->json($json, 400); 
//             }

//             if ($request->has('folder_id') && $req->folder_id == $request->input('folder_id')) {
//                 $json['error'] = 'You must delete the previous request or upgrade the existing one';
//                 $json['message'] = 'Requested not created successfully';
//                 return response()->json($json, 400); 
//             }
//         }

//         if ($request->input('folder_id')) {
//             $folder = Folder::find($request->input('folder_id')); 
//             if ($folder->course_id != $request->input('course_id')) {
//                 $json['error'] = 'The folder must be inside the course'; 
//                 $json['message'] = 'Requested not created successfully'; 
//                 return response()->json($json, 400);  
//             }
//         }

//         if (!$this->checkJsonSchema($request->input('permissions'))) {
//             $json['error'] = 'Invalid permission json schema'; 
//             $json['message'] = 'Requested not created successfully';
//             return response()->json($json, 400); 
//         }

//         $newRequest = new Request; 
//         $newRequest->user_id = Auth::user()->id; 
//         $newRequest->status = 'pending'; 
//         $newRequest->course_id = $request->input('course_id'); 
//         $newRequest->folder_id = $request->input('folder_id') ?? null; 
//         $newRequest->permissions = $request->input('permissions'); 
//         $newRequest->notes = $request->input('notes'); 
//         $newRequest->lifespan = $request->input('lifespan'); 
//         $newRequest->authorized = null; 
//         $newRequest->authorizer_id = null;
//         $newRequest->authorized_at = null; 
//         $newRequest->expiration_date = null; 
//         $response = $newRequest->save(['timestamps' => false]); 

//         if (!$response) {
//             $json['error'] = 'Database error, contact the sysAdmin'; 
//             $json['message'] = 'Requested not created successfully';
//             return response()->json($json, 500); 
//         }

//         $json['error'] = null; 
//         $json['message'] = 'Request successfully stored.';
//         return response()->json($json, 200);  

//     }


//     public function show($id)
//     {
//         $request = Request::find($id); 
//         if ($request === null) {
//             $json['content'] = null; 
//             $json['error'] = 'Request not found'; 
//             return response()->json($json, 404);
//         }

//         if (Auth::user()->cant('view', $request)) {
//             $json['content'] = null; 
//             $json['error'] = 'Unauthorized';
//             return response()->json($json, 403); 
//         }

//         $request->permissions = json_decode($request->permissions, true); 
//         $json['error'] = null; 
//         $json['content'] = $request; 
//         return response()->json($json, 200);
//     }


//     public function upgrade(HttpRequest $request, $id)
//     {
        
//     }


//     public function destroy($id)
//     {
//         $request = Request::find($id); 
//         if ($request === null) {
//             $json['error'] = 'Request not found'; 
//             $json['message'] = 'Request not deleted successfully';
//             return response()->json($json, 404); 
//         }    

//         if (Auth::user()->cant('delete', $request)) {
//             $json['error'] = 'Unauthorized'; 
//             $json['message'] = 'Request not deleted successfully';
//             return response()->json($json, 403); 
//         }

//         $response = $request->delete(); 
//         if (!$response) {
//             $json['error'] = 'Database error, contact the sysAdmin'; 
//             $json['message'] = 'Request not deleted successfully';
//             return response()->json($json, 500); 
//         }

//         $json['error'] = null; 
//         $json['message'] = 'Request deleted successfully'; 
//         return response()->json($json, 200);
//     }


//     public function manageRequest($id, Request $httpRequest) 
//     {

//         if (Auth::user()->cant('manage', Request::class)) {
//             $json['error'] = 'Unauthorized'; 
//             $json['message'] = 'Request not managed';
//             return response()->json($json, 403); 
//         }

//         $validation = Validator::make($httpRequest->all(), [
//             'choice' => 'required|boolean', 
//         ]);

//         if ($validation->fails()) {
//             $json['error'] = $validation->errors(); 
//             $json['message'] = 'Request not managed';
//             return response()->json($json, 400); 
//         }

//         $request = Request::find($id);
//         if ($request === null) {
//             $json['error'] = 'Request not found'; 
//             $json['message'] = 'Request not managed';
//             return response()->json($json, 404); 
//         }     

//         if ($httpRequest->input('choice')) {
//             $request->status = 'active'; 
//             $request->authorized = true; 
//             $request->authorizer_id = Auth::user()->id;
//             $request->authorized_at = now(); 
//             $request->expiration_date = now()->addDays($request->lifespan); 
//             $request->save(); 
//         }
//         else {
//             $request->status = 'refused';; 
//             $request->authorized = false; 
//             $request->authorizer_id = Auth::user()->id;
//             $request->save(); 
//         }        
 
//         $json['error'] = null; 
//         $json['message'] = 'Request ' . $request->status; 
//         return response()->json($json, 200); 
//     }

// }
