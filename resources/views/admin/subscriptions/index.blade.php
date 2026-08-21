@extends('layouts.admin')

@section('title', 'User Subscriptions')

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

    .toolbar-inline {
        display: flex;
        align-items: center;
        gap: 0.65rem;
        flex-wrap: nowrap;
        flex-shrink: 0;
    }

    .search-box {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        background: var(--card-bg);
        border: 1px solid var(--card-border);
        border-radius: 10px;
        padding: 0.35rem 0.4rem 0.35rem 0.75rem;
        width: 300px;
    }

    .search-box input {
        background: transparent;
        border: none;
        outline: none;
        color: #ffffff;
        font-size: 0.85rem;
        width: 100%;
    }

    .search-box input::placeholder {
        color: var(--text-muted);
    }

    .search-box svg {
        color: var(--text-muted);
        width: 18px;
        height: 18px;
        flex-shrink: 0;
    }

    .btn-search {
        background: var(--primary);
        color: #ffffff;
        border: none;
        border-radius: 6px;
        padding: 0.35rem 0.75rem;
        font-size: 0.8rem;
        font-weight: 600;
        cursor: pointer;
        flex-shrink: 0;
        transition: background 0.2s ease;
    }

    .btn-search:hover {
        background: var(--primary-hover);
    }

    .clear-link {
        color: var(--text-muted);
        font-size: 0.775rem;
        text-decoration: none;
        white-space: nowrap;
    }

    .clear-link:hover {
        color: #ffffff;
    }

    .sort-link {
        color: var(--text-muted);
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 0.3rem;
        transition: color 0.2s ease;
    }

    .sort-link:hover, .sort-link.active {
        color: #ffffff;
    }

    .status-select {
        background: var(--card-bg);
        border: 1px solid var(--card-border);
        color: #ffffff;
        border-radius: 8px;
        padding: 0.4rem 0.65rem;
        font-size: 0.8rem;
        outline: none;
        cursor: pointer;
    }

    .plan-badge {
        display: inline-block;
        padding: 0.25rem 0.6rem;
        border-radius: 6px;
        font-size: 0.75rem;
        font-weight: 700;
        background: rgba(168, 85, 247, 0.15);
        color: #c084fc;
        border: 1px solid rgba(168, 85, 247, 0.3);
    }

    /* Action Dropdown with More Icon (⋮) */
    .action-dropdown {
        position: relative;
        display: inline-block;
    }

    .btn-more {
        background: rgba(255, 255, 255, 0.05);
        color: #cbd5e1;
        border: 1px solid var(--card-border);
        padding: 0.4rem 0.55rem;
        border-radius: 8px;
        cursor: pointer;
        transition: all 0.2s ease;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }

    .btn-more:hover {
        background: rgba(255, 255, 255, 0.12);
        color: #ffffff;
        border-color: var(--primary);
    }

    .dropdown-menu {
        position: absolute;
        right: 0;
        top: 110%;
        background: #161e2e;
        border: 1px solid var(--card-border);
        border-radius: 10px;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.6);
        min-width: 160px;
        z-index: 100;
        display: none;
        padding: 0.35rem 0;
    }

    .dropdown-menu.show {
        display: block;
    }

    .dropdown-item {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.5rem 0.85rem;
        font-size: 0.8rem;
        font-weight: 600;
        color: #cbd5e1;
        text-decoration: none;
        transition: background 0.15s ease, color 0.15s ease;
        width: 100%;
        border: none;
        background: transparent;
        cursor: pointer;
        text-align: left;
    }

    .dropdown-item:hover {
        background: rgba(255, 255, 255, 0.08);
    }

    .dropdown-item.item-activate:hover { color: #34d399; }
    .dropdown-item.item-suspend:hover { color: #fca5a5; }

    .per-page-select {
        background: rgba(11, 15, 25, 0.7);
        border: 1px solid var(--card-border);
        color: #ffffff;
        border-radius: 6px;
        padding: 0.25rem 0.5rem;
        font-size: 0.775rem;
        outline: none;
        cursor: pointer;
    }

    .pagination-wrapper {
        padding: 1rem 1.25rem;
        border-top: 1px solid var(--card-border);
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 1rem;
    }

    .pagination-wrapper .pagination {
        display: flex;
        gap: 0.35rem;
        list-style: none;
    }

    .pagination-wrapper nav {
        display: flex;
        align-items: center;
    }
    .pagination-wrapper nav p {
        display: none !important;
    }
    .pagination-wrapper svg {
        width: 14px !important;
        height: 14px !important;
        max-width: 14px !important;
        max-height: 14px !important;
        vertical-align: middle;
    }
    .pagination-wrapper ul.pagination,
    .pagination-wrapper div.flex {
        display: flex !important;
        align-items: center !important;
        gap: 0.35rem !important;
        list-style: none !important;
        margin: 0 !important;
        padding: 0 !important;
    }
    .pagination-wrapper .page-item .page-link,
    .pagination-wrapper span[aria-current="page"],
    .pagination-wrapper a.relative,
    .pagination-wrapper span.relative {
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        min-width: 32px !important;
        height: 32px !important;
        padding: 0 0.6rem !important;
        border-radius: 6px !important;
        background: rgba(255, 255, 255, 0.05) !important;
        border: 1px solid var(--card-border) !important;
        color: #cbd5e1 !important;
        text-decoration: none !important;
        font-size: 0.8rem !important;
        font-weight: 600 !important;
        transition: all 0.15s ease !important;
    }
    .pagination-wrapper .page-item.active .page-link,
    .pagination-wrapper span[aria-current="page"] {
        background: var(--primary) !important;
        border-color: var(--primary) !important;
        color: #ffffff !important;
    }
    .pagination-wrapper a.page-link:hover,
    .pagination-wrapper a.relative:hover {
        background: rgba(255, 255, 255, 0.12) !important;
        color: #ffffff !important;
        border-color: rgba(255, 255, 255, 0.2) !important;
    }
    .pagination-wrapper .page-item.disabled .page-link,
    .pagination-wrapper span[aria-disabled="true"] {
        opacity: 0.4 !important;
        cursor: not-allowed !important;
        background: transparent !important;
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
        max-width: 520px;
        padding: 1.5rem;
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.6);
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
<!-- Single Inline Header Row -->
<div class="page-header">
    <div>
        <h1 class="page-title">User Subscriptions</h1>
        <p class="page-subtitle">View activated user subscriptions, transactions, validity status, and toggle status (Active / Suspend).</p>
    </div>

    <form action="{{ route('admin.subscriptions.index') }}" method="GET" class="toolbar-inline">
        @if(request('sort_by'))
            <input type="hidden" name="sort_by" value="{{ request('sort_by') }}">
        @endif
        @if(request('sort_order'))
            <input type="hidden" name="sort_order" value="{{ request('sort_order') }}">
        @endif
        @if(request('per_page'))
            <input type="hidden" name="per_page" value="{{ request('per_page') }}">
        @endif

        <select name="status" class="status-select" onchange="this.form.submit()">
            <option value="">All Statuses</option>
            <option value="active" {{ $selectedStatus === 'active' ? 'selected' : '' }}>Active</option>
            <option value="suspended" {{ $selectedStatus === 'suspended' ? 'selected' : '' }}>Suspended</option>
        </select>

        <div class="search-box">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
            <input type="text" name="search" value="{{ $search }}" placeholder="Search user, transaction ref...">
            <button type="submit" class="btn-search">Search</button>
            @if($search)
                <a href="{{ route('admin.subscriptions.index') }}" style="color: var(--text-muted); font-size: 0.75rem; text-decoration: none;">Clear</a>
            @endif
        </div>
    </form>

    <form action="{{ route('admin.subscriptions.checkExpired') }}" method="POST">
        @csrf
        <button type="submit" class="btn-action" style="background: rgba(239, 68, 68, 0.15); color: #ef4444; border: 1px solid rgba(239, 68, 68, 0.3); padding: 0.55rem 0.9rem; border-radius: 8px; font-size: 0.8rem; font-weight: 600; cursor: pointer; display: inline-flex; align-items: center; gap: 0.4rem;" title="Run Expired Subscriptions Check">
            <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            Check Expired
        </button>
    </form>
</div>

<div class="content-card">
    <div class="table-responsive">
        <table class="compact-table">
            <thead>
                <tr>
                    @php
                        function subSortUrl($field, $currentSortBy, $currentSortOrder) {
                            $order = ($currentSortBy === $field && $currentSortOrder === 'asc') ? 'desc' : 'asc';
                            return route('admin.subscriptions.index', array_merge(request()->query(), ['sort_by' => $field, 'sort_order' => $order]));
                        }
                    @endphp
                    <th>
                        <a href="{{ subSortUrl('id', $sortBy, $sortOrder) }}" class="sort-link {{ $sortBy === 'id' ? 'active' : '' }}">
                            ID
                            @if($sortBy === 'id')
                                <span>{{ $sortOrder === 'asc' ? '↑' : '↓' }}</span>
                            @endif
                        </a>
                    </th>
                    <th>Subscriber User</th>
                    <th>Plan & Access</th>
                    <th>Transaction Ref</th>
                    <th>Amount</th>
                    <th>Expires At</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($subscriptions as $sub)
                    @php
                        $userName = $sub->user ? $sub->user->name : ($sub->customer_name ?? 'User #' . $sub->user_id);
                        $userEmail = $sub->user ? $sub->user->email : ($sub->customer_email ?? 'N/A');
                        $planTitle = $sub->plan ? $sub->plan->title : ('Plan ' . $sub->plan_id);

                        $now = \Carbon\Carbon::now();
                        $expiresAt = $sub->expires_at ? \Carbon\Carbon::parse($sub->expires_at) : null;
                        $remainingDays = $expiresAt ? max(0, (int) $now->diffInDays($expiresAt, false)) : 0;
                        $isExpired = $expiresAt && $expiresAt->isPast();
                    @endphp
                    <tr>
                        <td><strong style="color: #ffffff;">#{{ $sub->id }}</strong></td>
                        <td>
                            <div style="display: flex; align-items: center; gap: 0.65rem;">
                                <div class="avatar" style="width: 28px; height: 28px; font-size: 0.75rem;">
                                    {{ strtoupper(substr($userName, 0, 1)) }}
                                </div>
                                <div>
                                    <div style="font-weight: 700; color: #ffffff;">{{ $userName }}</div>
                                    <div style="font-size: 0.75rem; color: var(--text-muted);">{{ $userEmail }}</div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="plan-badge">{{ $planTitle }}</span>
                            <div style="font-size: 0.75rem; color: var(--text-muted); margin-top: 0.2rem;">Plan ID: {{ $sub->plan_id }}</div>
                        </td>
                        <td>
                            <div style="font-weight: 600; color: #a5b4fc; font-size: 0.8rem;">{{ $sub->transaction_reference ?? $sub->cart_id ?? 'N/A' }}</div>
                            <div style="font-size: 0.75rem; color: var(--text-muted);">{{ $sub->payment_gateway ?? 'PayTabs' }} ({{ $sub->card_brand ?? 'Card' }})</div>
                        </td>
                        <td>
                            <strong style="color: #34d399;">{{ $sub->amount }} {{ $sub->currency }}</strong>
                        </td>
                        <td>
                            @if($expiresAt)
                                <div>{{ $expiresAt->format('M d, Y') }}</div>
                                <div style="font-size: 0.75rem; color: {{ $isExpired ? '#fca5a5' : '#818cf8' }};">
                                    {{ $isExpired ? 'Expired' : ($remainingDays . ' days left') }}
                                </div>
                            @else
                                <span style="color: var(--text-muted);">N/A</span>
                            @endif
                        </td>
                        <td>
                            <span class="badge {{ $sub->status === 'active' ? 'badge-active' : 'badge-suspended' }}">
                                {{ ucfirst($sub->status) }}
                            </span>
                        </td>
                        <td>
                            <!-- Action Dropdown -->
                            <div class="action-dropdown">
                                <button type="button" class="btn-more" onclick="toggleDropdown(event, 'drop-sub-{{ $sub->id }}')">
                                    <svg width="16" height="16" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M12 8c1.1 0 2-.9 2-2s-.9-2-2-2-2 .9-2 2 .9 2 2 2zm0 2c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2zm0 6c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2z"/>
                                    </svg>
                                </button>
                                <div id="drop-sub-{{ $sub->id }}" class="dropdown-menu">
                                    <a href="javascript:void(0)" onclick="openEditSubModal({{ json_encode($sub) }})" class="dropdown-item item-activate">
                                        <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                        Edit Subscription
                                    </a>

                                    @if($sub->status === 'suspended')
                                        <form id="activate-sub-{{ $sub->id }}" action="{{ route('admin.subscriptions.updateStatus', $sub->id) }}" method="POST" style="margin:0;">
                                            @csrf
                                            @method('PUT')
                                            <input type="hidden" name="status" value="active">
                                            <button type="button" class="dropdown-item item-activate" onclick="confirmAction({ title: 'Activate Subscription?', text: 'Are you sure you want to activate this subscription?', icon: 'info', confirmText: 'Yes, Activate', confirmClass: 'swal2-confirm', formId: 'activate-sub-{{ $sub->id }}' })">
                                                <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                Activate Subscription
                                            </button>
                                        </form>
                                    @else
                                        <form id="suspend-sub-{{ $sub->id }}" action="{{ route('admin.subscriptions.updateStatus', $sub->id) }}" method="POST" style="margin:0;">
                                            @csrf
                                            @method('PUT')
                                            <input type="hidden" name="status" value="suspended">
                                            <button type="button" class="dropdown-item item-suspend" onclick="confirmAction({ title: 'Suspend Subscription?', text: 'Are you sure you want to suspend this user subscription?', icon: 'warning', confirmText: 'Yes, Suspend', confirmClass: 'swal2-confirm btn-danger', formId: 'suspend-sub-{{ $sub->id }}' })">
                                                <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                                                Suspend Subscription
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" style="text-align: center; color: var(--text-muted); padding: 1.5rem;">
                            @if($search)
                                No user subscriptions found matching "{{ $search }}".
                            @else
                                No user subscriptions found.
                            @endif
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="pagination-wrapper">
        <div style="display: flex; align-items: center; gap: 1rem;">
            <span style="color: var(--text-muted); font-size: 0.8rem;">
                Showing {{ $subscriptions->firstItem() ?? 0 }} to {{ $subscriptions->lastItem() ?? 0 }} of {{ $subscriptions->total() }} subscriptions
            </span>

            <div style="display: flex; align-items: center; gap: 0.35rem;">
                <span style="color: var(--text-muted); font-size: 0.775rem;">Per page:</span>
                <select onchange="changePerPage(this.value)" class="per-page-select">
                    @foreach([10, 20, 30, 50, 100] as $size)
                        <option value="{{ $size }}" {{ $perPage == $size ? 'selected' : '' }}>{{ $size }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div>
            {{ $subscriptions->links('pagination::bootstrap-4') }}
        </div>
    </div>
</div>

<!-- Edit Subscription Modal -->
<div class="modal-backdrop" id="editSubModal">
    <div class="modal-card" style="max-width: 540px;">
        <div class="modal-header">
            <h2>Edit Subscription #<span id="subModalId"></span></h2>
            <button class="btn-close" onclick="closeEditSubModal()">&times;</button>
        </div>
        <form id="editSubForm" method="POST">
            @csrf
            @method('PUT')
            <div style="display: flex; flex-direction: column; gap: 1rem;">
                <div class="form-group" style="margin-bottom: 0;">
                    <label class="form-label">Subscription Plan <span style="color:#ef4444;">*</span></label>
                    <select name="plan_id" id="editSubPlanSelect" class="form-control" required onchange="onSubPlanSelectChange(this)">
                        <option value="">-- Select Plan --</option>
                        @foreach($plans as $plan)
                            <option value="{{ $plan->plan_id }}" data-days="{{ $plan->duration_days }}" data-price="{{ $plan->price_sar ?? $plan->price }}">
                                {{ $plan->title }} ({{ $plan->duration_days }} Days - SAR {{ $plan->price_sar ?? $plan->price }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group" style="margin-bottom: 0;">
                    <label class="form-label">Amount (SAR) <span style="color:#ef4444;">*</span></label>
                    <input type="number" step="0.01" name="amount" id="editSubAmountInput" class="form-control" placeholder="e.g. 397.46" required>
                </div>
                <div class="form-group" style="margin-bottom: 0;">
                    <label class="form-label">Subscription Duration (Days from Today)</label>
                    <input type="number" name="duration_days" id="editSubDurationInput" class="form-control" placeholder="e.g. 30" min="1">
                    <small style="color: var(--text-muted); font-size: 0.75rem; margin-top: 0.25rem; display: block;">Leave blank to use default plan duration.</small>
                </div>
                <div class="form-group" style="margin-bottom: 0;">
                    <label class="form-label">Status <span style="color:#ef4444;">*</span></label>
                    <select name="status" id="editSubStatusSelect" class="form-control" required>
                        <option value="active">Active</option>
                        <option value="suspended">Suspended</option>
                    </select>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn-secondary" onclick="closeEditSubModal()">Cancel</button>
                <button type="submit" class="btn-action" style="padding: 0.6rem 1.2rem; background: var(--primary); color: #ffffff; border: none; border-radius: 8px; font-weight: 600; cursor: pointer;">Update Subscription</button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function openEditSubModal(sub) {
        document.getElementById('subModalId').innerText = sub.id;
        document.getElementById('editSubForm').action = "{{ url('admin/subscriptions') }}/" + sub.id;
        document.getElementById('editSubPlanSelect').value = sub.plan_id;
        document.getElementById('editSubAmountInput').value = sub.amount;
        document.getElementById('editSubStatusSelect').value = sub.status;
        document.getElementById('editSubModal').classList.add('show');
    }

    function closeEditSubModal() {
        document.getElementById('editSubModal').classList.remove('show');
    }

    function onSubPlanSelectChange(selectElem) {
        var selectedOption = selectElem.options[selectElem.selectedIndex];
        var defaultDays = selectedOption.getAttribute('data-days');
        var defaultPrice = selectedOption.getAttribute('data-price');
        if (defaultDays) {
            document.getElementById('editSubDurationInput').value = defaultDays;
        }
        if (defaultPrice) {
            document.getElementById('editSubAmountInput').value = defaultPrice;
        }
    }
    function toggleDropdown(event, id) {
        event.stopPropagation();
        var menu = document.getElementById(id);
        var allMenus = document.querySelectorAll('.dropdown-menu');
        allMenus.forEach(function(m) {
            if (m.id !== id) m.classList.remove('show');
        });
        menu.classList.toggle('show');
    }

    function changePerPage(value) {
        var url = new URL(window.location.href);
        url.searchParams.set('per_page', value);
        url.searchParams.set('page', '1');
        window.location.href = url.href;
    }

    document.addEventListener('click', function() {
        var allMenus = document.querySelectorAll('.dropdown-menu');
        allMenus.forEach(function(m) {
            m.classList.remove('show');
        });
    });
</script>
@endsection
