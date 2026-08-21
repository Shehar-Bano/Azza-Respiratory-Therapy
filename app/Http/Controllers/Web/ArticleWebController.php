<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\ArticleImage;
use App\Models\Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class ArticleWebController extends Controller
{
    /**
     * Display listing of articles with search and sorting.
     */
    public function index(Request $request): View
    {
        $query = Article::with(['category', 'images']);

        // Global Search
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Sorting
        $sortBy = $request->input('sort_by', 'id');
        $sortOrder = strtolower($request->input('sort_order', 'desc')) === 'asc' ? 'asc' : 'desc';

        $allowedSorts = ['id', 'title', 'description', 'created_at'];
        if (in_array($sortBy, $allowedSorts)) {
            $query->orderBy($sortBy, $sortOrder);
        } else {
            $query->latest();
        }

        // Dynamic Per Page
        $perPage = (int) $request->input('per_page', 10);
        if (! in_array($perPage, [10, 20, 30, 50, 100])) {
            $perPage = 10;
        }

        $articles = $query->paginate($perPage)->withQueryString();
        $categories = Category::all();

        return view('admin.articles.index', [
            'articles' => $articles,
            'categories' => $categories,
            'search' => $request->input('search', ''),
            'sortBy' => $sortBy,
            'sortOrder' => $sortOrder,
            'perPage' => $perPage,
        ]);
    }

    /**
     * Store a newly created article.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'category_id' => 'nullable|exists:categories,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'document' => 'required|file|mimes:pdf|max:10240',
            'images' => 'nullable|array',
            'images.*' => 'file|mimes:jpeg,jpg,png,gif,webp|max:5120',
        ]);

        $documentPath = null;
        if ($request->hasFile('document')) {
            $file = $request->file('document');
            $documentName = time().'_doc_'.preg_replace('/[^A-Za-z0-9\._-]/', '', $file->getClientOriginalName());
            $file->move(public_path('uploads/articles/documents'), $documentName);
            $documentPath = 'uploads/articles/documents/'.$documentName;
        }

        $article = Article::create([
            'category_id' => $request->category_id,
            'title' => $request->title,
            'description' => $request->description,
            'document' => $documentPath,
        ]);

        $uploadedImages = [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $index => $file) {
                $imageName = time().'_'.$index.'_img_'.preg_replace('/[^A-Za-z0-9\._-]/', '', $file->getClientOriginalName());
                $file->move(public_path('uploads/articles/images'), $imageName);
                $imgPath = 'uploads/articles/images/'.$imageName;

                $articleImage = ArticleImage::create([
                    'article_id' => $article->id,
                    'image' => $imgPath,
                ]);

                $uploadedImages[] = $imgPath;
            }

            if (! empty($uploadedImages)) {
                $article->update(['image' => $uploadedImages[0]]);
            }
        }

        return redirect()->route('admin.articles.index')->with('success', 'Article created successfully.');
    }

    /**
     * Update specified article.
     */
    public function update(Request $request, Article $article): RedirectResponse
    {
        $request->validate([
            'category_id' => 'nullable|exists:categories,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'document' => 'nullable|file|mimes:pdf|max:10240',
            'images' => 'nullable|array',
            'images.*' => 'file|mimes:jpeg,jpg,png,gif,webp|max:5120',
        ]);

        $data = [
            'category_id' => $request->category_id,
            'title' => $request->title,
            'description' => $request->description,
        ];

        if ($request->hasFile('document')) {
            if ($article->document) {
                if (File::exists(public_path($article->document))) {
                    File::delete(public_path($article->document));
                } elseif (File::exists(public_path('uploads/articles/documents/'.$article->document))) {
                    File::delete(public_path('uploads/articles/documents/'.$article->document));
                }
            }
            $file = $request->file('document');
            $documentName = time().'_doc_'.preg_replace('/[^A-Za-z0-9\._-]/', '', $file->getClientOriginalName());
            $file->move(public_path('uploads/articles/documents'), $documentName);
            $data['document'] = 'uploads/articles/documents/'.$documentName;
        }

        $article->update($data);

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $index => $file) {
                $imageName = time().'_'.$index.'_img_'.preg_replace('/[^A-Za-z0-9\._-]/', '', $file->getClientOriginalName());
                $file->move(public_path('uploads/articles/images'), $imageName);
                $imgPath = 'uploads/articles/images/'.$imageName;

                ArticleImage::create([
                    'article_id' => $article->id,
                    'image' => $imgPath,
                ]);
            }
        }

        // Sync primary image if main image attribute is empty or updated
        $firstImage = $article->images()->first();
        if ($firstImage) {
            $article->update(['image' => $firstImage->image]);
        }

        return redirect()->route('admin.articles.index')->with('success', 'Article updated successfully.');
    }

    /**
     * Remove specified article from database.
     */
    public function destroy(Article $article): RedirectResponse
    {
        // Delete related images from storage
        foreach ($article->images as $imgRecord) {
            if (File::exists(public_path($imgRecord->image))) {
                File::delete(public_path($imgRecord->image));
            }
        }

        if ($article->image) {
            if (File::exists(public_path($article->image))) {
                File::delete(public_path($article->image));
            } elseif (File::exists(public_path('uploads/articles/images/'.$article->image))) {
                File::delete(public_path('uploads/articles/images/'.$article->image));
            }
        }

        if ($article->document) {
            if (File::exists(public_path($article->document))) {
                File::delete(public_path($article->document));
            } elseif (File::exists(public_path('uploads/articles/documents/'.$article->document))) {
                File::delete(public_path('uploads/articles/documents/'.$article->document));
            }
        }

        $article->delete();

        return redirect()->route('admin.articles.index')->with('success', 'Article deleted successfully.');
    }

    /**
     * Remove an individual image from an article.
     */
    public function destroyImage(ArticleImage $image)
    {
        $articleId = $image->article_id;
        $article = Article::find($articleId);

        if (File::exists(public_path($image->image))) {
            File::delete(public_path($image->image));
        }

        $image->delete();

        if ($article && Schema::hasColumn('articles', 'image')) {
            $firstRemaining = $article->images()->first();
            $article->update(['image' => $firstRemaining ? $firstRemaining->image : null]);
        }

        if (request()->wantsJson() || request()->ajax()) {
            return response()->json([
                'status' => true,
                'message' => 'Image removed successfully.',
            ]);
        }

        return redirect()->back()->with('success', 'Image removed successfully.');
    }
}
