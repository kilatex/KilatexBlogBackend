<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UserController extends Controller
{

    public function register(Request $request){
        
        // GET USER INFO

        // VALIDATE USER INFO

        // COMPORBE IF USER EXISTS

        // HASH PASSWORD

        // CREATE USER


        $data =  array(
            'status' => 'error',
            'code' => '404',
            'message' => 'User not Registered'
        );

        return response()->json($data,$data['code']);
    }

   public function login(Request $request){
        return "Register User Function";
    }
    public function update(Request $request){
       return "Update User Function";
    }



}
