<?php

declare(strict_types=1);

namespace App\Controllers;

use Core\View;
use Core\Session;
use Core\Validator;
use Core\Http\Request;
use Core\Http\RedirectResponse;
use App\Models\Post;

class PostsController extends BaseController
{

    /**
     * Display all posts
     */
    public function index(Request $request)
    {
        // VEFORE: $posts = $this->postModel->all();
        // AFTER: Even cleaner with static method
        $posts = Post::allWithAuthors();

        return View::render('posts/index', [
            'pageTitle' => 'All Posts',
            'posts' => $posts
        ]);
    }

    /**
     * Show a single post
     */
    public function show(Request $request, $id)
    {

        // BEFORE: $post = $this->postModel->findById($id);
        // AFTER: Much cleaner!
        $post = Post::findWithAuthor($id);

        return View::render('posts/show', [
            'pageTitle' => $post ? $post->title : 'Post Not Found',
            'post' => $post
        ]);
    }

    /**
     * Show create post form
     */
    public function create(Request $request)
    {
        return View::render('posts/create', [
            'pageTitle' => 'Create New Post'
        ]);
    }

    /**
     * Store a new post
     */
    public function store(Request $request)
    {
        Post::createPost($request);

        Session::flash('success', 'Post created successfully!');
        return new RedirectResponse(route('posts.index'));
    }

    /**
     * SHow edit form for a post
     */
    public function edit(Request $request, string $id)
    {
        $post = Post::findWithAuthor($id);

        return View::render('posts/edit', [
            'pageTitle' => 'Edit Post',
            'post' => $post
        ]);
    }

    /**
     * Update a post
     */
    public function update(Request $request, string $id)
    {
        $post = Post::find($id);

        $post->fill([
            'title' => $request->input('title'),
            'content' => $request->input('content'),
        ]);

        if ($request->hasFile('image')) {
            Post::saveImage($request, $post);
        }

        $post->save();

        // Check if the post was saved successfully
        if (!$post->id) {
            Session::flash('error', 'Failed to update post. Please try again.');
            return new RedirectResponse(route('posts.edit', ['id' => $id]));
        } else {
            Session::flash('success', 'Post updated successfully!');
        }

        return new RedirectResponse(route('posts.show', ['id' => $post->id]));
    }

    /**
     * Delete a post
     */
    public function destroy(Request $request, string $id)
    {
        $post = Post::find($id);
        if ($post) {
            $post->delete();
            Session::flash('success', 'Post deleted successfully!');
        } else {
            Session::flash('error', 'Post not found.');
        }
        return new RedirectResponse(route('posts.index'));
    }
}
