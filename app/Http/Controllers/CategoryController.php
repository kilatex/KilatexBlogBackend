<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function prueba(Request $request){
        echo "<h1> CONTROLADOR CATEGORY -> PRUEBA";
        die();
    }
}
