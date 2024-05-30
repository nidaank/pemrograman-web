<?php

namespace App\Http\Controllers\Api;

use App\Models\Post;
use App\Http\Controllers\Controller;
use App\Http\Resources\PostResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
class PostController extends Controller
{
    /**
    * index
    * @return-void
    */
    public function index() {
        //get all posts 
        $posts = Post::latest()->paginate(5);

        //return collection of posts as a resource 
        return new PostResource(true, 'List Data Posts', $posts);
    }

    /**
     * store
     * 
     * @param-mixed $request
     * @return-void
     */
    public function store(Request $request)
    {
    // Define validation rules
    $validator = Validator::make($request->all(), [
        'image'   => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        'title'   => 'required|string',
        'content' => 'required|string',
    ]);

    // Check if validation fails
    if ($validator->fails()) {
        return response()->json($validator->errors(), 422);
    }

    // Upload image
    $image = $request->file('image');
    $image->storeAs('public/posts', $image->hashName());

    // Create post
    $post = Post::create([
        'image' => $image->hashName(),
        'title' => $request->title,
        'content' => $request->content,
    ]);

    // Return response
    return new PostResource(true, 'Data Post Berhasil Ditambahkan!', $post);
    }

    /**
     * show
     * 
     * @param-mixed $id
     * @return-void
     */
    public function show($id)
    {
    //find post by ID
    $post = Post::find($id);

    //return single post as a resource
    return new PostResource(true, 'Detail Data Post!', $post);
    }

    /**
     * update
     * 
     */
    public function update(Request $request, $id)
    {
    //define validation rules
    $validator = Validator::make($request->all(), [
        'title'   => 'required',
        'content' => 'required',
    ]);

    //check if validation fails
    if ($validator->fails()) {
        return response()->json($validator->errors(), 422);
    }

    //find post by ID
    $post = Post::find($id);

    //check if image is not empty
    if ($request->hasFile('image')) {

        //upload image
        $image = $request->file('image');
        $image->storeAs('public/posts', $image->hashName());

        //delete old image
        Storage::delete('public/posts/'. basename($post->image));

        //update post with new image
        $post->update([
            'image'   => $image->hashName(),
            'title'   => $request->title,
            'content' => $request->content,
        ]);

    } else {
        //update post without image
        $post->update([
            'title'   => $request->title,
            'content' => $request->content,
        ]);
    }
    //return response
    return new PostResource(true, 'Data Post Berhasil Diubah!', $post);
    }

    /**
     * destroy
     * 
     */
    public function destroy($id)
    {
        //find post by ID
        $post = Post::find($id);

        //delete image
        Storage::delete('public/posts/' .basename($post->image));

        //delete post
        $post->delete();

        //return reponse
        return new PostResource(true, 'Data Poat Berhasil Dihapus!', null);
    }
}