@extends('layouts.admin')

@section('title', 'Subscription Plans Management')

@section('styles')
<style>
    .page-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 1.5rem;
        gap: 1.5rem;
        flex-wrap: nowrap;
    }

    .plans-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
        gap: 1.5rem;
        margin-bottom: 2rem;
    }

    .plan-card {
        background: #161e2e;
        border: 1px solid var(--card-border);
        border-radius: 14px;
        padding: 1.5rem;
        position: relative;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.4);
        transition: all 0.25s ease;
    }

    .plan-card:hover {
        border-color: var(--primary);
        transform: translateY(-3px);
    }

    .plan-card.featured {
        border: 1px solid rgba(168, 85, 247, 0.6);
        background: linear-gradient(135deg, rgba(22, 30, 46, 0.95), rgba(88, 28, 135, 0.2));
    }

    .plan-badge-id {
        display: inline-block;
        padding: 0.25rem 0.65rem;
        border-radius: 6px;
        font-size: 0.75rem;
        font-weight: 700;
        background: rgba(99, 102, 241, 0.15);
        color: #a5b4fc;
        border: 1px solid rgba(99, 102, 241, 0.3);
    }

    .plan-title {
        font-size: 1.25rem;
        font-weight: 800;
        color: #ffffff;
        margin-top: 0.5rem;
        margin-bottom: 0.35rem;
    }

    .price-box {
        display: flex;
        align-items: baseline;
        gap: 0.65rem;
        margin: 1rem 0;
        flex-wrap: wrap;
    }

    .price-amount-usd {
        font-size: 1.75rem;
        font-weight: 800;
        color: #34d399;
    }

    .price-amount-sar {
        font-size: 0.95rem;
        font-weight: 700;
        color: #c084fc;
        background: rgba(168, 85, 247, 0.15);
        padding: 0.3rem 0.75rem;
        border-radius: 6px;
        border: 1px solid rgba(168, 85, 247, 0.3);
    }

    .plan-access {
        color: var(--text-secondary);
        font-size: 0.85rem;
        line-height: 1.45;
        margin-bottom: 1rem;
        background: rgba(11, 15, 25, 0.6);
        padding: 0.65rem 0.85rem;
        border-radius: 8px;
        border: 1px solid var(--card-border);
    }

    .feature-list {
        list-style: none;
        padding: 0;
        margin: 0 0 1.25rem 0;
    }

    .feature-item {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        font-size: 0.85rem;
        color: #cbd5e1;
        margin-bottom: 0.5rem;
    }

    .feature-item svg {
        color: #34d399;
        width: 16px;
        height: 16px;
        flex-shrink: 0;
    }

    .btn-action {
        padding: 0.5rem 0.9rem;
        border-radius: 8px;
        font-size: 0.825rem;
        font-weight: 600;
        border: none;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.35rem;
        transition: all 0.2s ease;
        text-decoration: none;
        white-space: nowrap;
        width: 100%;
    }

    .btn-edit {
        background: rgba(59, 130, 246, 0.15);
        color: #60a5fa;
        border: 1px solid rgba(59, 130, 246, 0.3);
    }

    .btn-edit:hover {
        background: rgba(59, 130, 246, 0.3);
        color: #ffffff;
    }

    /* Modal Backdrop & Card */
    .modal-backdrop {
        position: fixed;
        top: 0; left: 0; right: 0; bottom: 0;
        background: rgba(0, 0, 0, 0.75);
        backdrop-filter: blur(4px);
        display: none;
        align-items: center;
        justify-content: center;
        z-index: 1000;
        padding: 1.25rem;
    }

    .modal-backdrop.show {
        display: flex;
    }

    .modal-card {
        background: #161e2e;
        border: 1px solid var(--card-border);
        border-radius: 14px;
        width: 100%;
        max-width: 580px;
        padding: 1.5rem;
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.6);
        max-height: 90vh;
        overflow-y: auto;
    }

    .modal-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 1.25rem;
    }

    .modal-header h2 {
        font-size: 1.15rem;
        font-weight: 800;
        color: #ffffff;
    }

    .btn-close {
        background: transparent;
        border: none;
        color: var(--text-muted);
        font-size: 1.35rem;
        cursor: pointer;
        line-height: 1;
    }

    .btn-close:hover {
        color: #ffffff;
    }

    .form-group {
        margin-bottom: 1rem;
    }

    .form-label {
        display: block;
        font-size: 0.8rem;
        font-weight: 600;
        color: #cbd5e1;
        margin-bottom: 0.35rem;
    }

    .form-control {
        width: 100%;
        padding: 0.6rem 0.85rem;
        background: rgba(11, 15, 25, 0.7);
        border: 1px solid rgba(255, 255, 255, 0.1);
        border-radius: 8px;
        color: #ffffff;
        font-size: 0.85rem;
        outline: none;
    }

    .form-control:focus {
        border-color: var(--primary);
        box-shadow: 0 0 0 3px var(--primary-glow);
    }

    .feature-checkbox-grid {
        display: grid;
        grid-template-columns: 1fr;
        gap: 0.5rem;
        background: rgba(11, 15, 25, 0.5);
        padding: 0.75rem;
        border-radius: 8px;
        border: 1px solid rgba(255, 255, 255, 0.08);
        max-height: 220px;
        overflow-y: auto;
    }

    .feature-checkbox-label {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        font-size: 0.8rem;
        color: #cbd5e1;
        cursor: pointer;
    }

    .feature-checkbox-label input[type="checkbox"] {
        accent-color: var(--primary);
        width: 16px;
        height: 16px;
        cursor: pointer;
    }

    .modal-footer {
        display: flex;
        justify-content: flex-end;
        gap: 0.65rem;
        margin-top: 1.5rem;
    }

    .btn-secondary {
        background: rgba(255, 255, 255, 0.06);
        border: 1px solid var(--card-border);
        color: #cbd5e1;
        padding: 0.55rem 1rem;
        border-radius: 8px;
        font-size: 0.825rem;
        font-weight: 600;
        cursor: pointer;
    }

    .btn-secondary:hover {
        background: rgba(255, 255, 255, 0.12);
        color: #ffffff;
    }
</style>
@endsection

@section('content')
<!-- Page Header Row -->
<div class="page-header">
    <div>
        <h1 class="page-title">Subscription Plans Management</h1>
        <p class="page-subtitle">Configure tier pricing (USD & SAR), duration, access levels, and feature permissions.</p>
    </div>
</div>

<!-- Plans Grid -->
<div class="plans-grid">
    @forelse($plans as $plan)
        <div class="plan-card {{ $plan->plan_id === '2' ? 'featured' : '' }}">
            <div>
                <div style="display: flex; align-items: center; justify-content: space-between;">
                    <span class="plan-badge-id">Plan ID: {{ $plan->plan_id }}</span>
                    @if($plan->duration_days > 0)
                        <span style="color: #818cf8; font-size: 0.8rem; font-weight: 700;">{{ $plan->duration_days }} Days Validity</span>
                    @else
                        <span style="color: var(--text-muted); font-size: 0.8rem; font-weight: 700;">Lifetime Free</span>
                    @endif
                </div>

                <h3 class="plan-title">{{ $plan->title }}</h3>

                <div class="price-box">
                    <span class="price-amount-usd">${{ $plan->price_usd ?? $plan->price }}/MO</span>
                    <span class="price-amount-sar">{{ $plan->price_sar }} SAR</span>
                </div>

                <div class="plan-access">
                    <strong style="color: #ffffff;">Access Scope:</strong> {{ $plan->access }}
                </div>

                <ul class="feature-list">
                    @foreach($plan->feature_objects as $featObj)
                        <li class="feature-item">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                            <span>{{ $featObj->title }}</span>
                        </li>
                    @endforeach
                </ul>
            </div>

            <div>
                <button type="button" class="btn-action btn-edit" onclick="openEditModal({{ json_encode($plan) }})">
                    <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    Edit Plan Details
                </button>
            </div>
        </div>
    @empty
        <div style="grid-column: 1 / -1; color: var(--text-muted); padding: 2rem; text-align: center; background: #161e2e; border-radius: 12px;">
            No subscription plans found in database.
        </div>
    @endforelse
</div>

<!-- Edit Plan Modal -->
<div class="modal-backdrop" id="editModal">
    <div class="modal-card">
        <div class="modal-header">
            <h2>Edit Subscription Plan</h2>
            <button class="btn-close" onclick="closeModal('editModal')">&times;</button>
        </div>
        <form id="editForm" method="POST">
            @csrf
            @method('PUT')
            <div class="form-group">
                <label class="form-label">Plan Title</label>
                <input type="text" name="title" id="editTitle" class="form-control" required>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                <div class="form-group">
                    <label class="form-label">Price (USD $)</label>
                    <input type="text" name="price_usd" id="editPriceUsd" class="form-control" placeholder="19.99" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Price (SAR)</label>
                    <input type="text" name="price_sar" id="editPriceSar" class="form-control" placeholder="74.96" required>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Duration (Days)</label>
                <input type="number" name="duration_days" id="editDurationDays" class="form-control" min="0" required>
                <small style="color: var(--text-muted); font-size: 0.75rem;">Set to 0 for lifetime/free plans.</small>
            </div>

            <div class="form-group">
                <label class="form-label">Access Scope Description</label>
                <input type="text" name="access" id="editAccess" class="form-control" placeholder="e.g. to Calculator, Articles and Cards Only">
            </div>

            <div class="form-group">
                <label class="form-label">Dynamic Plan Feature Permissions</label>
                <div class="feature-checkbox-grid">
                    @foreach($allFeatures as $feat)
                        <label class="feature-checkbox-label">
                            <input type="checkbox" name="feature_ids[]" value="{{ $feat->id }}" class="feature-checkbox" id="feat-check-{{ $feat->id }}">
                            <span><strong>{{ $feat->title }}</strong> <small style="color: #a5b4fc;">({{ $feat->slug }})</small></span>
                        </label>
                    @endforeach
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn-secondary" onclick="closeModal('editModal')">Cancel</button>
                <button type="submit" class="btn-action btn-edit" style="width: auto; padding: 0.6rem 1.2rem;">Save Changes</button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function openEditModal(plan) {
        document.getElementById('editForm').action = "{{ url('admin/plans') }}/" + plan.id;
        document.getElementById('editTitle').value = plan.title;
        document.getElementById('editPriceUsd').value = plan.price_usd ? plan.price_usd : plan.price;
        document.getElementById('editPriceSar').value = plan.price_sar ? plan.price_sar : '';
        document.getElementById('editDurationDays').value = plan.duration_days;
        document.getElementById('editAccess').value = plan.access ? plan.access : '';

        // Reset checkboxes
        var checkboxes = document.querySelectorAll('.feature-checkbox');
        checkboxes.forEach(function(cb) { cb.checked = false; });

        // Check assigned feature_ids
        if (Array.isArray(plan.feature_ids)) {
            plan.feature_ids.forEach(function(id) {
                var cb = document.getElementById('feat-check-' + id);
                if (cb) cb.checked = true;
            });
        }

        document.getElementById('editModal').classList.add('show');
    }

    function closeModal(modalId) {
        document.getElementById(modalId).classList.remove('show');
    }
</script>
@endsection
