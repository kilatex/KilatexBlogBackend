<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UserController extends Controller
{
    public function prueba(Request $request){
        echo "<h1> CONTROLADOR USER -> PRUEBA";
        die();
    }
}
