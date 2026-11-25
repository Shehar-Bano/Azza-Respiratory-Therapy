<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\SupportContact;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SupportContactWebController extends Controller
{
    /**
     * Display the current support contact details (View & Edit).
     */
    public function index(): View
    {
        $contact = SupportContact::first();

        if (!$contact) {
            $contact = SupportContact::create(SupportContact::defaultValues());
        }

        return view('admin.support_contact.index', [
            'contact' => $contact,
        ]);
    }

    /**
     * Update support contact details.
     */
    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'support_email' => ['required', 'email', 'max:255'],
            'support_phone' => ['required', 'string', 'max:100'],
            'working_hours' => ['required', 'string', 'max:255'],
            'support_options' => ['nullable', 'string'],
        ]);

        $options = collect(preg_split('/\r\n|\r|\n/', (string) ($validated['support_options'] ?? '')))
            ->map(fn ($line) => trim($line))
            ->filter(fn ($line) => $line !== '')
            ->values()
            ->all();

        $contact = SupportContact::first();
        if (!$contact) {
            $contact = new SupportContact();
        }

        $contact->fill([
            'support_email' => $validated['support_email'],
            'support_phone' => $validated['support_phone'],
            'working_hours' => $validated['working_hours'],
            'support_options' => $options,
        ]);
        $contact->save();

        return redirect()
            ->route('admin.support-contact.index')
            ->with('success', 'Support contact details updated successfully.');
    }
}
