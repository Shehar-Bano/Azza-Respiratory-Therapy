@extends('layouts.admin')

@section('title', 'FAQs Management')

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

    .btn-action {
        padding: 0.45rem 0.85rem;
        border-radius: 8px;
        font-size: 0.8rem;
        font-weight: 600;
        border: none;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
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

    .category-pill {
        display: inline-block;
        padding: 0.25rem 0.6rem;
        border-radius: 6px;
        background: rgba(99, 102, 241, 0.15);
        color: #818cf8;
        font-size: 0.75rem;
        font-weight: 600;
        white-space: nowrap;
    }

    .faq-question {
        font-weight: 700;
        color: var(--text-primary);
        font-size: 0.875rem;
        margin-bottom: 0.2rem;
    }

    .faq-answer {
        color: var(--text-muted);
        font-size: 0.8rem;
        line-height: 1.4;
        max-width: 520px;
    }

    .action-dropdown {
        position: relative;
        display: inline-block;
    }

    .btn-more {
        background: var(--input-bg);
        color: var(--text-secondary);
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
        color: var(--text-primary);
        text-decoration: none;
        transition: background 0.15s ease, color 0.15s ease;
    }

    .dropdown-item:hover {
        background: var(--input-bg);
    }

    .dropdown-item.item-edit:hover { color: #60a5fa; }
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

    .modal-footer {
        display: flex;
        justify-content: flex-end;
        gap: 0.65rem;
        margin-top: 1.5rem;
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

    .btn-close {
        background: transparent;
        border: none;
        color: var(--text-muted);
        font-size: 1.35rem;
        cursor: pointer;
        line-height: 1;
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
        <h1 class="page-title">FAQs Management</h1>
        <p class="page-subtitle">Add, edit, search, and manage questions &amp; answers returned by <code>GET /api/faqs</code>.</p>
    </div>

    <form action="{{ route('admin.faqs.index') }}" method="GET" class="toolbar-inline">
        @if(request('per_page'))
            <input type="hidden" name="per_page" value="{{ request('per_page') }}">
        @endif

        <select name="category" class="filter-select" onchange="this.form.submit()">
            <option value="">All Categories</option>
            @foreach($categories as $cat)
                <option value="{{ $cat }}" {{ $selectedCategory === $cat ? 'selected' : '' }}>{{ $cat }}</option>
            @endforeach
        </select>

        <div class="search-box">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
            <input type="text" name="search" value="{{ $search }}" placeholder="Search questions or answers...">
            <button type="submit" class="btn-search">Search</button>
        </div>

        @if($search || $selectedCategory)
            <a href="{{ route('admin.faqs.index') }}" class="clear-link">Clear</a>
        @endif

        <button type="button" class="btn-action btn-add" onclick="openCreateModal()">
            <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
            </svg>
            Add FAQ
        </button>
    </form>
</div>

<div class="content-card">
    <div class="table-responsive">
        <table class="compact-table">
            <thead>
                <tr>
                    <th>Sr No</th>
                    <th>Category</th>
                    <th>Question &amp; Answer</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($faqs as $faq)
                    <tr>
                        <td><strong style="color: var(--text-primary);">{{ ($faqs->currentPage() - 1) * $faqs->perPage() + $loop->iteration }}</strong></td>
                        <td><span class="category-pill">{{ $faq->category }}</span></td>
                        <td>
                            <div class="faq-question">{{ $faq->question }}</div>
                            <div class="faq-answer">{{ \Illuminate\Support\Str::limit($faq->answer, 140) }}</div>
                        </td>
                        <td>
                            <div class="action-dropdown">
                                <button type="button" class="btn-more" onclick="toggleDropdown(event, 'drop-faq-{{ $faq->id }}')">
                                    <svg width="16" height="16" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M12 8c1.1 0 2-.9 2-2s-.9-2-2-2-2 .9-2 2 .9 2 2 2zm0 2c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2zm0 6c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2z"/>
                                    </svg>
                                </button>
                                <div id="drop-faq-{{ $faq->id }}" class="dropdown-menu">
                                    <a href="javascript:void(0)" onclick='openEditModal(@json($faq))' class="dropdown-item item-edit">
                                        <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                        Edit FAQ
                                    </a>
                                    <form id="delete-faq-{{ $faq->id }}" action="{{ route('admin.faqs.destroy', $faq->id) }}" method="POST" style="margin:0;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" class="dropdown-item item-delete" onclick="confirmAction({ title: 'Delete FAQ?', text: 'Are you sure you want to delete this FAQ?', icon: 'warning', confirmText: 'Yes, Delete', confirmClass: 'swal2-confirm btn-danger', formId: 'delete-faq-{{ $faq->id }}' })" style="width:100%; border:none; background:transparent; cursor:pointer;">
                                            <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            Delete FAQ
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" style="text-align: center; color: var(--text-muted); padding: 1.5rem;">
                            No FAQs found. Click "Add FAQ" to create one.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="pagination-wrapper">
        <div style="display: flex; align-items: center; gap: 1rem;">
            <span style="color: var(--text-muted); font-size: 0.8rem;">
                Showing {{ $faqs->firstItem() ?? 0 }} to {{ $faqs->lastItem() ?? 0 }} of {{ $faqs->total() }} FAQs
            </span>
            <form action="{{ route('admin.faqs.index') }}" method="GET" style="margin:0;">
                @if($search)<input type="hidden" name="search" value="{{ $search }}">@endif
                @if($selectedCategory)<input type="hidden" name="category" value="{{ $selectedCategory }}">@endif
                <select name="per_page" class="per-page-select" onchange="this.form.submit()">
                    @foreach([10, 20, 30, 50, 100] as $size)
                        <option value="{{ $size }}" {{ $perPage == $size ? 'selected' : '' }}>{{ $size }} / page</option>
                    @endforeach
                </select>
            </form>
        </div>
        <div>
            {{ $faqs->links() }}
        </div>
    </div>
</div>

<!-- Create FAQ Modal -->
<div id="createFaqModal" class="modal-backdrop" style="display: none;">
    <div class="modal-card">
        <div class="modal-header">
            <h3 class="modal-title">Add New FAQ</h3>
            <button type="button" class="btn-close" onclick="closeCreateModal()">&times;</button>
        </div>
        <form action="{{ route('admin.faqs.store') }}" method="POST">
            @csrf
            <div class="form-group">
                <label class="form-label" for="create_category">Category</label>
                <select id="create_category" name="category" class="form-control" required>
                    <option value="">Select Category</option>
                    @foreach($dropdownCategories as $cat)
                        <option value="{{ $cat }}">{{ $cat }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label class="form-label" for="create_question">Question</label>
                <input type="text" id="create_question" name="question" class="form-control" placeholder="Enter question..." required>
            </div>
            <div class="form-group">
                <label class="form-label" for="create_answer">Answer</label>
                <textarea id="create_answer" name="answer" rows="4" class="form-control" placeholder="Enter answer..." required></textarea>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-secondary" onclick="closeCreateModal()">Cancel</button>
                <button type="submit" class="btn-search" style="padding: 0.55rem 1.15rem;">Save FAQ</button>
            </div>
        </form>
    </div>
</div>

<!-- Edit FAQ Modal -->
<div id="editFaqModal" class="modal-backdrop" style="display: none;">
    <div class="modal-card">
        <div class="modal-header">
            <h3 class="modal-title">Edit FAQ</h3>
            <button type="button" class="btn-close" onclick="closeEditModal()">&times;</button>
        </div>
        <form id="editFaqForm" method="POST">
            @csrf
            @method('PUT')
            <div class="form-group">
                <label class="form-label" for="edit_category">Category</label>
                <select id="edit_category" name="category" class="form-control" required>
                    <option value="">Select Category</option>
                    @foreach($dropdownCategories as $cat)
                        <option value="{{ $cat }}">{{ $cat }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label class="form-label" for="edit_question">Question</label>
                <input type="text" id="edit_question" name="question" class="form-control" required>
            </div>
            <div class="form-group">
                <label class="form-label" for="edit_answer">Answer</label>
                <textarea id="edit_answer" name="answer" rows="4" class="form-control" required></textarea>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-secondary" onclick="closeEditModal()">Cancel</button>
                <button type="submit" class="btn-search" style="padding: 0.55rem 1.15rem;">Update FAQ</button>
            </div>
        </form>
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

    function openCreateModal() {
        document.getElementById('createFaqModal').style.display = 'flex';
    }

    function closeCreateModal() {
        document.getElementById('createFaqModal').style.display = 'none';
    }

    function openEditModal(faq) {
        document.getElementById('editFaqForm').action = "{{ url('/admin/faqs') }}/" + faq.id;
        const select = document.getElementById('edit_category');
        if (faq.category && !Array.from(select.options).some(opt => opt.value === faq.category)) {
            const opt = new Option(faq.category, faq.category, true, true);
            select.add(opt);
        }
        select.value = faq.category;
        document.getElementById('edit_question').value = faq.question;
        document.getElementById('edit_answer').value = faq.answer;
        document.getElementById('editFaqModal').style.display = 'flex';
    }

    function closeEditModal() {
        document.getElementById('editFaqModal').style.display = 'none';
    }
</script>
@endsection
