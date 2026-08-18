<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\ClinicalCard;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\View\View;

class ClinicalCardWebController extends Controller
{
    /**
     * Display listing of clinical cards with search and sorting.
     */
    public function index(Request $request): View
    {
        $query = ClinicalCard::query();

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

        $cards = $query->paginate(10)->withQueryString();

        return view('admin.cards.index', [
            'cards' => $cards,
            'search' => $request->input('search', ''),
            'sortBy' => $sortBy,
            'sortOrder' => $sortOrder,
        ]);
    }

    /**
     * Store a newly created clinical card.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'image' => 'nullable|file|mimes:jpeg,jpg,png,gif,webp|max:5120',
            'document' => 'nullable|file|mimes:pdf,doc,docx,txt,zip|max:10240',
        ]);

        $imageName = null;
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $imageName = time() . '_card_img_' . preg_replace('/[^A-Za-z0-9\._-]/', '', $file->getClientOriginalName());
            $file->move(public_path('uploads/cards/images'), $imageName);
        }

        $documentName = null;
        if ($request->hasFile('document')) {
            $file = $request->file('document');
            $documentName = time() . '_card_doc_' . preg_replace('/[^A-Za-z0-9\._-]/', '', $file->getClientOriginalName());
            $file->move(public_path('uploads/cards/documents'), $documentName);
        }

        ClinicalCard::create([
            'title' => $request->title,
            'description' => $request->description,
            'image' => $imageName ?? 'airway_card_thumb.png',
            'document' => $documentName ?? 'airway_assessment.pdf',
        ]);

        return redirect()->route('admin.cards.index')->with('success', 'Clinical Card created successfully.');
    }

    /**
     * Update specified clinical card.
     */
    public function update(Request $request, ClinicalCard $card): RedirectResponse
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'image' => 'nullable|file|mimes:jpeg,jpg,png,gif,webp|max:5120',
            'document' => 'nullable|file|mimes:pdf,doc,docx,txt,zip|max:10240',
        ]);

        $data = [
            'title' => $request->title,
            'description' => $request->description,
        ];

        if ($request->hasFile('image')) {
            if ($card->image && File::exists(public_path('uploads/cards/images/' . $card->image))) {
                File::delete(public_path('uploads/cards/images/' . $card->image));
            }
            $file = $request->file('image');
            $imageName = time() . '_card_img_' . preg_replace('/[^A-Za-z0-9\._-]/', '', $file->getClientOriginalName());
            $file->move(public_path('uploads/cards/images'), $imageName);
            $data['image'] = $imageName;
        }

        if ($request->hasFile('document')) {
            if ($card->document && File::exists(public_path('uploads/cards/documents/' . $card->document))) {
                File::delete(public_path('uploads/cards/documents/' . $card->document));
            }
            $file = $request->file('document');
            $documentName = time() . '_card_doc_' . preg_replace('/[^A-Za-z0-9\._-]/', '', $file->getClientOriginalName());
            $file->move(public_path('uploads/cards/documents'), $documentName);
            $data['document'] = $documentName;
        }

        $card->update($data);

        return redirect()->route('admin.cards.index')->with('success', 'Clinical Card updated successfully.');
    }

    /**
     * Remove specified clinical card from database.
     */
    public function destroy(ClinicalCard $card): RedirectResponse
    {
        if ($card->image && File::exists(public_path('uploads/cards/images/' . $card->image))) {
            File::delete(public_path('uploads/cards/images/' . $card->image));
        }

        if ($card->document && File::exists(public_path('uploads/cards/documents/' . $card->document))) {
            File::delete(public_path('uploads/cards/documents/' . $card->document));
        }

        $card->delete();

        return redirect()->route('admin.cards.index')->with('success', 'Clinical Card deleted successfully.');
    }
}
