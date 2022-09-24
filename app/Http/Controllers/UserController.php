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

    public function register(Request $request)
    {

        // GET USER INFO
        $json = $request->input('json', null);
        $params = json_decode($json); //object
        $params_array = json_decode($json, true); //array


        if (!empty($params) && !empty($params_array)) {

            $params_array = array_map('trim', $params_array);
            // VALIDATE USER INFO

            $validate = \Validator::make($params_array, [
                'name' => 'required|alpha|max:100',
                'username' => 'required|string|max:100',
                'email' => 'required|string|email|max:255|unique:users', // COMPROBE IF USER EXISTS
                'password' => 'required|string',
                'password_confirmation' => 'required|string',
            ]);


            if ($validate->fails()) {

                $data =  array(
                    'status' => 'error',
                    'code' => '400',
                    'message' => 'User NOT VALIDATED',
                    'errors' => $validate->errors()
                );

                return response()->json($data);
            } else {

                //CREATE USER
                if ($params_array['password'] == $params_array['password_confirmation']) {

                    $user = new User();

                    $user->role = "ROLE_USER";
                    $user->name = $params_array['name'];
                    $user->email = $params_array['email'];
                    $user->password = hash('sha256', $params_array['password']); // HASH PASSWORD
                    $user->username = $params_array['username'];
                    $user->surname = $params_array['surname'];

                    $user->save();

                    $data =  array(
                        'status' => 'success',
                        'code' => '200',
                        'message' => "User Registered",
                        'user' => $user
                    );

                    return response()->json($data);
                } else {

                    $data =  array(
                        'status' => 'error',
                        'code' => '400',
                        'message' => "Password doesn't match"
                    );

                    return response()->json($data);
                }
            }
        } else {
            $data =  array(
                'status' => 'error',
                'code' => '400',
                'message' => 'Format Invalid'
            );
        }

        return response()->json($data, $data['code']);
    }

    public function login(Request $request)
    {

        // GET USER INFO
        $json = $request->input('json', null);
        $params = json_decode($json); //object
        $params_array = json_decode($json, true); //array




        if (!empty($params) && !empty($params_array)) {
            $params_array = array_map('trim', $params_array); // trim fields 
            $jwtAuth = new \JwtAuth();

            // validate info
            $validate = \Validator::make($params_array, [
                'email' => 'required|string|email',
                'password' => 'required|string',
            ]);

            if ($validate->fails()) {
                $signup =  array(
                    'status' => 'error',
                    'code' => '400',
                    'message' => 'Login Failed, NOT VALIDATED',
                    'errors' => $validate->errors()
                );
            } else {

                $email = $params_array['email'];
                $password = hash('sha256', $params_array['password']);

                if (!empty($params->getToken)) {
                    $signup =  $jwtAuth->signup($email, $password);
                } else {
                    $signup =  $jwtAuth->signup($email, $password, true);
                }
            }
        } else {
            $signup = array(
                'status' => 'error',
                'code' => '400',
                'message' => 'Login Failed, INPUTS EMPTY'

            );
        }

        return response()->json($signup, 200);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        $data =  array(
            'status' => 'success',
            'code' => '200',
            'message' => 'Logout successfully'
        );
        return response()->json($data);

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
