<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use Illuminate\Http\Request;

class BlogController extends Controller
{
    public function index()
    {
        $blogs = Blog::with(['categories', 'tags', 'user'])->paginate(12); // 12 blogs por página
        return view('blog.index', compact('blogs'));
    }

    public function show($slug)
    {
        $blog = Blog::with(['categories', 'tags'])->where('slug', $slug)->firstOrFail();
        $categoryCounts = Blog::whereHas('categories', function ($query) use ($blog) {
            $query->whereIn('category_id', $blog->categories->pluck('id'));
        })->count();
        $tagCounts = Blog::whereHas('tags', function ($query) use ($blog) {
            $query->whereIn('tag_id', $blog->tags->pluck('id'));
        })->count();
        return view('blog.show', compact('blog', 'categoryCounts', 'tagCounts'));
    }

    public function filterByCategory($category)
    {
        $blogs = Blog::with(['categories', 'tags', 'user'])
            ->whereHas('categories', function ($query) use ($category) {
                $query->where('name', $category);
            })->paginate(12);
        return view('blog.index', compact('blogs'));
    }

    public function filterByTag($tag)
    {
        $blogs = Blog::with(['categories', 'tags', 'user'])
            ->whereHas('tags', function ($query) use ($tag) {
                $query->where('name', $tag);
            })->paginate(12);
        return view('blog.index', compact('blogs'));
    }
}