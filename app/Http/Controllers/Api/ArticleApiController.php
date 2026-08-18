<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ArticleResource;
use App\Models\Article;
use Illuminate\Http\JsonResponse;

class ArticleApiController extends Controller
{
    /**
     * Get list of articles.
     */
    public function getArticles(): JsonResponse
    {
        $articles = Article::latest()->get();

        return response()->json([
            'status' => 'success',
            'message' => 'Articles fetched successfully',
            'artical' => ArticleResource::collection($articles),
        ], 200);
    }
}
