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
     * Get paginated list of all articles with multiple images.
     */
    public function getArticles(Request $request): JsonResponse
    {
        $query = Article::with(['category', 'images']);

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        $perPage = (int) $request->input('per_page', $request->input('limit', 10));

        $articles = $query->latest()->paginate($perPage);

        return response()->json([
            'status' => 'success',
            'message' => 'Articles fetched successfully',
            'artical' => ArticleResource::collection($articles->items()),
            'pagination' => [
                'total' => $articles->total(),
                'count' => $articles->count(),
                'per_page' => $articles->perPage(),
                'current_page' => $articles->currentPage(),
                'total_pages' => $articles->lastPage(),
                'has_more_pages' => $articles->hasMorePages(),
            ],
        ], 200);
    }

    /**
     * Get paginated articles filtered by category_id with multiple images.
     */
    public function getCategoryArticles(Request $request): JsonResponse
    {
        $query = Article::with(['category', 'images']);

        if ($request->has('category_id') && $request->category_id !== null) {
            $query->where('category_id', $request->category_id);
        }

        $perPage = (int) $request->input('per_page', $request->input('limit', 10));

        $articles = $query->latest()->paginate($perPage);

        return response()->json([
            'status' => 'success',
            'message' => 'Category articles fetched successfully',
            'artical' => ArticleResource::collection($articles->items()),
            'pagination' => [
                'total' => $articles->total(),
                'count' => $articles->count(),
                'per_page' => $articles->perPage(),
                'current_page' => $articles->currentPage(),
                'total_pages' => $articles->lastPage(),
                'has_more_pages' => $articles->hasMorePages(),
            ],
        ], 200);
    }
}
