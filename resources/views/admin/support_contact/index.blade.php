@extends('layouts.admin')

@section('title', 'Support Contact')

@section('styles')
<style>
    .page-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 1.5rem;
        gap: 1.5rem;
    }

    .support-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(340px, 1fr));
        gap: 1.25rem;
    }

    .card-section {
        padding: 1.5rem;
    }

    .section-title {
        font-size: 1rem;
        font-weight: 700;
        color: var(--text-primary);
        margin-bottom: 1rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .detail-row {
        padding: 0.75rem 0;
        border-bottom: 1px solid var(--card-border);
    }

    .detail-row:last-child {
        border-bottom: none;
    }

    .detail-label {
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        color: var(--text-muted);
        margin-bottom: 0.25rem;
    }

    .detail-value {
        font-size: 0.925rem;
        font-weight: 600;
        color: var(--text-primary);
    }

    .option-badge {
        display: inline-block;
        padding: 0.3rem 0.65rem;
        border-radius: 6px;
        background: rgba(99, 102, 241, 0.15);
        border: 1px solid rgba(99, 102, 241, 0.3);
        color: #818cf8;
        font-size: 0.8rem;
        font-weight: 600;
        margin: 0.2rem 0.25rem 0.2rem 0;
    }

    .form-group {
        margin-bottom: 1rem;
    }

    .form-label {
        display: block;
        font-size: 0.8rem;
        font-weight: 600;
        color: var(--text-primary);
        margin-bottom: 0.35rem;
    }

    .form-control {
        width: 100%;
        padding: 0.6rem 0.85rem;
        background: var(--input-bg);
        border: 1px solid var(--card-border);
        border-radius: 8px;
        color: var(--text-primary);
        font-size: 0.85rem;
        outline: none;
    }

    .form-control:focus {
        border-color: var(--primary);
        box-shadow: 0 0 0 3px var(--primary-glow);
    }

    .form-hint {
        font-size: 0.75rem;
        color: var(--text-muted);
        margin-top: 0.3rem;
    }

    .btn-save {
        background: var(--primary);
        color: #ffffff;
        border: none;
        border-radius: 8px;
        padding: 0.6rem 1.25rem;
        font-size: 0.85rem;
        font-weight: 600;
        cursor: pointer;
        transition: background 0.2s ease;
    }

    .btn-save:hover {
        background: var(--primary-hover);
    }
</style>
@endsection

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Support Contact</h1>
        <p class="page-subtitle">View and update official support contact details returned by <code>GET /api/support-contact</code>.</p>
    </div>
</div>

<div class="support-grid">
    <!-- View Current Support Contact -->
    <div class="content-card card-section">
        <div class="section-title">
            <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
            </svg>
            Current Support Details
        </div>

        <div class="detail-row">
            <div class="detail-label">Support Email</div>
            <div class="detail-value">{{ $contact->support_email }}</div>
        </div>

        <div class="detail-row">
            <div class="detail-label">Support Phone</div>
            <div class="detail-value">{{ $contact->support_phone }}</div>
        </div>

        <div class="detail-row">
            <div class="detail-label">Working Hours</div>
            <div class="detail-value">{{ $contact->working_hours }}</div>
        </div>

        <div class="detail-row">
            <div class="detail-label">Support Options</div>
            <div style="margin-top: 0.35rem;">
                @forelse(($contact->support_options ?? []) as $option)
                    <span class="option-badge">{{ $option }}</span>
                @empty
                    <span style="color: var(--text-muted); font-size: 0.85rem;">No support options configured.</span>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Edit Support Contact -->
    <div class="content-card card-section">
        <div class="section-title">
            <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
            </svg>
            Edit Support Contact
        </div>

        <form action="{{ route('admin.support-contact.update') }}" method="POST">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label class="form-label" for="support_email">Support Email</label>
                <input type="email" id="support_email" name="support_email" class="form-control" value="{{ old('support_email', $contact->support_email) }}" required>
            </div>

            <div class="form-group">
                <label class="form-label" for="support_phone">Support Phone</label>
                <input type="text" id="support_phone" name="support_phone" class="form-control" value="{{ old('support_phone', $contact->support_phone) }}" required>
            </div>

            <div class="form-group">
                <label class="form-label" for="working_hours">Working Hours</label>
                <input type="text" id="working_hours" name="working_hours" class="form-control" value="{{ old('working_hours', $contact->working_hours) }}" required>
            </div>

            <div class="form-group">
                <label class="form-label" for="support_options">Support Options</label>
                <textarea id="support_options" name="support_options" rows="4" class="form-control">{{ old('support_options', implode("\n", $contact->support_options ?? [])) }}</textarea>
                <div class="form-hint">Enter one support option per line.</div>
            </div>

            <div style="display: flex; justify-content: flex-end; margin-top: 1.25rem;">
                <button type="submit" class="btn-save">Save Changes</button>
            </div>
        </form>
    </div>
</div>
@endsection
