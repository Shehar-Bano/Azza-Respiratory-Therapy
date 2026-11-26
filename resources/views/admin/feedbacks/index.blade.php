@extends('layouts.admin')

@section('title', 'Customer Feedback')

@section('styles')
<style>
    .page-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 1.5rem;
        gap: 1.5rem;
        flex-wrap: wrap;
    }

    .toolbar-inline {
        display: flex;
        align-items: center;
        gap: 0.65rem;
        flex-wrap: wrap;
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
        width: 280px;
    }

    .search-box input {
        background: transparent;
        border: none;
        outline: none;
        color: var(--text-primary);
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

    .filter-select {
        background: var(--card-bg);
        border: 1px solid var(--card-border);
        color: var(--text-primary);
        border-radius: 10px;
        padding: 0.48rem 0.75rem;
        font-size: 0.825rem;
        outline: none;
        cursor: pointer;
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
        color: var(--text-primary);
    }

    .badge {
        display: inline-block;
        padding: 0.25rem 0.6rem;
        border-radius: 6px;
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: capitalize;
        white-space: nowrap;
    }

    .badge-pending { background: rgba(245, 158, 11, 0.15); color: #f59e0b; border: 1px solid rgba(245, 158, 11, 0.3); }
    .badge-reviewed { background: rgba(56, 189, 248, 0.15); color: #38bdf8; border: 1px solid rgba(56, 189, 248, 0.3); }
    .badge-resolved { background: rgba(16, 185, 129, 0.15); color: #10b981; border: 1px solid rgba(16, 185, 129, 0.3); }

    .feedback-msg {
        color: var(--text-primary);
        font-size: 0.85rem;
        line-height: 1.45;
        max-width: 480px;
    }

    .action-dropdown { position: relative; display: inline-block; }

    .btn-more {
        background: var(--input-bg);
        color: var(--text-secondary);
        border: 1px solid var(--card-border);
        padding: 0.4rem 0.55rem;
        border-radius: 8px;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }

    .btn-more:hover {
        background: var(--card-border);
        color: var(--text-primary);
        border-color: var(--primary);
    }

    .dropdown-menu {
        position: absolute;
        right: 0;
        top: 110%;
        background: var(--card-bg);
        border: 1px solid var(--card-border);
        border-radius: 10px;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
        min-width: 170px;
        z-index: 100;
        display: none;
        padding: 0.35rem 0;
    }

    .dropdown-menu.show { display: block; }

    .dropdown-item {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.5rem 0.85rem;
        font-size: 0.8rem;
        font-weight: 600;
        color: var(--text-primary);
        text-decoration: none;
        transition: background 0.15s ease, color 0.15s ease;
    }

    .dropdown-item:hover { background: var(--input-bg); }
    .dropdown-item.item-view:hover { color: #60a5fa; }
    .dropdown-item.item-delete:hover { color: #fca5a5; }

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

    .modal-card {
        background: var(--card-bg);
        border: 1px solid var(--card-border);
        border-radius: 14px;
        width: 100%;
        max-width: 540px;
        padding: 1.5rem;
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.25);
    }

    .modal-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 1.25rem;
    }

    .modal-title {
        font-size: 1.15rem;
        font-weight: 800;
        color: var(--text-primary);
    }

    .btn-close {
        background: transparent;
        border: none;
        color: var(--text-muted);
        font-size: 1.35rem;
        cursor: pointer;
        line-height: 1;
    }

    .btn-secondary {
        background: var(--input-bg);
        border: 1px solid var(--card-border);
        color: var(--text-primary);
        padding: 0.55rem 1rem;
        border-radius: 8px;
        font-size: 0.825rem;
        font-weight: 600;
        cursor: pointer;
    }

    .per-page-select {
        background: var(--input-bg);
        border: 1px solid var(--card-border);
        color: var(--text-primary);
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
</style>
@endsection

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Customer Feedback</h1>
        <p class="page-subtitle">View and manage clinical inquiries and feedback submitted via <code>POST /api/feedback/submit</code>.</p>
    </div>

    <form action="{{ route('admin.feedbacks.index') }}" method="GET" class="toolbar-inline">
        @if(request('per_page'))
            <input type="hidden" name="per_page" value="{{ request('per_page') }}">
        @endif

        <select name="status" class="filter-select" onchange="this.form.submit()">
            <option value="">All Statuses</option>
            <option value="pending" {{ $selectedStatus === 'pending' ? 'selected' : '' }}>Pending</option>
            <option value="reviewed" {{ $selectedStatus === 'reviewed' ? 'selected' : '' }}>Reviewed</option>
            <option value="resolved" {{ $selectedStatus === 'resolved' ? 'selected' : '' }}>Resolved</option>
        </select>

        <div class="search-box">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
            <input type="text" name="search" value="{{ $search }}" placeholder="Search user or feedback...">
            <button type="submit" class="btn-search">Search</button>
        </div>

        @if($search || $selectedStatus)
            <a href="{{ route('admin.feedbacks.index') }}" class="clear-link">Clear</a>
        @endif
    </form>
</div>

<div class="content-card">
    <div class="table-responsive">
        <table class="compact-table">
            <thead>
                <tr>
                    <th>Sr No</th>
                    <th>User</th>
                    <th>Feedback Message</th>
                    <th>Status</th>
                    <th>Submitted At</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($feedbacks as $feedback)
                    <tr>
                        <td><strong style="color: var(--text-primary);">{{ ($feedbacks->currentPage() - 1) * $feedbacks->perPage() + $loop->iteration }}</strong></td>
                        <td>
                            <div style="font-weight: 700; color: var(--text-primary);">{{ $feedback->user->name ?? 'Unknown User' }}</div>
                            <div style="font-size: 0.775rem; color: var(--text-muted);">{{ $feedback->user->email ?? 'N/A' }}</div>
                        </td>
                        <td>
                            <div class="feedback-msg">{{ \Illuminate\Support\Str::limit($feedback->message, 120) }}</div>
                        </td>
                        <td>
                            <span class="badge badge-{{ $feedback->status ?? 'pending' }}">{{ $feedback->status ?? 'pending' }}</span>
                        </td>
                        <td>{{ $feedback->created_at ? $feedback->created_at->format('M d, Y H:i') : 'N/A' }}</td>
                        <td>
                            <div class="action-dropdown">
                                <button type="button" class="btn-more" onclick="toggleDropdown(event, 'drop-fb-{{ $feedback->id }}')">
                                    <svg width="16" height="16" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M12 8c1.1 0 2-.9 2-2s-.9-2-2-2-2 .9-2 2 .9 2 2 2zm0 2c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2zm0 6c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2z"/>
                                    </svg>
                                </button>
                                <div id="drop-fb-{{ $feedback->id }}" class="dropdown-menu">
                                    <a href="javascript:void(0)" onclick='openViewFeedbackModal(@json($feedback))' class="dropdown-item item-view">
                                        <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                        View Message
                                    </a>
                                    @foreach(['pending', 'reviewed', 'resolved'] as $st)
                                        @if(($feedback->status ?? 'pending') !== $st)
                                            <form action="{{ route('admin.feedbacks.updateStatus', $feedback->id) }}" method="POST" style="margin:0;">
                                                @csrf
                                                @method('PUT')
                                                <input type="hidden" name="status" value="{{ $st }}">
                                                <button type="submit" class="dropdown-item" style="width:100%; border:none; background:transparent; cursor:pointer;">
                                                    Mark {{ ucfirst($st) }}
                                                </button>
                                            </form>
                                        @endif
                                    @endforeach
                                    <form id="delete-fb-{{ $feedback->id }}" action="{{ route('admin.feedbacks.destroy', $feedback->id) }}" method="POST" style="margin:0;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" class="dropdown-item item-delete" onclick="confirmAction({ title: 'Delete Feedback?', text: 'Are you sure you want to delete this feedback?', icon: 'warning', confirmText: 'Yes, Delete', confirmClass: 'swal2-confirm btn-danger', formId: 'delete-fb-{{ $feedback->id }}' })" style="width:100%; border:none; background:transparent; cursor:pointer;">
                                            <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            Delete
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" style="text-align: center; color: var(--text-muted); padding: 1.5rem;">
                            No customer feedback found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="pagination-wrapper">
        <div style="display: flex; align-items: center; gap: 1rem;">
            <span style="color: var(--text-muted); font-size: 0.8rem;">
                Showing {{ $feedbacks->firstItem() ?? 0 }} to {{ $feedbacks->lastItem() ?? 0 }} of {{ $feedbacks->total() }} feedbacks
            </span>
            <form action="{{ route('admin.feedbacks.index') }}" method="GET" style="margin:0;">
                @if($search)<input type="hidden" name="search" value="{{ $search }}">@endif
                @if($selectedStatus)<input type="hidden" name="status" value="{{ $selectedStatus }}">@endif
                <select name="per_page" class="per-page-select" onchange="this.form.submit()">
                    @foreach([10, 20, 30, 50, 100] as $size)
                        <option value="{{ $size }}" {{ $perPage == $size ? 'selected' : '' }}>{{ $size }} / page</option>
                    @endforeach
                </select>
            </form>
        </div>
        <div>
            {{ $feedbacks->links() }}
        </div>
    </div>
</div>

<!-- View Feedback Modal -->
<div id="viewFeedbackModal" class="modal-backdrop">
    <div class="modal-card">
        <div class="modal-header">
            <h3 class="modal-title">Customer Feedback</h3>
            <button type="button" class="btn-close" onclick="closeViewFeedbackModal()">&times;</button>
        </div>
        <div style="margin-bottom: 1rem;">
            <div style="font-size: 0.8rem; color: var(--text-muted); margin-bottom: 0.25rem;">Submitted By</div>
            <div id="fbModalUser" style="font-weight: 700; color: var(--text-primary);"></div>
        </div>
        <div style="margin-bottom: 1.25rem;">
            <div style="font-size: 0.8rem; color: var(--text-muted); margin-bottom: 0.35rem;">Message</div>
            <div id="fbModalMessage" style="background: var(--input-bg); border: 1px solid var(--card-border); border-radius: 10px; padding: 1rem; color: var(--text-primary); font-size: 0.9rem; line-height: 1.55; white-space: pre-wrap;"></div>
        </div>
        <div style="display: flex; justify-content: flex-end;">
            <button type="button" class="btn-secondary" onclick="closeViewFeedbackModal()">Close</button>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function toggleDropdown(event, id) {
        event.stopPropagation();
        document.querySelectorAll('.dropdown-menu').forEach(menu => {
            if (menu.id !== id) menu.classList.remove('show');
        });
        const target = document.getElementById(id);
        if (target) target.classList.toggle('show');
    }

    document.addEventListener('click', function () {
        document.querySelectorAll('.dropdown-menu').forEach(menu => menu.classList.remove('show'));
    });

    function openViewFeedbackModal(fb) {
        const userName = fb.user ? (fb.user.name || 'User') + ' (' + (fb.user.email || 'N/A') + ')' : 'Unknown User';
        document.getElementById('fbModalUser').innerText = userName;
        document.getElementById('fbModalMessage').innerText = fb.message || '';
        document.getElementById('viewFeedbackModal').style.display = 'flex';
    }

    function closeViewFeedbackModal() {
        document.getElementById('viewFeedbackModal').style.display = 'none';
    }
</script>
@endsection
