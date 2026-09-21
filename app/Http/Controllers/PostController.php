<?php

namespace App\Http\Controllers;

use App\Http\Requests\PostCreateRequest;
use App\Http\Requests\PostUpdateRequest;
use App\Models\Post;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = auth()->user();
        
        $query = Post::with('user')
            ->where('published_at','<=',now())
            ->withCount('claps')
            ->latest();
        if($user) {
            $ids = $user->following()->pluck('users.id');
            $query->whereIn('user_id', $ids);
        }
    
        $posts = $query->paginate(5);

        return view('post.index', [
            'posts' => $posts,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = Category::get();
        return view('post.create',[
            'categories'=>$categories,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(PostCreateRequest $request)
    {
        $data = $request->validated();

        $image = $data['image'];
        $data['user_id'] = Auth::id();
        $data['slug'] = Str::slug($data['title']);

        $imagePath = $image->store('posts','public');
        $data['image'] = $imagePath;

        Post::create($data);

        return redirect()->route('myPosts');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $username,Post $post)
    {
        return view('post.show', [
            'post' => $post,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Post $post)
    {
        if($post->user_id !== Auth::id()){
            abort(403);
        }
        $categories = Category::get();
        return view('post.edit', [
            'post' => $post,
            'categories' => $categories,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(PostUpdateRequest $request, Post $post)
    {
        if($post->user_id !== Auth::id()){
            abort(403);
        }
        $data = $request->validated();
        $data['slug'] = Str::slug($data['title']);
        if ($data['image'] ?? false) {
            $image = $data['image'];
            $imagePath = $image->store('posts','public');
            $data['image'] = $imagePath;
        }
        $post->update($data);
        return redirect()->route('myPosts');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Post $post)
    {
        if($post->user_id !== Auth::id()){
            abort(403);
        }
        $post->delete();

        return redirect()->route('dashboard');
    }

    public function category(Category $category){
        $user = auth()->user();
        $query = $category->posts()
            ->where('published_at','<=',now())
            ->with('user')
            ->withCount('claps')
            ->latest();
        
        if($user) {
            $ids = $user->following()->pluck('users.id');
            $query->whereIn('user_id', $ids);
        }

        $posts = $query->Paginate(5);

        return view('post.index', [
            'posts' => $posts,
        ]);
    }
    public function myPosts(){
        $user = auth()->user();
        $posts = $user->posts()
            ->with('user')
            ->withCount('claps')
            ->latest()
            ->Paginate(5);

        return view('post.index', [
            'posts' => $posts,
        ]);
    }
}
