<?php

namespace App\Http\Controllers;

use  App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{

    public function register(Request $request){
        
        // GET USER INFO
        $json = $request->input('json',null);
        $params = json_decode($json); //object
        $params_array = json_decode($json,true); //array
     

        if(!empty($params) && !empty($params_array)){

            $params_array = array_map('trim',$params_array);
            // VALIDATE USER INFO
            
            $validate = \Validator::make($params_array,[
                'name' => 'required|alpha|max:100',
                'surname' => 'required|alpha|max:100',
                'email' => 'required|string|email|max:255|unique:users', // COMPROBE IF USER EXISTS
                'password' => 'required|string',
                'password_confirmation' => 'required|string',
            ]);


            if($validate->fails()){

                $data =  array(
                    'status' => 'error',
                    'code' => '400',
                    'message' => 'User NOT VALIDATED',
                    'errors' => $validate->errors()
                );

                return response()->json($data);

            }else{

                //CREATE USER
                if($params_array['password'] == $params_array['password_confirmation']){

                    $user = new User();

                    $user->role = "ROLE_USER";
                    $user->name = $params_array['name'];
                    $user->surname = $params_array['surname'];
                    $user->email = $params_array['email'];
                    $user->password = hash('sha256',$params_array['password']); // HASH PASSWORD
                    $user->username = $params_array['username'];
                    $user->save();

                    $data =  array(
                        'status' => 'success',
                        'code' => '200',
                        'message' => "User Registered",
                        'user' => $user
                    );

                    return response()->json($data);


                }
                else{

                    $data =  array(
                        'status' => 'error',
                        'code' => '400',
                        'message' => "Password doesn't match"
                    );

                    return response()->json($data);
                }

             

            }
        }else{
            $data =  array(
                'status' => 'error',
                'code' => '400',
                'message' => 'Format Invalid'
            );
        }
 


 




     

        return response()->json($data,$data['code']);
    }

   public function login(Request $request){

        // GET USER INFO
        $json = $request->input('json',null);
        $params = json_decode($json); //object
        $params_array = json_decode($json,true); //array
        $jwtAuth = new \JwtAuth();

        if(!empty($params) && !empty($params_array)){
            $params_array = array_map('trim',$params_array); // trim fields 

            // validate info
            $validate = \Validator::make($params_array,[       
                'email' => 'required|string|email',
                'password' => 'required|string',
            ]);
            
            if($validate->fails()){
                $signup =  array(
                    'status' => 'error',
                    'code' => '400',
                    'message' => 'Login Failed',
                    'errors' => $validate->errors()
                );
           }else{
            $email = $params_array['email'];
            $password = hash('sha256', $params_array['password']);
            $signup =  $jwtAuth->signup($email,$password,true);

           }
        }
        else{
            $signup = array(
                'status' => 'error',
                'code' => '400',
                'message' => 'Login Failed'

            );
        }
        
        return response()->json($signup);
    }
    public function update(Request $request){
      
        //Comprobe if User is AUTH
        $token = $request->header('Authorization');
        $jwtAuth = new \jwtAuth();
        $checkToken = $jwtAuth->checkToken($token);

        if($checkToken){
            
            // GET INFO 
            $json = $request->input('json',null);
            $params = json_decode($json);
            $params_array = json_decode($json,true);

            $user = $jwtAuth->checkToken($token,true);
            var_dump($user);
            die();
            // VALIDATE INFO
            $validate = \Validator::make($params_array,[
                'name' => 'required|alpha|max:100',
                'surname' => 'required|alpha|max:100',
                'username' => 'required|string|max:255|unique:users',
                'email' => 'required|string|email|max:255|unique:users,'.$user->id, // COMPROBE IF USER EXISTS
            ]);
       

            // UPDATE USER

            // RETURN ARRAY

        }else{

            $data = array(
                'status' => 'error',
                'code' => '400',
                'message' => 'User not updated'
            );
        }

        return response()->json($update,$update['code']);
    }



}

/* JSON EXAMPLE
{
"name":"Luis",
"surname" :  "Maldonado",
"email" : "luisito@luisito.com",
"username": "luissxxxx",
"password": "santiago123",
"password_confirmation" : "santiago123"
}

*/