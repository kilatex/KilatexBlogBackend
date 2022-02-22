<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PostController extends Controller
{
      public function prueba(Request $request){
        echo "<h1> CONTROLADOR POST -> PRUEBA </h1>";
        die();
    }
}
