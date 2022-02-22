<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UserController extends Controller
{

    public function register(Request $request){
        $name =  $request->input('name');
        $surname = $request->input('surname');
        return "Your Name Is $name - $surname ";
    }

   public function login(Request $request){
        return "Register User Function";
    }
    public function update(Request $request){
       return "Update User Function";
    }



}
