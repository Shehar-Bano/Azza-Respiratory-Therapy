<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ArticleResource;
use App\Models\Article;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ArticleApiController extends Controller
{
    /**
     * Get list of all articles with multiple images.
     */
    public function getArticles(): JsonResponse
    {
        $articles = Article::with(['category', 'images'])->latest()->get();

        return response()->json([
            'status' => 'success',
            'message' => 'Articles fetched successfully',
            'artical' => ArticleResource::collection($articles),
        ], 200);
    }

    /**
     * Get articles filtered by category_id with multiple images.
     */
    public function getCategoryArticles(Request $request): JsonResponse
    {
        $query = Article::with(['category', 'images']);

        if ($request->has('category_id') && $request->category_id !== null) {
            $query->where('category_id', $request->category_id);
        }

        $articles = $query->latest()->get();

        return response()->json([
            'status' => 'success',
            'message' => 'Category articles fetched successfully',
            'artical' => ArticleResource::collection($articles),
        ], 200);
    }
}
