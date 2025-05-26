<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdatePostRequest;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Gate;

class PostController extends Controller
{
   public function __construct()
    {
        $this->middleware('auth:sanctum')->except(['index', 'show']);
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Post::all();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
      $fields = $request->validate([
            'title' => 'required|string|max:255',
            'body' => 'required|string',
        ]);

        // $post = Post::create($fields);
          $post = $request->user()->posts()->create($fields);
        return  $post;

    }

    /**
     * Display the specified resource.
     */
    public function show(Post $post)
    {
        return  $post;
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Post $post)
    {
         Gate::authorize('modify', $post);
          $fields = $request->validate([
            'title' => 'required|string|max:255',
            'body' => 'required|string',
        ]);

        $post ->update ($fields);
        return  $post;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Post $post)
    {
         Gate::authorize('modify', $post);
        $post->delete();
        return (['message' => 'Post deleted successfully']);
    }
}
