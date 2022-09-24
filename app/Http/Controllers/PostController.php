<?php

namespace App\Http\Controllers;

use Illuminate\Http\Response;
use Illuminate\Http\Request;
use  App\Models\Post;
use Illuminate\Support\Facades\Storage;

class PostController extends Controller
{

    public function __construct()
    {
        $this->middleware('api.auth', ['except'
        => ['index', 'show', 'getImage', 'store', 'postsByUser']]);
    }

    public function index(Request $request)
    {
        $posts = Post::with('user')->with('category')->paginate('10');
        $data = array(
            'status' => 'success',
            'code' => '200',
            'posts' => $posts,

        );
        return $data;
    }

    public function show($id)
    {
        $post = Post::find($id) ? Post::find($id)->load('category') : null;
        if (is_object($post)) {
            $data = array(
                'status' => 'success',
                'code' => '200',
                'post' => $post
            );
        } else {
            $data = array(
                'status' => 'error',
                'code' => '404',
                'message' => 'Post not found'
            );
        }
        return response()->json($data);
    }

    public function store(Request $request)
    {
        if (!empty($request)) {
            $validate = \Validator::make($request->all(), [
                'title' => 'required|string|max:256',
                'content' => 'required|string',
                'category' => 'required|integer',
                'file0' => 'required'
            ]);
            if ($validate->fails() /*|| $validate_image->fails() */) {
                $data = array(
                    'status' => 'error',
                    'code' => '400',
                    'message' => 'Post not created',
                    'error' => $validate->errors(),
                );
            } else {
                $post = new Post(); // Create Post
                $post->title = $request->input('title');
                $post->content = $request->input('content');
                $post->user_id = $request->input('user_id');
                $post->category_id = $request->input('category');
                $image = $request->file('file0');

                $image_path_name = time() . $image->getClientOriginalName(); // SAVE IMAGE
                Storage::disk('images')->put($image_path_name, \File::get($image));
                $post->image = $image_path_name;
                $post->save();
                $data = array(
                    'status' => 'success',
                    'code' => '200',
                    'message' => 'Post created'
                );
            }
        } else {
            $data = array(
                'status' => 'error',
                'code' => '400',
                'message' => 'Post not created'
            );
        }
        return response()->json($data);
    }

    public function update($id, Request $request)
    {
        $json = $request->input('json');
        $params_array = json_decode($json, true);
        $user = $this->getIdentity($request);
        $post = Post::where('id', $id)->where('user_id', $user->sub)->first();
        if (!empty($params_array) && is_object($post)) {
            $validate = \Validator::make($params_array, [
                'category' => 'string',
                'title' => 'string',
                'content' => 'string',
                'image' => 'string'
            ]);
            if (!$validate->fails()) {
                if ($params_array['category'] != null) {
                    $post->category_id = $params_array['category'];
                }
                // UPDATE POST
                $post->update($params_array);
                $data = array(
                    'status' => 'success',
                    'code' => '200',
                    'changes' => $post
                );
            } else {
                $data = array(
                    'status' => 'error',
                    'code' => '400',
                    'message' => 'Post NOUP updated',
                    'error' => $validate->errors()
                );
            }
        } else {
            $data = array(
                'status' => 'error',
                'code' => '400',
                'message' => 'Post not updated'
            );
        }
        return response()->json($data);
    }

    public function destroy($id, Request $request)
    {
        $user = $this->getIdentity($request);
        $post = Post::where('id', $id)->where('user_id', $user->sub)->first();
        if (is_object($post)) {
            $post->delete();
            $data = array(
                'status' => 'success',
                'code' => '200',
                'message' => 'Post deleted'
            );
        } else {
            $data = array(
                'status' => 'error',
                'code' => '404',
                'Message' => 'Post not found'
            );
        }
        // RETURN
        return response()->json($data);
    }

    public function getImage($filename)
    {
        $isset = Storage::disk('images')->exists($filename);
        if ($isset) {
            // GET IMAGE
            $file =  Storage::disk('images')->get($filename);
            return new Response($file, 200);
        }
        $data = array(
            'status' => 'error',
            'code' => '404',
            'Message' => 'FILE not found'
        );
        return response()->json($data);
        // RETURN IMAGE OR ERROR
    }

    public function postsByUser($id)
    {
        $posts = Post::where('user_id', $id)->orderBy('id', 'DESC')->paginate('5');
        $data = array(
            'status' => 'success',
            'code' => '200',
            'posts' => $posts
        );
        return response()->json($data);
    }

    public function postsByCategory($id)
    {
        $posts = Post::where('category_id', $id)->get();
        $post_json = json_decode($posts);
        if (!empty($post_json)) {
            $data = array(
                'status' => 'success',
                'code' => '200',
                'posts' => $posts
            );
        } else {
            $data = array(
                'status' => 'error',
                'code' => '400',
                'Message' => "This Category has no posts"
            );
        }
        return response()->json($data);
    }
}
