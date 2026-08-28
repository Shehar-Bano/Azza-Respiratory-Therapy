@extends('layouts.admin')

@section('title', 'Article Management')

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

    .article-title {
        font-weight: 700;
        color: #ffffff;
        font-size: 0.875rem;
    }

    .article-desc {
        color: var(--text-secondary);
        font-size: 0.8rem;
        max-width: 240px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .category-badge {
        display: inline-block;
        padding: 0.25rem 0.6rem;
        border-radius: 6px;
        font-size: 0.75rem;
        font-weight: 600;
        background: rgba(99, 102, 241, 0.15);
        color: #a5b4fc;
        border: 1px solid rgba(99, 102, 241, 0.3);
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
        <h1 class="page-title">Article Management</h1>
        <p class="page-subtitle">Manage clinical articles, ABG guides, image assets, and documentation manuals.</p>
    </div>

    <form action="{{ route('admin.articles.index') }}" method="GET" class="toolbar-inline">
        @if(request('sort_by'))
            <input type="hidden" name="sort_by" value="{{ request('sort_by') }}">
        @endif
        @if(request('sort_order'))
            <input type="hidden" name="sort_order" value="{{ request('sort_order') }}">
        @endif
        @if(request('per_page'))
            <input type="hidden" name="per_page" value="{{ request('per_page') }}">
        @endif

        <div style="display: flex; align-items: center; gap: 0.35rem;">
            <select name="category_id" onchange="this.form.submit()" class="per-page-select" style="padding: 0.4rem 0.65rem; border-radius: 10px; font-size: 0.825rem;">
                <option value="">All Categories</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}" {{ ($selectedCategoryId ?? '') == $cat->id ? 'selected' : '' }}>
                        {{ $cat->category_name }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="search-box">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
            <input type="text" name="search" value="{{ $search }}" placeholder="Search title or description...">
            <button type="submit" class="btn-search">Search</button>
        </div>

        @if($search || !empty($selectedCategoryId))
            <a href="{{ route('admin.articles.index') }}" class="clear-link">Clear</a>
        @endif

        <button type="button" class="btn-action btn-add" onclick="openCreateModal()">
            <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
            </svg>
            Add Article
        </button>
    </form>
</div>

<div class="content-card">
    <div class="table-responsive">
        <table class="compact-table">
            <thead>
                <tr>
                    @php
                        function articleSortUrl($field, $currentSortBy, $currentSortOrder) {
                            $order = ($currentSortBy === $field && $currentSortOrder === 'asc') ? 'desc' : 'asc';
                            return route('admin.articles.index', array_merge(request()->query(), ['sort_by' => $field, 'sort_order' => $order]));
                        }
                    @endphp
                    <th>
                        <a href="{{ articleSortUrl('id', $sortBy, $sortOrder) }}" class="sort-link {{ $sortBy === 'id' ? 'active' : '' }}">
                            ID
                            @if($sortBy === 'id')
                                <span>{{ $sortOrder === 'asc' ? '↑' : '↓' }}</span>
                            @endif
                        </a>
                    </th>
                    <th>Category</th>
                    <th>
                        <a href="{{ articleSortUrl('title', $sortBy, $sortOrder) }}" class="sort-link {{ $sortBy === 'title' ? 'active' : '' }}">
                            Title
                            @if($sortBy === 'title')
                                <span>{{ $sortOrder === 'asc' ? '↑' : '↓' }}</span>
                            @endif
                        </a>
                    </th>
                    <th>Image</th>
                    <th>Document</th>
                    <th>Video</th>
                    <th>
                        <a href="{{ articleSortUrl('created_at', $sortBy, $sortOrder) }}" class="sort-link {{ $sortBy === 'created_at' ? 'active' : '' }}">
                            Created At
                            @if($sortBy === 'created_at')
                                <span>{{ $sortOrder === 'asc' ? '↑' : '↓' }}</span>
                            @endif
                        </a>
                    </th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="articlesTableBody">
                @forelse($articles as $article)
                    @php
                        $hasImages = ($article->images && $article->images->count() > 0) || !empty($article->image);
                        $imageCount = $article->images && $article->images->count() > 0 ? $article->images->count() : ($article->image ? 1 : 0);
                    @endphp
                    <tr id="article-row-{{ $article->id }}">
                        <td><strong style="color: #ffffff;">#{{ $article->id }}</strong></td>
                        <td>
                            @if($article->category)
                                <span class="category-badge">{{ $article->category->category_name }}</span>
                            @else
                                <span style="color: var(--text-muted); font-size: 0.775rem;">Uncategorized</span>
                            @endif
                        </td>
                        <td>
                            <div class="article-title">{{ $article->title }}</div>
                        </td>
                        <td>
                            @if($hasImages)
                                <a href="javascript:void(0)" onclick="openViewModal({{ json_encode($article) }})" class="file-btn btn-view" title="View Images">
                                    <svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                    Image {{ $imageCount > 1 ? "($imageCount)" : "" }}
                                </a>
                            @else
                                <span style="color: var(--text-muted); font-size: 0.775rem;">no image found</span>
                            @endif
                        </td>
                        <td>
                            @if($article->document)
                                @php
                                    $docPath = str_starts_with($article->document, 'uploads/') ? $article->document : 'uploads/articles/documents/' . $article->document;
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
                        <td id="video-cell-{{ $article->id }}">
                            @if($article->video)
                                <a href="javascript:void(0)" onclick="openViewModal({{ json_encode($article) }})" class="file-btn btn-view" title="Watch Video">
                                    <svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    Video
                                </a>
                            @else
                                <span style="color: var(--text-muted); font-size: 0.775rem;">no video found</span>
                            @endif
                        </td>
                        <td>{{ $article->created_at ? $article->created_at->format('M d, Y') : 'N/A' }}</td>
                        <td>
                            <!-- Action Dropdown with More Icon (⋮) -->
                            <div class="action-dropdown">
                                <button type="button" class="btn-more" onclick="toggleDropdown(event, 'drop-article-{{ $article->id }}')">
                                    <svg width="16" height="16" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M12 8c1.1 0 2-.9 2-2s-.9-2-2-2-2 .9-2 2 .9 2 2 2zm0 2c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2zm0 6c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2z"/>
                                    </svg>
                                </button>
                                <div id="drop-article-{{ $article->id }}" class="dropdown-menu">
                                    <a href="javascript:void(0)" onclick="openViewModal({{ json_encode($article) }})" class="dropdown-item item-view">
                                        <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                        View Details
                                    </a>
                                    <a href="javascript:void(0)" onclick="openEditModal({{ json_encode($article) }})" class="dropdown-item item-edit">
                                        <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                        Edit Article
                                    </a>
                                    <form id="delete-article-{{ $article->id }}" action="{{ route('admin.articles.destroy', $article->id) }}" method="POST" style="margin:0;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" class="dropdown-item item-delete" onclick="confirmAction({ title: 'Delete Article?', text: 'Are you sure you want to delete this article?', icon: 'warning', confirmText: 'Yes, Delete', confirmClass: 'swal2-confirm btn-danger', formId: 'delete-article-{{ $article->id }}' })" style="width:100%; border:none; background:transparent; cursor:pointer;">
                                            <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            Delete Article
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" style="text-align: center; color: var(--text-muted); padding: 1.5rem;">
                            @if($search)
                                No articles found matching "{{ $search }}".
                            @else
                                No articles found. Click "Add Article" to create one.
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
                Showing {{ $articles->firstItem() ?? 0 }} to {{ $articles->lastItem() ?? 0 }} of {{ $articles->total() }} articles
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
            {{ $articles->links('pagination::bootstrap-4') }}
        </div>
    </div>
</div>

<!-- View Article Detail Modal -->
<div class="modal-backdrop" id="viewModal">
    <div class="modal-card">
        <div class="modal-header">
            <h2>Article Details</h2>
            <button class="btn-close" onclick="closeModal('viewModal')">&times;</button>
        </div>
        <div>
            <div style="margin-bottom: 0.75rem;">
                <span id="viewCategoryBadge" class="category-badge"></span>
                <h3 id="viewTitle" style="font-size: 1.1rem; font-weight: 800; color: #ffffff; margin-top: 0.4rem;"></h3>
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

            <!-- Video Media Player Section -->
            <div id="viewVideoWrapper" class="detail-preview-container" style="margin-top: 0.75rem;">
                <label class="form-label">Article Video</label>
                <div id="viewVideoContent">
                    <video id="viewVideoPlayer" controls style="width: 100%; max-height: 260px; border-radius: 8px; background: #000; margin-bottom: 0.5rem; display: block;"></video>
                    <div style="display: flex; gap: 0.65rem; flex-wrap: wrap;">
                        <a id="viewDownloadVideoLink" href="#" download class="btn-action btn-download">
                            <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                            Download Video
                        </a>
                    </div>
                </div>
                <div id="viewNoVideoText" style="color: var(--text-muted); font-size: 0.8rem; display: none;">no video found</div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn-secondary" onclick="closeModal('viewModal')">Close</button>
        </div>
    </div>
</div>

<!-- Create Article Modal -->
<div class="modal-backdrop" id="createModal">
    <div class="modal-card">
        <div class="modal-header">
            <h2>Add New Article</h2>
            <button class="btn-close" onclick="closeModal('createModal')">&times;</button>
        </div>
        <form id="createArticleForm" action="{{ route('admin.articles.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="form-group">
                <label class="form-label">Category</label>
                <select name="category_id" class="form-control">
                    <option value="">-- Select Category --</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}">{{ $cat->category_name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Article Title</label>
                <input type="text" name="title" class="form-control" placeholder="e.g. PEEP Titration Protocol" required>
            </div>
            <div class="form-group">
                <label class="form-label">Description</label>
                <textarea name="description" id="createDescription" class="form-control" placeholder="Enter article content description..."></textarea>
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
            <div class="form-group">
                <label class="form-label">Video File (Optional, MP4/MOV/WEBM)</label>
                <input type="file" name="video" class="form-control" accept="video/*">
                <small style="color: var(--text-muted); font-size: 0.75rem;">Select a video file to attach.</small>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-secondary" onclick="closeModal('createModal')">Cancel</button>
                <button type="submit" id="createSubmitBtn" class="btn-action btn-add" style="padding: 0.6rem 1.2rem;">Save Article</button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Article Modal -->
<div class="modal-backdrop" id="editModal">
    <div class="modal-card">
        <div class="modal-header">
            <h2>Edit Article</h2>
            <button class="btn-close" onclick="closeModal('editModal')">&times;</button>
        </div>
        <form id="editForm" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="form-group">
                <label class="form-label">Category</label>
                <select name="category_id" id="editCategory" class="form-control">
                    <option value="">-- Select Category --</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}">{{ $cat->category_name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Article Title</label>
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
            <div class="form-group">
                <label class="form-label">Replace Video File (Optional)</label>
                <input type="file" name="video" class="form-control" accept="video/*">
                <small style="color: var(--text-muted); font-size: 0.75rem;">Current: <span id="currentVideoName"></span></small>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-secondary" onclick="closeModal('editModal')">Cancel</button>
                <button type="submit" id="editSubmitBtn" class="btn-action btn-edit" style="padding: 0.6rem 1.2rem;">Update Article</button>
            </div>
        </form>
    </div>
</div>

<!-- Article Submit Loading Overlay -->
<div class="loading-overlay" id="articleLoadingOverlay" style="display: none;">
    <div class="loading-card">
        <div class="spinner-outer">
            <div class="spinner-ring"></div>
            <svg class="spinner-icon" width="24" height="24" style="width: 24px; height: 24px; max-width: 24px; max-height: 24px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
            </svg>
        </div>
        <div>
            <div class="loading-title" id="loadingOverlayTitle">Saving Article...</div>
            <div class="loading-subtitle" id="loadingOverlaySubtitle">Please wait while your files (PDF, video, images) are uploaded and processed.</div>
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
        <p style="font-size: 0.85rem; color: #94a3b8; margin-bottom: 1.5rem; line-height: 1.5;">Are you sure you want to remove this image from the article? This action cannot be undone.</p>
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
            confirmBtn.addEventListener('click', executeImageDelete);
        }
    });

    function openCreateModal() {
        if (createDescriptionEditor) {
            createDescriptionEditor.setData('');
        }
        document.getElementById('createModal').classList.add('show');
    }

    function openViewModal(article) {
        document.getElementById('viewTitle').innerText = article.title;
        renderFormattedDescription(article.description, 'viewDescription');
        document.getElementById('viewCategoryBadge').innerText = article.category ? article.category.category_name : 'Uncategorized';

        // Images Gallery logic
        const galleryContainer = document.getElementById('viewImageGallery');
        const noImageText = document.getElementById('viewNoImageText');
        galleryContainer.innerHTML = '';

        let imagesList = [];
        if (article.images && article.images.length > 0) {
            imagesList = article.images.map(img => img.image);
        } else if (article.image) {
            imagesList = [article.image];
        }

        if (imagesList.length > 0) {
            noImageText.style.display = 'none';
            galleryContainer.style.display = 'grid';
            imagesList.forEach(img => {
                const imgPath = img.startsWith('uploads/') ? img : 'uploads/articles/images/' + img;
                const card = document.createElement('div');
                card.className = 'gallery-img-card';
                card.innerHTML = `
                    <a href="${assetBaseUrl + imgPath}" target="_blank">
                        <img src="${assetBaseUrl + imgPath}" alt="Article Image">
                    </a>
                `;
                galleryContainer.appendChild(card);
            });
        } else {
            galleryContainer.style.display = 'none';
            noImageText.style.display = 'block';
        }

        // Document logic
        const docContent = document.getElementById('viewDocContent');
        const noDocText = document.getElementById('viewNoDocText');

        if (article.document) {
            var docPath = article.document.startsWith('uploads/') ? article.document : 'uploads/articles/documents/' + article.document;
            var docUrl = assetBaseUrl + docPath;
            document.getElementById('viewDocName').innerText = article.document.split('/').pop();
            document.getElementById('viewDocLink').href = docUrl;
            document.getElementById('viewDownloadDocLink').href = docUrl;
            docContent.style.display = 'block';
            noDocText.style.display = 'none';
        } else {
            docContent.style.display = 'none';
            noDocText.style.display = 'block';
        }

        // Video logic
        const videoContent = document.getElementById('viewVideoContent');
        const noVideoText = document.getElementById('viewNoVideoText');
        const videoPlayer = document.getElementById('viewVideoPlayer');
        const downloadVideoLink = document.getElementById('viewDownloadVideoLink');

        if (article.video) {
            var vidPath = article.video.startsWith('uploads/') ? article.video : 'uploads/articles/videos/' + article.video;
            var vidUrl = assetBaseUrl + vidPath;
            videoPlayer.src = vidUrl;
            downloadVideoLink.href = vidUrl;
            videoContent.style.display = 'block';
            noVideoText.style.display = 'none';
        } else {
            videoPlayer.src = '';
            videoContent.style.display = 'none';
            noVideoText.style.display = 'block';
        }

        document.getElementById('viewModal').classList.add('show');
    }

    function openEditModal(article) {
        document.getElementById('editForm').action = "{{ url('admin/articles') }}/" + article.id;
        document.getElementById('editCategory').value = article.category_id ? article.category_id : '';
        document.getElementById('editTitle').value = article.title;
        if (editDescriptionEditor) {
            editDescriptionEditor.setData(article.description || '');
        } else {
            document.getElementById('editDescription').value = article.description || '';
        }
        document.getElementById('currentDocumentName').innerText = article.document ? article.document.split('/').pop() : 'no document found';
        document.getElementById('currentVideoName').innerText = article.video ? article.video.split('/').pop() : 'no video found';

        // Render Existing Images with delete option
        const existingContainer = document.getElementById('editExistingImages');
        existingContainer.innerHTML = '';

        if (article.images && article.images.length > 0) {
            article.images.forEach(imgRecord => {
                const imgPath = imgRecord.image.startsWith('uploads/') ? imgRecord.image : 'uploads/articles/images/' + imgRecord.image;
                const card = document.createElement('div');
                card.className = 'gallery-img-card';
                card.innerHTML = `
                    <img src="${assetBaseUrl + imgPath}" alt="Article Image">
                    <button type="button" class="delete-btn" title="Delete Image" onclick="promptDeleteArticleImage(${imgRecord.id}, this)">&times;</button>
                `;
                existingContainer.appendChild(card);
            });
        } else if (article.image) {
            const imgPath = article.image.startsWith('uploads/') ? article.image : 'uploads/articles/images/' + article.image;
            const card = document.createElement('div');
            card.className = 'gallery-img-card';
            card.innerHTML = `<img src="${assetBaseUrl + imgPath}" alt="Article Image">`;
            existingContainer.appendChild(card);
        } else {
            existingContainer.innerHTML = '<span style="color: var(--text-muted); font-size: 0.775rem;">no image found</span>';
        }

        document.getElementById('editModal').classList.add('show');
    }

    function promptDeleteArticleImage(imageId, btnElement) {
        pendingDeleteImageId = imageId;
        pendingDeleteBtnElement = btnElement;
        document.getElementById('confirmDeleteModal').classList.add('show');
    }

    function closeConfirmDeleteModal() {
        pendingDeleteImageId = null;
        pendingDeleteBtnElement = null;
        document.getElementById('confirmDeleteModal').classList.remove('show');
    }

    function executeImageDelete() {
        if (!pendingDeleteImageId) return;

        const confirmBtn = document.getElementById('confirmDeleteSubmitBtn');
        if (confirmBtn) {
            confirmBtn.innerText = 'Deleting...';
            confirmBtn.disabled = true;
        }

        const deleteId = pendingDeleteImageId;
        const targetBtn = pendingDeleteBtnElement;

        fetch("{{ url('admin/articles/images') }}/" + deleteId, {
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

    function validateFormFileSizes(formEl) {
        const MAX_DOC_SIZE = 10 * 1024 * 1024;   // 10 MB
        const MAX_VIDEO_SIZE = 100 * 1024 * 1024; // 100 MB
        const MAX_IMG_SIZE = 5 * 1024 * 1024;    // 5 MB

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

        const videoInput = formEl.querySelector('input[name="video"]');
        if (videoInput && videoInput.files && videoInput.files.length > 0) {
            const file = videoInput.files[0];
            if (file.size > MAX_VIDEO_SIZE) {
                const sizeMB = (file.size / (1024 * 1024)).toFixed(1);
                Swal.fire({
                    title: 'Video Too Large!',
                    text: `The selected video file "${file.name}" is ${sizeMB} MB. Maximum allowed limit is 100 MB.`,
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

    function uploadVideoInBackground(articleId, videoFile, articleTitle) {
        const videoCell = document.getElementById('video-cell-' + articleId);
        if (videoCell) {
            videoCell.innerHTML = `
                <span class="video-upload-badge" id="video-badge-${articleId}">
                    <span class="btn-spinner"></span> Uploading (0%)...
                </span>
            `;
        }

        const formData = new FormData();
        formData.append('video', videoFile);
        formData.append('_token', '{{ csrf_token() }}');

        const xhr = new XMLHttpRequest();
        xhr.open('POST', `{{ url('admin/articles') }}/${articleId}/upload-video`, true);
        xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
        xhr.setRequestHeader('Accept', 'application/json');

        xhr.upload.addEventListener('progress', function(e) {
            if (e.lengthComputable) {
                const percentComplete = Math.round((e.loaded / e.total) * 100);
                const badge = document.getElementById('video-badge-' + articleId);
                if (badge) {
                    badge.innerHTML = `<span class="btn-spinner"></span> Uploading (${percentComplete}%)...`;
                }
            }
        });

        xhr.onload = function() {
            if (xhr.status >= 200 && xhr.status < 300) {
                try {
                    const response = JSON.parse(xhr.responseText);
                    if (response.status) {
                        const article = response.article || {};
                        const articleJson = JSON.stringify(article).replace(/"/g, '&quot;');
                        if (videoCell) {
                            videoCell.innerHTML = `
                                <a href="javascript:void(0)" onclick="openViewModal(${articleJson})" class="file-btn btn-view" title="Watch Video">
                                    <svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    Video
                                </a>
                            `;
                        }
                        Swal.fire({
                            title: 'Video Upload Completed!',
                            text: `Video for "${articleTitle || article.title || 'Article'}" uploaded successfully.`,
                            icon: 'success',
                            toast: true,
                            position: 'top-end',
                            showConfirmButton: false,
                            timer: 4500,
                            timerProgressBar: true,
                            background: '#161e2e',
                            color: '#ffffff'
                        });
                        return;
                    }
                } catch (err) {
                    console.error('JSON parse error:', err);
                }
            }

            if (videoCell) {
                videoCell.innerHTML = `<span class="video-upload-badge failed">Upload Failed</span>`;
            }
            Swal.fire({
                title: 'Video Upload Failed',
                text: 'Could not complete video upload. Please try again.',
                icon: 'error',
                background: '#161e2e',
                color: '#ffffff'
            });
        };

        xhr.onerror = function() {
            if (videoCell) {
                videoCell.innerHTML = `<span class="video-upload-badge failed">Network Error</span>`;
            }
            Swal.fire({
                title: 'Upload Error',
                text: 'Network error occurred while uploading video.',
                icon: 'error',
                background: '#161e2e',
                color: '#ffffff'
            });
        };

        xhr.send(formData);
    }

    function showArticleSubmitLoader(submitBtnEl, loadingText) {
        if (submitBtnEl) {
            submitBtnEl.disabled = true;
            submitBtnEl.style.opacity = '0.75';
            submitBtnEl.style.cursor = 'not-allowed';
            submitBtnEl.innerHTML = `<span class="btn-spinner"></span> ${loadingText}`;
        }
        const overlay = document.getElementById('articleLoadingOverlay');
        const titleEl = document.getElementById('loadingOverlayTitle');
        if (titleEl) titleEl.innerText = loadingText;
        if (overlay) overlay.classList.add('show');
    }

    function resetArticleSubmitBtn(submitBtnEl, originalText) {
        if (submitBtnEl) {
            submitBtnEl.disabled = false;
            submitBtnEl.style.opacity = '1';
            submitBtnEl.style.cursor = 'pointer';
            submitBtnEl.innerHTML = originalText;
        }
        const overlay = document.getElementById('articleLoadingOverlay');
        if (overlay) overlay.classList.remove('show');
    }

    document.addEventListener('DOMContentLoaded', function() {
        const createForm = document.getElementById('createArticleForm');
        if (createForm) {
            createForm.addEventListener('submit', function(e) {
                if (createDescriptionEditor) {
                    createDescriptionEditor.updateSourceElement();
                }

                if (!validateFormFileSizes(createForm)) {
                    e.preventDefault();
                    return false;
                }

                const videoInput = createForm.querySelector('input[name="video"]');
                const hasVideoFile = videoInput && videoInput.files && videoInput.files.length > 0;

                if (hasVideoFile) {
                    e.preventDefault();
                    const videoFile = videoInput.files[0];
                    const btn = document.getElementById('createSubmitBtn');
                    showArticleSubmitLoader(btn, 'Saving Article...');

                    // Copy form data except video file for fast initial save
                    const formData = new FormData(createForm);
                    formData.delete('video');

                    fetch(createForm.action, {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        }
                    })
                    .then(async res => {
                        const data = await res.json().catch(() => ({}));
                        if (!res.ok || !data.status) {
                            let errMsg = data.message;
                            if (data.errors) {
                                errMsg = Object.values(data.errors).flat().join('\n');
                            }
                            throw new Error(errMsg || 'Error saving article');
                        }
                        return data;
                    })
                    .then(data => {
                        resetArticleSubmitBtn(btn, 'Save Article');
                        closeModal('createModal');

                        if (data.status && data.article) {
                            const newArticle = data.article;
                            createForm.reset();
                            if (createDescriptionEditor) {
                                createDescriptionEditor.setData('');
                            }

                            // Dynamic row prepending
                            const tbody = document.getElementById('articlesTableBody');
                            if (tbody) {
                                const tr = document.createElement('tr');
                                tr.id = 'article-row-' + newArticle.id;
                                const categoryName = newArticle.category ? newArticle.category.category_name : 'Uncategorized';
                                const articleJson = JSON.stringify(newArticle).replace(/"/g, '&quot;');
                                const docPath = newArticle.document ? (newArticle.document.startsWith('uploads/') ? newArticle.document : 'uploads/articles/documents/' + newArticle.document) : '';
                                
                                tr.innerHTML = `
                                    <td><strong style="color: #ffffff;">#${newArticle.id}</strong></td>
                                    <td><span class="category-badge">${categoryName}</span></td>
                                    <td><div class="article-title">${newArticle.title}</div></td>
                                    <td><span style="color: var(--text-muted); font-size: 0.775rem;">no image found</span></td>
                                    <td>
                                        ${docPath ? `
                                        <div style="display: flex; gap: 0.35rem; align-items: center;">
                                            <a href="${assetBaseUrl + docPath}" target="_blank" class="file-btn btn-view" title="View PDF">View</a>
                                            <a href="${assetBaseUrl + docPath}" download class="file-btn btn-download" title="Download PDF">Download</a>
                                        </div>` : '<span style="color: var(--text-muted); font-size: 0.775rem;">no document found</span>'}
                                    </td>
                                    <td id="video-cell-${newArticle.id}">
                                        <span class="video-upload-badge" id="video-badge-${newArticle.id}"><span class="btn-spinner"></span> Starting upload...</span>
                                    </td>
                                    <td>Just now</td>
                                    <td>
                                        <div class="action-dropdown">
                                            <button type="button" class="btn-more" onclick="toggleDropdown(event, 'drop-article-${newArticle.id}')">⋮</button>
                                            <div id="drop-article-${newArticle.id}" class="dropdown-menu">
                                                <a href="javascript:void(0)" onclick="openViewModal(${articleJson})" class="dropdown-item item-view">View Details</a>
                                                <a href="javascript:void(0)" onclick="openEditModal(${articleJson})" class="dropdown-item item-edit">Edit Article</a>
                                            </div>
                                        </div>
                                    </td>
                                `;
                                tbody.prepend(tr);
                            }

                            Swal.fire({
                                title: 'Article Created!',
                                text: 'Article details saved. Video is uploading in background...',
                                icon: 'success',
                                toast: true,
                                position: 'top-end',
                                showConfirmButton: false,
                                timer: 4000,
                                background: '#161e2e',
                                color: '#ffffff'
                            });

                            // Trigger background video upload
                            uploadVideoInBackground(newArticle.id, videoFile, newArticle.title);
                        }
                    })
                    .catch(err => {
                        resetArticleSubmitBtn(btn, 'Save Article');
                        console.error('Create article error:', err);
                        Swal.fire({
                            title: 'Validation Error',
                            text: err.message || 'Failed to save article.',
                            icon: 'error',
                            background: '#161e2e',
                            color: '#ffffff'
                        });
                    });
                } else {
                    const btn = document.getElementById('createSubmitBtn');
                    showArticleSubmitLoader(btn, 'Saving Article...');
                }
            });
        }

        const editForm = document.getElementById('editForm');
        if (editForm) {
            editForm.addEventListener('submit', function(e) {
                if (editDescriptionEditor) {
                    editDescriptionEditor.updateSourceElement();
                }

                if (!validateFormFileSizes(editForm)) {
                    e.preventDefault();
                    return false;
                }

                const videoInput = editForm.querySelector('input[name="video"]');
                const hasVideoFile = videoInput && videoInput.files && videoInput.files.length > 0;

                if (hasVideoFile) {
                    e.preventDefault();
                    const videoFile = videoInput.files[0];
                    const btn = document.getElementById('editSubmitBtn');
                    showArticleSubmitLoader(btn, 'Updating Article...');

                    const formData = new FormData(editForm);
                    formData.delete('video');

                    fetch(editForm.action, {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        }
                    })
                    .then(async res => {
                        const data = await res.json().catch(() => ({}));
                        if (!res.ok || !data.status) {
                            let errMsg = data.message;
                            if (data.errors) {
                                errMsg = Object.values(data.errors).flat().join('\n');
                            }
                            throw new Error(errMsg || 'Error updating article');
                        }
                        return data;
                    })
                    .then(data => {
                        resetArticleSubmitBtn(btn, 'Update Article');
                        closeModal('editModal');

                        if (data.status && data.article) {
                            const updatedArticle = data.article;
                            const videoCell = document.getElementById('video-cell-' + updatedArticle.id);
                            if (videoCell) {
                                videoCell.innerHTML = `<span class="video-upload-badge" id="video-badge-${updatedArticle.id}"><span class="btn-spinner"></span> Starting upload...</span>`;
                            }

                            Swal.fire({
                                title: 'Article Updated!',
                                text: 'Article details updated. Video is uploading in background...',
                                icon: 'success',
                                toast: true,
                                position: 'top-end',
                                showConfirmButton: false,
                                timer: 4000,
                                background: '#161e2e',
                                color: '#ffffff'
                            });

                            // Trigger background video upload
                            uploadVideoInBackground(updatedArticle.id, videoFile, updatedArticle.title);
                        }
                    })
                    .catch(err => {
                        resetArticleSubmitBtn(btn, 'Update Article');
                        console.error('Update article error:', err);
                        Swal.fire({
                            title: 'Validation Error',
                            text: err.message || 'Failed to update article.',
                            icon: 'error',
                            background: '#161e2e',
                            color: '#ffffff'
                        });
                    });
                } else {
                    const btn = document.getElementById('editSubmitBtn');
                    showArticleSubmitLoader(btn, 'Updating Article...');
                }
            });
        }
    });
</script>
@endsection
