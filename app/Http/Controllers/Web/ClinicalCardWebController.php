<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\ClinicalCard;
use App\Models\ClinicalCardImage;
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
        $query = ClinicalCard::with('images');

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

        $cards = $query->paginate($perPage)->withQueryString();

        return view('admin.cards.index', [
            'cards' => $cards,
            'search' => $request->input('search', ''),
            'sortBy' => $sortBy,
            'sortOrder' => $sortOrder,
            'perPage' => $perPage,
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
            'document' => 'required|file|mimes:pdf|max:10240',
            'images' => 'nullable|array',
            'images.*' => 'file|mimes:jpeg,jpg,png,gif,webp|max:5120',
        ]);

        $documentPath = null;
        if ($request->hasFile('document')) {
            $file = $request->file('document');
            $documentName = time() . '_card_doc_' . preg_replace('/[^A-Za-z0-9\._-]/', '', $file->getClientOriginalName());
            $file->move(public_path('uploads/cards/documents'), $documentName);
            $documentPath = 'uploads/cards/documents/' . $documentName;
        }

        $card = ClinicalCard::create([
            'title' => $request->title,
            'description' => $request->description,
            'image' => null,
            'document' => $documentPath,
        ]);

        $uploadedImages = [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $index => $file) {
                $imageName = time() . '_' . $index . '_card_img_' . preg_replace('/[^A-Za-z0-9\._-]/', '', $file->getClientOriginalName());
                $file->move(public_path('uploads/cards/images'), $imageName);
                $imgPath = 'uploads/cards/images/' . $imageName;

                ClinicalCardImage::create([
                    'clinical_card_id' => $card->id,
                    'image' => $imgPath,
                ]);

                $uploadedImages[] = $imgPath;
            }

            if (!empty($uploadedImages)) {
                $card->update(['image' => $uploadedImages[0]]);
            }
        }

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
            'document' => 'nullable|file|mimes:pdf|max:10240',
            'images' => 'nullable|array',
            'images.*' => 'file|mimes:jpeg,jpg,png,gif,webp|max:5120',
        ]);

        $data = [
            'title' => $request->title,
            'description' => $request->description,
        ];

        if ($request->hasFile('document')) {
            if ($card->document) {
                if (File::exists(public_path($card->document))) {
                    File::delete(public_path($card->document));
                } elseif (File::exists(public_path('uploads/cards/documents/' . $card->document))) {
                    File::delete(public_path('uploads/cards/documents/' . $card->document));
                }
            }
            $file = $request->file('document');
            $documentName = time() . '_card_doc_' . preg_replace('/[^A-Za-z0-9\._-]/', '', $file->getClientOriginalName());
            $file->move(public_path('uploads/cards/documents'), $documentName);
            $data['document'] = 'uploads/cards/documents/' . $documentName;
        }

        $card->update($data);

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $index => $file) {
                $imageName = time() . '_' . $index . '_card_img_' . preg_replace('/[^A-Za-z0-9\._-]/', '', $file->getClientOriginalName());
                $file->move(public_path('uploads/cards/images'), $imageName);
                $imgPath = 'uploads/cards/images/' . $imageName;

                ClinicalCardImage::create([
                    'clinical_card_id' => $card->id,
                    'image' => $imgPath,
                ]);
            }
        }

        // Sync primary image if main image attribute is empty or updated
        $firstImage = $card->images()->first();
        if ($firstImage) {
            $card->update(['image' => $firstImage->image]);
        }

        return redirect()->route('admin.cards.index')->with('success', 'Clinical Card updated successfully.');
    }

    /**
     * Remove specified clinical card from database.
     */
    public function destroy(ClinicalCard $card): RedirectResponse
    {
        // Delete related images from storage
        foreach ($card->images as $imgRecord) {
            if (File::exists(public_path($imgRecord->image))) {
                File::delete(public_path($imgRecord->image));
            }
        }

        if ($card->image) {
            if (File::exists(public_path($card->image))) {
                File::delete(public_path($card->image));
            } elseif (File::exists(public_path('uploads/cards/images/' . $card->image))) {
                File::delete(public_path('uploads/cards/images/' . $card->image));
            }
        }

        if ($card->document) {
            if (File::exists(public_path($card->document))) {
                File::delete(public_path($card->document));
            } elseif (File::exists(public_path('uploads/cards/documents/' . $card->document))) {
                File::delete(public_path('uploads/cards/documents/' . $card->document));
            }
        }

        $card->delete();

        return redirect()->route('admin.cards.index')->with('success', 'Clinical Card deleted successfully.');
    }

    /**
     * Remove an individual image from a clinical card.
     */
    public function destroyImage(ClinicalCardImage $image): RedirectResponse
    {
        $cardId = $image->clinical_card_id;
        $card = ClinicalCard::find($cardId);

        if (File::exists(public_path($image->image))) {
            File::delete(public_path($image->image));
        }

        $image->delete();

        if ($card) {
            $firstRemaining = $card->images()->first();
            $card->update(['image' => $firstRemaining ? $firstRemaining->image : null]);
        }

        return redirect()->back()->with('success', 'Image removed successfully.');
    }
}
