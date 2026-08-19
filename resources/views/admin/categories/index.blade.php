@extends('layouts.admin')

@section('title', 'Category Management')

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

    .category-name-text {
        font-weight: 700;
        color: #ffffff;
        font-size: 0.875rem;
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
        max-width: 480px;
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
        <h1 class="page-title">Category Management</h1>
        <p class="page-subtitle">View, create, search, and manage all clinical therapy categories.</p>
    </div>

    <form action="{{ route('admin.categories.index') }}" method="GET" class="toolbar-inline">
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
            <input type="text" name="search" value="{{ $search }}" placeholder="Search category name...">
            <button type="submit" class="btn-search">Search</button>
        </div>

        @if($search)
            <a href="{{ route('admin.categories.index') }}" class="clear-link">Clear</a>
        @endif

        <button type="button" class="btn-action btn-add" onclick="openCreateModal()">
            <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
            </svg>
            Add Category
        </button>
    </form>
</div>

<div class="content-card">
    <div class="table-responsive">
        <table class="compact-table">
            <thead>
                <tr>
                    @php
                        function categorySortUrl($field, $currentSortBy, $currentSortOrder) {
                            $order = ($currentSortBy === $field && $currentSortOrder === 'asc') ? 'desc' : 'asc';
                            return route('admin.categories.index', array_merge(request()->query(), ['sort_by' => $field, 'sort_order' => $order]));
                        }
                    @endphp
                    <th>
                        <a href="{{ categorySortUrl('id', $sortBy, $sortOrder) }}" class="sort-link {{ $sortBy === 'id' ? 'active' : '' }}">
                            ID
                            @if($sortBy === 'id')
                                <span>{{ $sortOrder === 'asc' ? '↑' : '↓' }}</span>
                            @endif
                        </a>
                    </th>
                    <th>
                        <a href="{{ categorySortUrl('category_name', $sortBy, $sortOrder) }}" class="sort-link {{ $sortBy === 'category_name' ? 'active' : '' }}">
                            Category Name
                            @if($sortBy === 'category_name')
                                <span>{{ $sortOrder === 'asc' ? '↑' : '↓' }}</span>
                            @endif
                        </a>
                    </th>
                    <th>
                        <a href="{{ categorySortUrl('created_at', $sortBy, $sortOrder) }}" class="sort-link {{ $sortBy === 'created_at' ? 'active' : '' }}">
                            Created Date
                            @if($sortBy === 'created_at')
                                <span>{{ $sortOrder === 'asc' ? '↑' : '↓' }}</span>
                            @endif
                        </a>
                    </th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($categories as $category)
                    <tr>
                        <td><strong style="color: #ffffff;">#{{ $category->id }}</strong></td>
                        <td>
                            <div class="category-name-text">{{ $category->category_name }}</div>
                        </td>
                        <td>{{ $category->created_at ? $category->created_at->format('M d, Y') : 'N/A' }}</td>
                        <td>
                            <!-- Action Dropdown with More Icon (⋮) -->
                            <div class="action-dropdown">
                                <button type="button" class="btn-more" onclick="toggleDropdown(event, 'drop-cat-{{ $category->id }}')">
                                    <svg width="16" height="16" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M12 8c1.1 0 2-.9 2-2s-.9-2-2-2-2 .9-2 2 .9 2 2 2zm0 2c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2zm0 6c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2z"/>
                                    </svg>
                                </button>
                                <div id="drop-cat-{{ $category->id }}" class="dropdown-menu">
                                    <a href="javascript:void(0)" onclick="openEditModal({{ json_encode($category) }})" class="dropdown-item item-edit">
                                        <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                        Edit Category
                                    </a>
                                    <form id="delete-cat-{{ $category->id }}" action="{{ route('admin.categories.destroy', $category->id) }}" method="POST" style="margin:0;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" class="dropdown-item item-delete" onclick="confirmAction({ title: 'Delete Category?', text: 'Are you sure you want to delete this category?', icon: 'warning', confirmText: 'Yes, Delete', confirmClass: 'swal2-confirm btn-danger', formId: 'delete-cat-{{ $category->id }}' })" style="width:100%; border:none; background:transparent; cursor:pointer;">
                                            <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            Delete Category
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" style="text-align: center; color: var(--text-muted); padding: 1.5rem;">
                            @if($search)
                                No categories found matching "{{ $search }}".
                            @else
                                No categories found. Click "Add Category" to create one.
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
                Showing {{ $categories->firstItem() ?? 0 }} to {{ $categories->lastItem() ?? 0 }} of {{ $categories->total() }} categories
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
            {{ $categories->links() }}
        </div>
    </div>
</div>

<!-- Create Category Modal -->
<div class="modal-backdrop" id="createModal">
    <div class="modal-card">
        <div class="modal-header">
            <h2>Add New Category</h2>
            <button class="btn-close" onclick="closeModal('createModal')">&times;</button>
        </div>
        <form action="{{ route('admin.categories.store') }}" method="POST">
            @csrf
            <div class="form-group">
                <label class="form-label">Category Name</label>
                <input type="text" name="category_name" class="form-control" placeholder="e.g. Mechanical Ventilation" required>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-secondary" onclick="closeModal('createModal')">Cancel</button>
                <button type="submit" class="btn-action btn-add" style="padding: 0.6rem 1.2rem;">Save Category</button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Category Modal -->
<div class="modal-backdrop" id="editModal">
    <div class="modal-card">
        <div class="modal-header">
            <h2>Edit Category</h2>
            <button class="btn-close" onclick="closeModal('editModal')">&times;</button>
        </div>
        <form id="editForm" method="POST">
            @csrf
            @method('PUT')
            <div class="form-group">
                <label class="form-label">Category Name</label>
                <input type="text" name="category_name" id="editCategoryName" class="form-control" required>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-secondary" onclick="closeModal('editModal')">Cancel</button>
                <button type="submit" class="btn-action btn-edit" style="padding: 0.6rem 1.2rem;">Update Category</button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function openCreateModal() {
        document.getElementById('createModal').classList.add('show');
    }

    function openEditModal(category) {
        document.getElementById('editForm').action = "{{ url('admin/categories') }}/" + category.id;
        document.getElementById('editCategoryName').value = category.category_name;
        document.getElementById('editModal').classList.add('show');
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
</script>
@endsection
