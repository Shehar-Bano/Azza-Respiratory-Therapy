<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use Illuminate\Http\JsonResponse;

class CategoryApiController extends Controller
{
    /**
     * Get list of all categories.
     */
    public function getCategories(): JsonResponse
    {
        $categories = Category::latest()->get();

        return response()->json([
            'status' => 'success',
            'message' => 'Categories fetched successfully',
            'categories' => CategoryResource::collection($categories),
        ], 200);
    }
}
