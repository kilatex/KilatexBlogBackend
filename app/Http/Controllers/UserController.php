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

            if(!empty($params->getToken)){
                $signup =  $jwtAuth->signup($email,$password);

            }else{
                $signup =  $jwtAuth->signup($email,$password,'messi');

            }

           }
        }
        else{
            $signup = array(
                'status' => 'error',
                'code' => '400',
                'message' => 'Login Failed'

            );
        }
        
        return response()->json($signup,200);
    }
    public function update(Request $request){
      
        //Comprobe if User is AUTH
        $token = $request->header('Authorization');
        $jwtAuth = new \JwtAuth();
        $checkToken = $jwtAuth->checkToken($token);   

        if($checkToken){
            
            // GET INFO 
            $json = $request->input('json',null);
            $params = json_decode($json);
            $params_array = json_decode($json,true);

            $user = $jwtAuth->checkToken($token,true);
   
           
            // VALIDATE INFO
            $validate = \Validator::make($params_array,[
                'name' => 'alpha|max:100',
                'surname' => 'alpha|max:100',
                'username' => 'string|max:255|unique:users,username,'.$user->sub,
                'email' => 'string|email|max:255|unique:users,email,'.$user->sub, // COMPROBE IF USER EXISTS
                'password' => 'string',
                'password_confirmation' => 'string',
            ]);


       
            if($validate->fails()){
                $data =  array(
                    'status' => 'error',
                    'code' => '400',
                    'message' => 'Login Failed',
                    'errors' => $validate->errors()
                );
           }else{
            // UPDATE USER
       

                $password = empty($params_array['password']) ? null : $params_array['password'];
                $password_confirmation = empty($params_array['password_confirmation']) ? null : $params_array['password_confirmation'];

 
              

                if(($password != null || $password_confirmation != null) && $password == $password_confirmation){
                    $params_array['password'] = hash('sha256', $params_array['password']);
                }else{
                    $data = array(
                        'status' => 'error',
                        'code' => '400',
                        'message' => "Passwords doesn't match"
                    );

                    return response()->json($data);

                }
            
                // FIELDS THAT NOT GONNA BE UPDATED
                unset($params_array['id']);
                unset($params_array['role']);
                unset($params_array['created_at']);
                unset($params_array['remember_token']);
                unset($params_array['password_confirmation']);

                $userToUpdate = User::where('id',$user->sub);
                if($userToUpdate){
                    $userToUpdate->update($params_array);
                    $data = array(
                        'status' => 'success',
                        'code' => '200',
                        'message' => 'User Updated Successfully',
                        'changes' => $params_array
                    );
                }else{
                    $data = array(
                        'status' => 'error',
                        'code' => '400',
                        'message' => 'User not updated'
                    );
                }
              
        

           }
        
        
            // RETURN ARRAY
            return response()->json($data);

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
"surname" :  "Maldonadito",
"email" : "luchitooo@luisito.com",
"username": "luisitazoooo",

}
"password": "santiago123",
"password_confirmation" : "santiago123"
*/

