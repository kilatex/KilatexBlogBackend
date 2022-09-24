<?php

namespace App\Http\Controllers;

use  App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use App\Helpers\JwtAuth;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('api.auth', ['except' => ['register', 'login',]]);
    }

    public function update(Request $request)
    {

        // GET INFO 


        $token = $request->header('Authorization');
        $jwtAuth = new \JwtAuth();
        $user = $jwtAuth->checkToken($token, true);

       // VALIDATE INFO
        $validate = \Validator::make($request->all(), [
            'name' => 'max:100',
            'surname' => 'max:100',
            'username' => 'string|max:255|unique:users,username,' . $user->sub,
            'description' => 'string|max:255'
        ]);

        if ($validate->fails()) {
            $data =  array(
                'status' => 'error',
                'code' => '400',
                'message' => 'Updated Failed',
                'errors' => $validate->errors()
            );
            
        } 
        else {
            $user = User::where('id',$user->sub)->first();
            $user->update($request->all());
            $data =  array(
                'status' => 'success',
                'code' => '200',
                'message' => 'Updated success',
                'user' => $user
            );

        }


        // RETURN ARRAY
        return response()->json($data);
    }

    public function uploadAvatar(Request $request)
    {
        // GET IMAGE
        $path1 = $request->file('file0');
        $token = $request->header('Authorization');
        $jwtAuth = new \JwtAuth();
        $user = $jwtAuth->checkToken($token, true);

        $validate = \Validator::make($request->all(), [
            'file0' => 'required|image'
        ]);


        if ($validate->fails()) {
            $data =  array(
                'status' => 'error',
                'code' => '400',
                'message' => 'Upload Avatar Failed',
                'errors' => $validate->errors()
            );
        } else {
            $user_auth = User::find($user->sub);


            //IMAGE 1
            $image_path_name1 = time() . $path1->getClientOriginalName();


            // SAVE IMAGE     
            Storage::disk('users')->put($image_path_name1, \File::get($path1));
            $user_auth->image = $image_path_name1;
            $user_auth->save();


            $data = array(
                'status' => 'success',
                'code' => '200',
                'image' => $image_path_name1
            );
        }




        return response()->json($data);
    }

    public function getAvatar($filename)
    {

        $isset = Storage::disk('users')->exists($filename);

        if ($isset) {
            $file =  Storage::disk('users')->get($filename);

            return new Response($file, 200);
        } else {
            $data = array(
                'status' => 'error',
                'code' => '404',
                'message' => 'Avatar not found',

            );
        }
    }

    public function getUser($id)
    {
        $user = User::find($id);

        if (is_object($user)) {
            $data = array(
                'status' => 'success',
                'code' => '200',
                'user' => $user
            );
        } else {
            $data = array(

                'status' => 'error',
                'code' => '404',
                'message' => 'User not found',

            );
        }

        return $data;
    }

    public function getAll()
    {

        $users = User::orderBy('id', 'DESC')->paginate('6');
        $data = array(
            'status' => 'success',
            'code' => '200',
            'users' => $users,
        );

        return $data;
    }

    public function latestUsers()
    {
        $users = User::orderBy('id', 'DESC')->limit(5)->get();
        $data = array(
            'status' => 'success',
            'code' => '200',
            'users' => $users,
        );

        return $data;
    }
}


/* JSON EXAMPLE
{
"name" : "Mafer",
"surname" :  "Mafer",
"email" : "mafer@mafer.com",
"username": "maferrr"
"password": "mafer123",
"password_confirmation" : "mafer123"
}
*/
