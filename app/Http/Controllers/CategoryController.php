<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CategoryController extends Controller
{
    
    public function create(Request $request){
        return "Create Category Function";
    }


    public function update(Request $request){
       return "Update Category Function";
    }

    public function list(Request $request){
        return "List Category Function";
    }

    public function delete(Request $request){
       return "Delete Category Function";
    }
}
