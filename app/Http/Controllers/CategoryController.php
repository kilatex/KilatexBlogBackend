<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use  App\Models\Category;

class CategoryController extends Controller
{
    public function index(Request $request){
        $categories = Category::all();

        return response()->json($categories);
    }

    public function show($id){

        $category = Category::find($id)->first();

       
        if(is_object($category)){
            $data = array(
                'status' => 'success',
                'code' => '200',
                'category' => $category
            );
        }else{
            $data = array(
                'status' => 'error',
                'code' => '404',
                'message' => 'category not found'
            );  
    
        }
       
        return response()->json($data);
    }


    public function create(Request $request){
        return "Create Category Function";
    }


    public function update(Request $request){
       return "Update Category Function";
    }

   

    public function delete(Request $request){
       return "Delete Category Function";
    }
}
