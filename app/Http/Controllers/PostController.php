<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PostController extends Controller
{
    
     public function create(Request $request){
         return "Create Post Function";
     }


     public function update(Request $request){
        return "Update Post Function";
     }

     public function list(Request $request){
         return "List Posts Function";
     }

     public function delete(Request $request){
        return "Delete Post Function";
     }


}
