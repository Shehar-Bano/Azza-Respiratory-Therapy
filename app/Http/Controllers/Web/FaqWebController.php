<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Faq;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FaqWebController extends Controller
{
    /**
     * Display listing of FAQs with search, category filter, and pagination.
     */
    public function index(Request $request): View
    {
        $query = Faq::query();

        if ($request->filled('search')) {
            $search = trim((string) $request->input('search'));
            $query->where(function ($q) use ($search) {
                $q->where('question', 'like', "%{$search}%")
                    ->orWhere('answer', 'like', "%{$search}%")
                    ->orWhere('category', 'like', "%{$search}%");
            });
        }

        if ($request->filled('category')) {
            $query->where('category', $request->input('category'));
        }

        $perPage = (int) $request->input('per_page', 10);
        if (!in_array($perPage, [10, 20, 30, 50, 100])) {
            $perPage = 10;
        }

        $faqs = $query->orderByDesc('id')->paginate($perPage)->withQueryString();
        $categories = Faq::query()->select('category')->distinct()->orderBy('category')->pluck('category');

        return view('admin.faqs.index', [
            'faqs' => $faqs,
            'categories' => $categories,
            'search' => $request->input('search', ''),
            'selectedCategory' => $request->input('category', ''),
            'perPage' => $perPage,
        ]);
    }

    /**
     * Store a newly created FAQ.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'category' => ['required', 'string', 'max:150'],
            'question' => ['required', 'string', 'max:1000'],
            'answer' => ['required', 'string', 'max:10000'],
        ]);

        Faq::create($validated);

        return redirect()
            ->route('admin.faqs.index')
            ->with('success', 'FAQ added successfully.');
    }

    /**
     * Update the specified FAQ.
     */
    public function update(Request $request, Faq $faq): RedirectResponse
    {
        $validated = $request->validate([
            'category' => ['required', 'string', 'max:150'],
            'question' => ['required', 'string', 'max:1000'],
            'answer' => ['required', 'string', 'max:10000'],
        ]);

        $faq->update($validated);

        return redirect()
            ->route('admin.faqs.index')
            ->with('success', 'FAQ updated successfully.');
    }

    /**
     * Remove the specified FAQ.
     */
    public function destroy(Faq $faq): RedirectResponse
    {
        $faq->delete();

        return redirect()
            ->route('admin.faqs.index')
            ->with('success', 'FAQ deleted successfully.');
    }
}
