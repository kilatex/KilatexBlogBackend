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
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string',
                'password_confirmation' => 'required|string',
            ]);


            if($validate->fails()){

                $data =  array(
                    'status' => 'Failed',
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
                    $user->password = hash::make($params_array['password']);
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
                        'status' => 'Failed',
                        'code' => '400',
                        'message' => "Password doesn't match"
                    );

                    return response()->json($data);
                }

             

            }
        }else{
            $data =  array(
                'status' => 'Failed',
                'code' => '400',
                'message' => 'Format Invalid'
            );
        }
 


        // COMPORBE IF USER EXISTS

        // HASH PASSWORD

        // CREATE USER


     

        return response()->json($data,$data['code']);
    }

   public function login(Request $request){
        return "Register User Function";
    }
    public function update(Request $request){
       return "Update User Function";
    }



}

/* JSON EXAMPLE
{
"name":"Luis",
"surname" :  "Maldonado",
"email" : "santiagodmaldon@gmail.com",
"username": "luissxxxx",
"password": "santiago123",
"password_confirmation" : "santiago123"
}

*/