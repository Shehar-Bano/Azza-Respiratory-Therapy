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

    .pagination-wrapper .page-item .page-link {
        padding: 0.35rem 0.7rem;
        border-radius: 6px;
        background: rgba(255, 255, 255, 0.05);
        border: 1px solid var(--card-border);
        color: #ffffff;
        text-decoration: none;
        font-size: 0.8rem;
    }

    .pagination-wrapper .page-item.active .page-link {
        background: var(--primary);
        border-color: var(--primary);
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
        </div>

        @if($search || $selectedStatus)
            <a href="{{ route('admin.subscriptions.index') }}" class="clear-link">Clear</a>
        @endif
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
            {{ $subscriptions->links() }}
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
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
