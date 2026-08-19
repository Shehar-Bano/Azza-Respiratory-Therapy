<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\View\View;

class ArticleWebController extends Controller
{
    /**
     * Display listing of articles with search and sorting.
     */
    public function index(Request $request): View
    {
        $query = Article::with('category');

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
        if (!in_array($perPage, [10, 20, 30, 50, 100])) {
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
            'image' => 'nullable|file|mimes:jpeg,jpg,png,gif,webp|max:5120',
            'document' => 'nullable|file|mimes:pdf,doc,docx,txt,zip|max:10240',
        ]);

        $imageName = null;
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $imageName = time() . '_img_' . preg_replace('/[^A-Za-z0-9\._-]/', '', $file->getClientOriginalName());
            $file->move(public_path('uploads/articles/images'), $imageName);
        }

        $documentName = null;
        if ($request->hasFile('document')) {
            $file = $request->file('document');
            $documentName = time() . '_doc_' . preg_replace('/[^A-Za-z0-9\._-]/', '', $file->getClientOriginalName());
            $file->move(public_path('uploads/articles/documents'), $documentName);
        }

        Article::create([
            'category_id' => $request->category_id,
            'title' => $request->title,
            'description' => $request->description,
            'image' => $imageName ?? 'abg_article.png',
            'document' => $documentName ?? 'abg_clinical_manual.pdf',
        ]);

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
            'image' => 'nullable|file|mimes:jpeg,jpg,png,gif,webp|max:5120',
            'document' => 'nullable|file|mimes:pdf,doc,docx,txt,zip|max:10240',
        ]);

        $data = [
            'category_id' => $request->category_id,
            'title' => $request->title,
            'description' => $request->description,
        ];

        if ($request->hasFile('image')) {
            if ($article->image && File::exists(public_path('uploads/articles/images/' . $article->image))) {
                File::delete(public_path('uploads/articles/images/' . $article->image));
            }
            $file = $request->file('image');
            $imageName = time() . '_img_' . preg_replace('/[^A-Za-z0-9\._-]/', '', $file->getClientOriginalName());
            $file->move(public_path('uploads/articles/images'), $imageName);
            $data['image'] = $imageName;
        }

        if ($request->hasFile('document')) {
            if ($article->document && File::exists(public_path('uploads/articles/documents/' . $article->document))) {
                File::delete(public_path('uploads/articles/documents/' . $article->document));
            }
            $file = $request->file('document');
            $documentName = time() . '_doc_' . preg_replace('/[^A-Za-z0-9\._-]/', '', $file->getClientOriginalName());
            $file->move(public_path('uploads/articles/documents'), $documentName);
            $data['document'] = $documentName;
        }

        $article->update($data);

        return redirect()->route('admin.articles.index')->with('success', 'Article updated successfully.');
    }

    /**
     * Remove specified article from database.
     */
    public function destroy(Article $article): RedirectResponse
    {
        if ($article->image && File::exists(public_path('uploads/articles/images/' . $article->image))) {
            File::delete(public_path('uploads/articles/images/' . $article->image));
        }

        if ($article->document && File::exists(public_path('uploads/articles/documents/' . $article->document))) {
            File::delete(public_path('uploads/articles/documents/' . $article->document));
        }

        $article->delete();

        return redirect()->route('admin.articles.index')->with('success', 'Article deleted successfully.');
    }
}
