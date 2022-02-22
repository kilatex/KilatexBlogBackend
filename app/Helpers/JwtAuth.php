<?php
namespace App\Helpers;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\SUpport\Facades\DB;
use App\Models\User;

class JwtAUth {
    public $key;

    public function __construct(){
        $this->key = 'clave_secreta';
    }

    public function signup($email, $password, $getToken = null){
        // Search if other user with this info exists
        $user = User::where([
            'email' => $email,
            'password' => $password
        ])->first();
        // comprobe if it is correct(object)
        $signup = false;

        if(is_object($user)){
            $signup = true;
        }
        // generate token with user auth
        if($signup == true){
            $token = array(
                'sub' => $user->id,
                'email' => $user->email,
                'name' => $user->name,
                'surname' => $user->surname,
                'username' => $user->username,
                'iat' => time(),
                'exp' => time() + (7*24*60*60)
            );

            $jwt = JWT::encode($token,$this->key, 'HS256' );
            $decoded = JWT::decode($jwt, new Key($this->key, 'HS256'));

            if(is_null($getToken)){
                $data =  $jwt;
            }else{
                 $data = $decoded;
            }

        }else{
            $data = array(
                'status' => 'error',
                'message' => "Invalid Login, email or password doesn't match",

            );

            return $data;
        }
        // return information in fuction of params

        return $data;
    }

}
