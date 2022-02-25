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


    public function store(Request $request){

        // GET INFO
        $json = $request->input('json');
        $params_array = json_decode($json,true);

  

        if(!empty($params_array)){
            
            // VALIDATE INFO
            $validate = \Validator::make($params_array,[
                'name' => 'required|string'
            ]);

            if(!$validate->fails()){
                // SAVE CATEGORY
                $category = new Category();
                $category->name = $params_array['name'];

                $category->save();


                $data = array(
                    'status' => 'success',
                    'code' => '200',
                    'message' => 'Category  registered',
                    'category' => $category->name
                );
            }else{
                $data = array(
                    'status' => 'error',
                    'code' => '400',
                    'message' => 'Category not registered',
                    'error' => $validate->errors()
                );
            }
        

            
        }else{
            $data = array(
                'status' => 'error',
                'code' => '400',
                'message' => 'Category not registered'
            );
        }
   


        // RETURN RESULT

        return response()->json($data);
    }



    public function update($id,Request $request){
            // GET INFO
            $json = $request->input('json');
            $params_array = json_decode($json,true);
    
      
    
            if(!empty($params_array)){
                
                // VALIDATE INFO
                $validate = \Validator::make($params_array,[
                    'name' => 'required|string'
                ]);
    
                if(!$validate->fails()){
                    // SAVE CATEGORY
                    $category = Category::find($id);
                    $category->name = $params_array['name'];
    
                    $category->update();
    
    
                    $data = array(
                        'status' => 'success',
                        'code' => '200',
                        'message' => 'Category  updated',
                        'category' => $category->name
                    );

                }else{
                    $data = array(
                        'status' => 'error',
                        'code' => '400',
                        'message' => 'Category NOT Updated',
                        'error' => $validate->errors()
                    );
                }
            
    
                
            }else{
                $data = array(
                    'status' => 'error',
                    'code' => '400',
                    'message' => 'Category not updated'
                );
            }
       
    
    
            // RETURN RESULT
    
            return response()->json($data);
    }


}
