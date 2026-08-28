@extends('layouts.admin')

@section('title', 'Clinical Cards Management')

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
        width: 320px;
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

    .card-title-text {
        font-weight: 700;
        color: #ffffff;
        font-size: 0.875rem;
    }

    .card-desc-text {
        color: var(--text-secondary);
        font-size: 0.8rem;
        max-width: 260px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .file-btn {
        padding: 0.25rem 0.55rem;
        border-radius: 6px;
        font-size: 0.75rem;
        font-weight: 600;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 0.25rem;
        transition: all 0.2s ease;
    }

    .btn-action {
        padding: 0.4rem 0.75rem;
        border-radius: 8px;
        font-size: 0.8rem;
        font-weight: 600;
        border: none;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        transition: all 0.2s ease;
        text-decoration: none;
        white-space: nowrap;
    }

    .btn-add {
        background: rgba(99, 102, 241, 0.15);
        color: #818cf8;
        border: 1px solid rgba(99, 102, 241, 0.3);
    }

    .btn-add:hover {
        background: rgba(99, 102, 241, 0.3);
        color: #ffffff;
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

    .btn-view {
        background: rgba(16, 185, 129, 0.12);
        color: #34d399;
        border: 1px solid rgba(16, 185, 129, 0.25);
    }

    .btn-view:hover {
        background: rgba(16, 185, 129, 0.25);
        color: #ffffff;
    }

    .btn-download {
        background: rgba(168, 85, 247, 0.15);
        color: #c084fc;
        border: 1px solid rgba(168, 85, 247, 0.3);
    }

    .btn-download:hover {
        background: rgba(168, 85, 247, 0.3);
        color: #ffffff;
    }

    /* Custom File Selector Button Styling */
    .form-control[type="file"]::file-selector-button {
        background: rgba(99, 102, 241, 0.2);
        color: #818cf8;
        border: 1px solid rgba(99, 102, 241, 0.4);
        padding: 0.35rem 0.75rem;
        border-radius: 6px;
        font-size: 0.8rem;
        font-weight: 600;
        cursor: pointer;
        margin-right: 0.75rem;
        transition: all 0.2s ease;
    }

    .form-control[type="file"]::file-selector-button:hover {
        background: rgba(99, 102, 241, 0.35);
        color: #ffffff;
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
        min-width: 150px;
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
    }

    .dropdown-item:hover {
        background: rgba(255, 255, 255, 0.08);
    }

    .dropdown-item.item-view:hover { color: #34d399; }
    .dropdown-item.item-edit:hover { color: #60a5fa; }
    .dropdown-item.item-delete:hover { color: #fca5a5; }

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
        max-width: 620px;
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

    textarea.form-control {
        min-height: 90px;
        resize: vertical;
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

    .detail-preview-container {
        background: rgba(11, 15, 25, 0.7);
        border: 1px solid var(--card-border);
        border-radius: 10px;
        padding: 1rem;
        margin-top: 1rem;
    }

    .image-gallery-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(130px, 1fr));
        gap: 0.75rem;
        margin-top: 0.5rem;
    }

    .gallery-img-card {
        position: relative;
        border-radius: 8px;
        overflow: hidden;
        border: 1px solid var(--card-border);
        background: rgba(0, 0, 0, 0.3);
    }

    .gallery-img-card img {
        width: 100%;
        height: 100px;
        object-fit: cover;
        display: block;
    }

    .gallery-img-card .delete-btn {
        position: absolute;
        top: 4px;
        right: 4px;
        background: rgba(239, 68, 68, 0.85);
        color: #ffffff;
        border: none;
        border-radius: 4px;
        width: 22px;
        height: 22px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        font-size: 11px;
    }

    .gallery-img-card .delete-btn:hover {
        background: #ef4444;
    }

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

    /* CKEditor Dark Theme Overrides */
    .ck-editor__editable_inline {
        min-height: 180px;
        background-color: #0b0f19 !important;
        color: #ffffff !important;
        border-radius: 0 0 8px 8px !important;
    }
    .ck.ck-editor__main>.ck-editor__editable {
        background: #0b0f19 !important;
        color: #ffffff !important;
    }
    .ck.ck-toolbar {
        background-color: #1a2234 !important;
        border-color: rgba(255, 255, 255, 0.1) !important;
        border-radius: 8px 8px 0 0 !important;
    }
    .ck.ck-toolbar .ck-button {
        color: #cbd5e1 !important;
    }
    .ck.ck-toolbar .ck-button:hover {
        background-color: rgba(255, 255, 255, 0.1) !important;
        color: #ffffff !important;
    }
    .ck.ck-toolbar .ck-button.ck-on {
        background-color: var(--primary) !important;
        color: #ffffff !important;
    }
    .ck.ck-dropdown__panel {
        background: #161e2e !important;
        border-color: rgba(255, 255, 255, 0.1) !important;
    }
    .ck.ck-list__item .ck-button {
        color: #cbd5e1 !important;
    }
    .ck.ck-list__item .ck-button:hover {
        background: rgba(255, 255, 255, 0.1) !important;
        color: #ffffff !important;
    }
</style>
@endsection

@section('content')
<!-- Single Inline Header Row -->
<div class="page-header">
    <div>
        <h1 class="page-title">Clinical Cards Management</h1>
        <p class="page-subtitle">Manage quick reference clinical cards and medical flashcard documentation.</p>
    </div>

    <form action="{{ route('admin.cards.index') }}" method="GET" class="toolbar-inline">
        @if(request('sort_by'))
            <input type="hidden" name="sort_by" value="{{ request('sort_by') }}">
        @endif
        @if(request('sort_order'))
            <input type="hidden" name="sort_order" value="{{ request('sort_order') }}">
        @endif
        @if(request('per_page'))
            <input type="hidden" name="per_page" value="{{ request('per_page') }}">
        @endif

        <div class="search-box">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
            <input type="text" name="search" value="{{ $search }}" placeholder="Search title or description...">
            <button type="submit" class="btn-search">Search</button>
        </div>

        @if($search)
            <a href="{{ route('admin.cards.index') }}" class="clear-link">Clear</a>
        @endif

        <button type="button" class="btn-action btn-add" onclick="openCreateModal()">
            <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
            </svg>
            Add Card
        </button>
    </form>
</div>

<div class="content-card">
    <div class="table-responsive">
        <table class="compact-table">
            <thead>
                <tr>
                    @php
                        function cardSortUrl($field, $currentSortBy, $currentSortOrder) {
                            $order = ($currentSortBy === $field && $currentSortOrder === 'asc') ? 'desc' : 'asc';
                            return route('admin.cards.index', array_merge(request()->query(), ['sort_by' => $field, 'sort_order' => $order]));
                        }
                    @endphp
                    <th>
                        <a href="{{ cardSortUrl('id', $sortBy, $sortOrder) }}" class="sort-link {{ $sortBy === 'id' ? 'active' : '' }}">
                            ID
                            @if($sortBy === 'id')
                                <span>{{ $sortOrder === 'asc' ? '↑' : '↓' }}</span>
                            @endif
                        </a>
                    </th>
                    <th>
                        <a href="{{ cardSortUrl('title', $sortBy, $sortOrder) }}" class="sort-link {{ $sortBy === 'title' ? 'active' : '' }}">
                            Title
                            @if($sortBy === 'title')
                                <span>{{ $sortOrder === 'asc' ? '↑' : '↓' }}</span>
                            @endif
                        </a>
                    </th>
                    <th>Image</th>
                    <th>Document</th>
                    <th>
                        <a href="{{ cardSortUrl('created_at', $sortBy, $sortOrder) }}" class="sort-link {{ $sortBy === 'created_at' ? 'active' : '' }}">
                            Created At
                            @if($sortBy === 'created_at')
                                <span>{{ $sortOrder === 'asc' ? '↑' : '↓' }}</span>
                            @endif
                        </a>
                    </th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($cards as $card)
                    @php
                        $hasImages = ($card->images && $card->images->count() > 0) || !empty($card->image);
                        $imageCount = $card->images && $card->images->count() > 0 ? $card->images->count() : ($card->image ? 1 : 0);
                    @endphp
                    <tr>
                        <td><strong style="color: #ffffff;">#{{ $card->id }}</strong></td>
                        <td>
                            <div class="card-title-text">{{ $card->title }}</div>
                        </td>
                        <td>
                            @if($hasImages)
                                <a href="javascript:void(0)" onclick="openViewModal({{ json_encode($card) }})" class="file-btn btn-view" title="View Images">
                                    <svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                    Image {{ $imageCount > 1 ? "($imageCount)" : "" }}
                                </a>
                            @else
                                <span style="color: var(--text-muted); font-size: 0.775rem;">no image found</span>
                            @endif
                        </td>
                        <td>
                            @if($card->document)
                                @php
                                    $docPath = str_starts_with($card->document, 'uploads/') ? $card->document : 'uploads/cards/documents/' . $card->document;
                                @endphp
                                <div style="display: flex; gap: 0.35rem; align-items: center;">
                                    <a href="{{ asset($docPath) }}" target="_blank" class="file-btn btn-view" title="View PDF">
                                        <svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                        View
                                    </a>
                                    <a href="{{ asset($docPath) }}" download class="file-btn btn-download" title="Download PDF">
                                        <svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                        Download
                                    </a>
                                </div>
                            @else
                                <span style="color: var(--text-muted); font-size: 0.775rem;">no document found</span>
                            @endif
                        </td>
                        <td>{{ $card->created_at ? $card->created_at->format('M d, Y') : 'N/A' }}</td>
                        <td>
                            <!-- Action Dropdown with More Icon (⋮) -->
                            <div class="action-dropdown">
                                <button type="button" class="btn-more" onclick="toggleDropdown(event, 'drop-card-{{ $card->id }}')">
                                    <svg width="16" height="16" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M12 8c1.1 0 2-.9 2-2s-.9-2-2-2-2 .9-2 2 .9 2 2 2zm0 2c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2zm0 6c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2z"/>
                                    </svg>
                                </button>
                                <div id="drop-card-{{ $card->id }}" class="dropdown-menu">
                                    <a href="javascript:void(0)" onclick="openViewModal({{ json_encode($card) }})" class="dropdown-item item-view">
                                        <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                        View Details
                                    </a>
                                    <a href="javascript:void(0)" onclick="openEditModal({{ json_encode($card) }})" class="dropdown-item item-edit">
                                        <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                        Edit Card
                                    </a>
                                    <form id="delete-card-{{ $card->id }}" action="{{ route('admin.cards.destroy', $card->id) }}" method="POST" style="margin:0;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" class="dropdown-item item-delete" onclick="confirmAction({ title: 'Delete Clinical Card?', text: 'Are you sure you want to delete this clinical card?', icon: 'warning', confirmText: 'Yes, Delete', confirmClass: 'swal2-confirm btn-danger', formId: 'delete-card-{{ $card->id }}' })" style="width:100%; border:none; background:transparent; cursor:pointer;">
                                            <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            Delete Card
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" style="text-align: center; color: var(--text-muted); padding: 1.5rem;">
                            @if($search)
                                No clinical cards found matching "{{ $search }}".
                            @else
                                No clinical cards found. Click "Add Card" to create one.
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
                Showing {{ $cards->firstItem() ?? 0 }} to {{ $cards->lastItem() ?? 0 }} of {{ $cards->total() }} cards
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
            {{ $cards->links('pagination::bootstrap-4') }}
        </div>
    </div>
</div>

<!-- View Card Detail Modal -->
<div class="modal-backdrop" id="viewModal">
    <div class="modal-card">
        <div class="modal-header">
            <h2>Clinical Card Details</h2>
            <button class="btn-close" onclick="closeModal('viewModal')">&times;</button>
        </div>
        <div>
            <div style="margin-bottom: 0.75rem;">
                <h3 id="viewTitle" style="font-size: 1.1rem; font-weight: 800; color: #ffffff;"></h3>
            </div>

            <div style="margin-bottom: 1rem;">
                <label class="form-label">Description</label>
                <div id="viewDescription" style="color: var(--text-secondary); font-size: 0.875rem; line-height: 1.5; background: rgba(11,15,25,0.7); padding: 0.75rem; border-radius: 8px; border: 1px solid var(--card-border);"></div>
            </div>

            <!-- Image Preview Section -->
            <div id="viewImageWrapper" class="detail-preview-container">
                <label class="form-label">Images Gallery</label>
                <div id="viewImageGallery" class="image-gallery-grid"></div>
                <div id="viewNoImageText" style="color: var(--text-muted); font-size: 0.8rem; display: none;">no image found</div>
            </div>

            <!-- Document / PDF Actions -->
            <div id="viewDocWrapper" class="detail-preview-container" style="margin-top: 0.75rem;">
                <label class="form-label">Document Manual</label>
                <div id="viewDocContent">
                    <div id="viewDocName" style="color: #a5b4fc; font-size: 0.825rem; font-weight: 600; word-break: break-all; margin-bottom: 0.75rem;"></div>
                    <div style="display: flex; gap: 0.65rem; flex-wrap: wrap;">
                        <a id="viewDocLink" href="#" target="_blank" class="btn-action btn-view">
                            <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            View PDF
                        </a>
                        <a id="viewDownloadDocLink" href="#" download class="btn-action btn-download">
                            <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                            Download PDF
                        </a>
                    </div>
                </div>
                <div id="viewNoDocText" style="color: var(--text-muted); font-size: 0.8rem; display: none;">no document found</div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn-secondary" onclick="closeModal('viewModal')">Close</button>
        </div>
    </div>
</div>

<!-- Create Card Modal -->
<div class="modal-backdrop" id="createModal">
    <div class="modal-card">
        <div class="modal-header">
            <h2>Add New Clinical Card</h2>
            <button class="btn-close" onclick="closeModal('createModal')">&times;</button>
        </div>
        <form id="createCardForm" action="{{ route('admin.cards.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="form-group">
                <label class="form-label">Card Title</label>
                <input type="text" name="title" class="form-control" placeholder="e.g. Airway Assessment & Mallampati Score" required>
            </div>
            <div class="form-group">
                <label class="form-label">Description</label>
                <textarea name="description" id="createDescription" class="form-control" placeholder="Enter clinical card description..."></textarea>
            </div>
            <div class="form-group">
                <label class="form-label">Images (Multiple allowed, Optional)</label>
                <input type="file" name="images[]" class="form-control" accept="image/*" multiple>
                <small style="color: var(--text-muted); font-size: 0.75rem;">Select one or multiple images.</small>
            </div>
            <div class="form-group">
                <label class="form-label">Document Manual PDF File <span style="color: #ef4444;">*Required (PDF Only)</span></label>
                <input type="file" name="document" class="form-control" accept=".pdf" required>
                <small style="color: var(--text-muted); font-size: 0.75rem;">Only PDF files are allowed.</small>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-secondary" onclick="closeModal('createModal')">Cancel</button>
                <button type="submit" id="createSubmitBtn" class="btn-action btn-add" style="padding: 0.6rem 1.2rem;">Save Card</button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Card Modal -->
<div class="modal-backdrop" id="editModal">
    <div class="modal-card">
        <div class="modal-header">
            <h2>Edit Clinical Card</h2>
            <button class="btn-close" onclick="closeModal('editModal')">&times;</button>
        </div>
        <form id="editForm" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="form-group">
                <label class="form-label">Card Title</label>
                <input type="text" name="title" id="editTitle" class="form-control" required>
            </div>
            <div class="form-group">
                <label class="form-label">Description</label>
                <textarea name="description" id="editDescription" class="form-control"></textarea>
            </div>

            <!-- Existing Images List -->
            <div class="form-group">
                <label class="form-label">Existing Images</label>
                <div id="editExistingImages" class="image-gallery-grid" style="margin-bottom: 0.5rem;"></div>
            </div>

            <div class="form-group">
                <label class="form-label">Add More Images (Optional)</label>
                <input type="file" name="images[]" class="form-control" accept="image/*" multiple>
            </div>
            <div class="form-group">
                <label class="form-label">Replace Document PDF File (Optional, PDF Only)</label>
                <input type="file" name="document" class="form-control" accept=".pdf">
                <small style="color: var(--text-muted); font-size: 0.75rem;">Current: <span id="currentDocumentName"></span></small>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-secondary" onclick="closeModal('editModal')">Cancel</button>
                <button type="submit" id="editSubmitBtn" class="btn-action btn-edit" style="padding: 0.6rem 1.2rem;">Update Card</button>
            </div>
        </form>
    </div>
</div>

<!-- Card Submit Loading Overlay -->
<div class="loading-overlay" id="cardLoadingOverlay" style="display: none;">
    <div class="loading-card">
        <div class="spinner-outer">
            <div class="spinner-ring"></div>
            <svg class="spinner-icon" width="24" height="24" style="width: 24px; height: 24px; max-width: 24px; max-height: 24px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
            </svg>
        </div>
        <div>
            <div class="loading-title" id="loadingOverlayTitle">Saving Clinical Card...</div>
            <div class="loading-subtitle" id="loadingOverlaySubtitle">Please wait while your files (PDF, images) are uploaded and processed.</div>
        </div>
        <div class="loading-progress-badge">
            <div class="pulse-dot"></div>
            <span>Uploading data, please do not close page...</span>
        </div>
    </div>
</div>

<!-- Hidden Form for Image Deletion -->
<form id="deleteImageForm" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>

<!-- Custom Dark Theme Confirm Image Delete Modal -->
<div class="modal-backdrop" id="confirmDeleteModal" style="z-index: 1100;">
    <div class="modal-card" style="max-width: 420px; text-align: center; padding: 1.75rem 1.5rem; background: #161e2e; border: 1px solid var(--card-border); border-radius: 16px; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.7);">
        <div style="width: 54px; height: 54px; border-radius: 50%; background: rgba(239, 68, 68, 0.12); color: #ef4444; display: flex; align-items: center; justify-content: center; margin: 0 auto 1.1rem auto; border: 1px solid rgba(239, 68, 68, 0.25);">
            <svg width="26" height="26" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
            </svg>
        </div>
        <h3 style="font-size: 1.15rem; font-weight: 700; color: #ffffff; margin-bottom: 0.5rem;">Delete Image?</h3>
        <p style="font-size: 0.85rem; color: #94a3b8; margin-bottom: 1.5rem; line-height: 1.5;">Are you sure you want to remove this image from the clinical card? This action cannot be undone.</p>
        <div style="display: flex; gap: 0.75rem; justify-content: center;">
            <button type="button" class="btn-secondary" onclick="closeConfirmDeleteModal()" style="padding: 0.65rem 1.25rem; flex: 1; font-weight: 600;">Cancel</button>
            <button type="button" id="confirmDeleteSubmitBtn" class="btn-action" style="background: #ef4444; color: #ffffff; border: none; padding: 0.65rem 1.25rem; flex: 1; border-radius: 8px; font-weight: 600; cursor: pointer;">Delete Image</button>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.ckeditor.com/ckeditor5/39.0.1/classic/ckeditor.js"></script>
<script>
    const assetBaseUrl = "{{ asset('') }}";
    let createDescriptionEditor = null;
    let editDescriptionEditor = null;
    let pendingDeleteImageId = null;
    let pendingDeleteBtnElement = null;

    document.addEventListener("DOMContentLoaded", function () {
        ClassicEditor.create(document.querySelector('#createDescription'))
            .then(editor => { createDescriptionEditor = editor; })
            .catch(error => { console.error('CKEditor Create Error:', error); });

        ClassicEditor.create(document.querySelector('#editDescription'))
            .then(editor => { editDescriptionEditor = editor; })
            .catch(error => { console.error('CKEditor Edit Error:', error); });

        const confirmBtn = document.getElementById('confirmDeleteSubmitBtn');
        if (confirmBtn) {
            confirmBtn.addEventListener('click', executeCardImageDelete);
        }
    });

    function openCreateModal() {
        if (createDescriptionEditor) {
            createDescriptionEditor.setData('');
        }
        document.getElementById('createModal').classList.add('show');
    }

    function openViewModal(card) {
        document.getElementById('viewTitle').innerText = card.title;
        renderFormattedDescription(card.description, 'viewDescription');

        // Images Gallery logic
        const galleryContainer = document.getElementById('viewImageGallery');
        const noImageText = document.getElementById('viewNoImageText');
        galleryContainer.innerHTML = '';

        let imagesList = [];
        if (card.images && card.images.length > 0) {
            imagesList = card.images.map(img => img.image);
        } else if (card.image) {
            imagesList = [card.image];
        }

        if (imagesList.length > 0) {
            noImageText.style.display = 'none';
            galleryContainer.style.display = 'grid';
            imagesList.forEach(img => {
                const imgPath = img.startsWith('uploads/') ? img : 'uploads/cards/images/' + img;
                const cardEl = document.createElement('div');
                cardEl.className = 'gallery-img-card';
                cardEl.innerHTML = `
                    <a href="${assetBaseUrl + imgPath}" target="_blank">
                        <img src="${assetBaseUrl + imgPath}" alt="Card Image">
                    </a>
                `;
                galleryContainer.appendChild(cardEl);
            });
        } else {
            galleryContainer.style.display = 'none';
            noImageText.style.display = 'block';
        }

        // Document logic
        const docContent = document.getElementById('viewDocContent');
        const noDocText = document.getElementById('viewNoDocText');

        if (card.document) {
            var docPath = card.document.startsWith('uploads/') ? card.document : 'uploads/cards/documents/' . card.document;
            var docUrl = assetBaseUrl + docPath;
            document.getElementById('viewDocName').innerText = card.document.split('/').pop();
            document.getElementById('viewDocLink').href = docUrl;
            document.getElementById('viewDownloadDocLink').href = docUrl;
            docContent.style.display = 'block';
            noDocText.style.display = 'none';
        } else {
            docContent.style.display = 'none';
            noDocText.style.display = 'block';
        }

        document.getElementById('viewModal').classList.add('show');
    }

    function openEditModal(card) {
        document.getElementById('editForm').action = "{{ url('admin/cards') }}/" + card.id;
        document.getElementById('editTitle').value = card.title;
        if (editDescriptionEditor) {
            editDescriptionEditor.setData(card.description || '');
        } else {
            document.getElementById('editDescription').value = card.description || '';
        }
        document.getElementById('currentDocumentName').innerText = card.document ? card.document.split('/').pop() : 'no document found';

        // Render Existing Images with delete option
        const existingContainer = document.getElementById('editExistingImages');
        existingContainer.innerHTML = '';

        if (card.images && card.images.length > 0) {
            card.images.forEach(imgRecord => {
                const imgPath = imgRecord.image.startsWith('uploads/') ? imgRecord.image : 'uploads/cards/images/' + imgRecord.image;
                const cardEl = document.createElement('div');
                cardEl.className = 'gallery-img-card';
                cardEl.innerHTML = `
                    <img src="${assetBaseUrl + imgPath}" alt="Card Image">
                    <button type="button" class="delete-btn" title="Delete Image" onclick="promptDeleteCardImage(${imgRecord.id}, this)">&times;</button>
                `;
                existingContainer.appendChild(cardEl);
            });
        } else if (card.image) {
            const imgPath = card.image.startsWith('uploads/') ? card.image : 'uploads/cards/images/' + card.image;
            const cardEl = document.createElement('div');
            cardEl.className = 'gallery-img-card';
            cardEl.innerHTML = `<img src="${assetBaseUrl + imgPath}" alt="Card Image">`;
            existingContainer.appendChild(cardEl);
        } else {
            existingContainer.innerHTML = '<span style="color: var(--text-muted); font-size: 0.775rem;">no image found</span>';
        }

        document.getElementById('editModal').classList.add('show');
    }

    function promptDeleteCardImage(imageId, btnElement) {
        pendingDeleteImageId = imageId;
        pendingDeleteBtnElement = btnElement;
        document.getElementById('confirmDeleteModal').classList.add('show');
    }

    function closeConfirmDeleteModal() {
        pendingDeleteImageId = null;
        pendingDeleteBtnElement = null;
        document.getElementById('confirmDeleteModal').classList.remove('show');
    }

    function executeCardImageDelete() {
        if (!pendingDeleteImageId) return;

        const confirmBtn = document.getElementById('confirmDeleteSubmitBtn');
        if (confirmBtn) {
            confirmBtn.innerText = 'Deleting...';
            confirmBtn.disabled = true;
        }

        const deleteId = pendingDeleteImageId;
        const targetBtn = pendingDeleteBtnElement;

        fetch("{{ url('admin/cards/images') }}/" + deleteId, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ _method: 'DELETE' })
        })
        .then(response => response.json())
        .then(data => {
            if (confirmBtn) {
                confirmBtn.innerText = 'Delete Image';
                confirmBtn.disabled = false;
            }
            closeConfirmDeleteModal();

            if (data.status) {
                if (targetBtn) {
                    const cardEl = targetBtn.closest('.gallery-img-card');
                    if (cardEl) {
                        cardEl.remove();
                    }
                }
                const container = document.getElementById('editExistingImages');
                if (container && container.children.length === 0) {
                    container.innerHTML = '<span style="color: var(--text-muted); font-size: 0.775rem;">no image found</span>';
                }
            } else {
                alert(data.message || 'Error deleting image');
            }
        })
        .catch(err => {
            if (confirmBtn) {
                confirmBtn.innerText = 'Delete Image';
                confirmBtn.disabled = false;
            }
            closeConfirmDeleteModal();
            console.error('Delete image error:', err);
            alert('Failed to delete image.');
        });
    }

    function closeModal(modalId) {
        document.getElementById(modalId).classList.remove('show');
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

    function renderFormattedDescription(fullHtml, targetElementId) {
        const descContainer = document.getElementById(targetElementId);
        if (!descContainer) return;

        if (!fullHtml || !fullHtml.trim()) {
            descContainer.innerHTML = '<span style="color: var(--text-muted);">N/A</span>';
            return;
        }

        const tempDiv = document.createElement('div');
        tempDiv.innerHTML = fullHtml;
        const plainText = (tempDiv.textContent || tempDiv.innerText || '').trim();
        const words = plainText.split(/\s+/).filter(w => w.length > 0);

        if (words.length > 100) {
            const shortText = words.slice(0, 100).join(' ') + '...';
            descContainer.innerHTML = `
                <div id="${targetElementId}-short" style="line-height: 1.6; color: #cbd5e1;">${shortText}</div>
                <div id="${targetElementId}-full" style="display: none; line-height: 1.6; color: #cbd5e1;">${fullHtml}</div>
                <a href="javascript:void(0)" id="${targetElementId}-toggle-btn" onclick="toggleDescriptionTruncate('${targetElementId}')" style="color: #818cf8; font-weight: 700; font-size: 0.85rem; text-decoration: none; margin-top: 0.5rem; display: inline-block;">
                    Show More...
                </a>
            `;
        } else {
            descContainer.innerHTML = `<div style="line-height: 1.6; color: #cbd5e1;">${fullHtml}</div>`;
        }
    }

    function toggleDescriptionTruncate(targetElementId) {
        const shortEl = document.getElementById(targetElementId + '-short');
        const fullEl = document.getElementById(targetElementId + '-full');
        const btn = document.getElementById(targetElementId + '-toggle-btn');

        if (!shortEl || !fullEl || !btn) return;

        if (fullEl.style.display === 'none') {
            fullEl.style.display = 'block';
            shortEl.style.display = 'none';
            btn.innerText = 'Show Less';
        } else {
            fullEl.style.display = 'none';
            shortEl.style.display = 'block';
            btn.innerText = 'Show More...';
        }
    }

    function validateCardFileSizes(formEl) {
        const MAX_DOC_SIZE = 10 * 1024 * 1024; // 10 MB
        const MAX_IMG_SIZE = 5 * 1024 * 1024;   // 5 MB

        const docInput = formEl.querySelector('input[name="document"]');
        if (docInput && docInput.files && docInput.files.length > 0) {
            const file = docInput.files[0];
            if (file.size > MAX_DOC_SIZE) {
                const sizeMB = (file.size / (1024 * 1024)).toFixed(1);
                Swal.fire({
                    title: 'File Too Large!',
                    text: `The selected PDF document "${file.name}" is ${sizeMB} MB. Maximum allowed limit is 10 MB.`,
                    icon: 'error',
                    background: '#161e2e',
                    color: '#ffffff',
                    confirmButtonText: 'OK',
                    customClass: { confirmButton: 'swal2-confirm btn-danger' },
                    buttonsStyling: false
                });
                return false;
            }
        }

        const imgInputs = formEl.querySelectorAll('input[type="file"][name="images[]"]');
        for (let input of imgInputs) {
            if (input.files && input.files.length > 0) {
                for (let file of input.files) {
                    if (file.size > MAX_IMG_SIZE) {
                        const sizeMB = (file.size / (1024 * 1024)).toFixed(1);
                        Swal.fire({
                            title: 'Image Too Large!',
                            text: `The image "${file.name}" is ${sizeMB} MB. Maximum allowed limit is 5 MB per image.`,
                            icon: 'error',
                            background: '#161e2e',
                            color: '#ffffff',
                            confirmButtonText: 'OK',
                            customClass: { confirmButton: 'swal2-confirm btn-danger' },
                            buttonsStyling: false
                        });
                        return false;
                    }
                }
            }
        }

        return true;
    }

    function showCardSubmitLoader(submitBtnEl, loadingText) {
        if (submitBtnEl) {
            submitBtnEl.disabled = true;
            submitBtnEl.style.opacity = '0.75';
            submitBtnEl.style.cursor = 'not-allowed';
            submitBtnEl.innerHTML = `<span class="btn-spinner"></span> ${loadingText}`;
        }
        const overlay = document.getElementById('cardLoadingOverlay');
        const titleEl = document.getElementById('loadingOverlayTitle');
        if (titleEl) titleEl.innerText = loadingText;
        if (overlay) overlay.classList.add('show');
    }

    document.addEventListener('DOMContentLoaded', function() {
        const createForm = document.getElementById('createCardForm');
        if (createForm) {
            createForm.addEventListener('submit', function(e) {
                if (!validateCardFileSizes(createForm)) {
                    e.preventDefault();
                    return false;
                }
                const btn = document.getElementById('createSubmitBtn');
                showCardSubmitLoader(btn, 'Saving Card...');
            });
        }

        const editForm = document.getElementById('editForm');
        if (editForm) {
            editForm.addEventListener('submit', function(e) {
                if (!validateCardFileSizes(editForm)) {
                    e.preventDefault();
                    return false;
                }
                const btn = document.getElementById('editSubmitBtn');
                showCardSubmitLoader(btn, 'Updating Card...');
            });
        }
    });
</script>
@endsection
