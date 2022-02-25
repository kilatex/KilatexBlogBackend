<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use  App\Models\Post;

class PostController extends Controller
{
    
     public function __construct(){
        $this->middleware('api.auth', ['except' => ['index','show']]);
     }

     public function index(Request $request){
        $posts = Post::all()->load('category');

        $data = array(
            'status' => 'success',
            'code' => '200',
            'posts' => $posts
        );

        return response()->json($data);
     }

     public function show($id){
         $post = Post::find($id) ? Post::find($id)->load('category') : null ;


         if(is_object($post)){
            
             
             $data = array(
                 'status' => 'success',
                 'code' => '200',
                 'post' => $post
             );
         }else{
            $data = array(
                'status' => 'error',
                'code' => '404',
                'message' => 'Post not found'
            ); 
         }
         return response()->json($data);
     }


     public function store(Request $request){
        $json = $request->input('json',null);
        $params_array = json_decode($json,true);

        $token = $request->header('Authorization');
        $jwtAuth = new \JwtAuth();
        $user = $jwtAuth->checkToken($token,true);


       if(!empty($params_array)){

            // Validate Info
            $validate = \Validator::make($params_array,[
                'title' => 'required|string|max:256',
                'content' => 'required|string',
                'category' => 'required|integer',
                'image' => 'required'
            ]);

            // If Validate fails
            if($validate->fails()){

                $data = array(
                    'status' => 'error',
                    'code' => '400',
                    'message' => 'Post not created',
                    'error' => $validate->errors(),
                );

            }else{

                // Create Post
                $post = new Post();
                $post->title = $params_array['title'];
                $post->content = $params_array['content'];
                $post->user_id = $user->sub;
                $post->category_id = $params_array['category'];
                $post->image = $params_array['image'];
                $post->save();

                $data = array(
                    'status' => 'success',
                    'code' => '200',
                    'message' => 'Post created'
                );

            }
       }else{

            $data = array(
                'status' => 'error',
                'code' => '400',
                'message' => 'Post not created'
            );

       }

       return response()->json($data);

     }

     public function update($id, Request $request){
        
        // GET INFO and User
        $json = $request->input('json');
        $params_array = json_decode($json,true);
        $post = Post::find($id);

    
       
        if(!empty($params_array) && is_object($post) ){

            // VALIDATE INFO
            $validate = \Validator::make($params_array,[
                'category' => 'string',
                'title' => 'string',
                'content' => 'string',
                'image' => 'string'
            ]);

            if(!$validate->fails()){
                
                if($params_array['category'] != null){
                    $post->category_id = $params_array['category'];
                }
                $post->update($params_array);
                
                $data = array(
                    'status' => 'success',
                    'code' => '200',
                    'changes' => $post
                );

            }else{
                $data = array(
                    'status' => 'error',
                    'code' => '400',
                    'message' => 'Post NOUP updated',
                    'error' => $validate->errors()
                );  
            }

        }else{
            $data = array(
                'status' => 'error',
                'code' => '400',
                'message' => 'Post not updated'
            );
        }
     

        // UPDATE POST


        //RETURN
        return response()->json($data);
     }

     public function delete(Request $request){
        return "Delete Post Function";
     }


}
