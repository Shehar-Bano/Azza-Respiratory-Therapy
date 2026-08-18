@extends('layouts.admin')

@section('title', 'Article Management')

@section('styles')
<style>
    .page-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 1.5rem;
    }

    .article-title {
        font-weight: 700;
        color: #ffffff;
        font-size: 0.875rem;
    }

    .article-desc {
        color: var(--text-secondary);
        font-size: 0.8rem;
        max-width: 280px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .file-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        padding: 0.25rem 0.5rem;
        background: rgba(255, 255, 255, 0.04);
        border: 1px solid var(--card-border);
        border-radius: 6px;
        font-size: 0.75rem;
        color: #a5b4fc;
        text-decoration: none;
    }

    .file-badge:hover {
        background: rgba(255, 255, 255, 0.08);
    }

    .action-btns {
        display: flex;
        align-items: center;
        gap: 0.4rem;
    }

    .btn-action {
        padding: 0.35rem 0.65rem;
        border-radius: 6px;
        font-size: 0.775rem;
        font-weight: 600;
        border: none;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 0.3rem;
        transition: all 0.2s ease;
        text-decoration: none;
    }

    .btn-edit {
        background: rgba(59, 130, 246, 0.12);
        color: #60a5fa;
        border: 1px solid rgba(59, 130, 246, 0.25);
    }

    .btn-edit:hover {
        background: rgba(59, 130, 246, 0.25);
        color: #ffffff;
    }

    .btn-delete {
        background: rgba(239, 68, 68, 0.12);
        color: #fca5a5;
        border: 1px solid rgba(239, 68, 68, 0.25);
    }

    .btn-delete:hover {
        background: rgba(239, 68, 68, 0.25);
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
        max-width: 540px;
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
</style>
@endsection

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Article Management</h1>
        <p class="page-subtitle">Manage clinical articles, ABG guides, image assets, and documentation manuals.</p>
    </div>
    <button class="btn-primary" onclick="openCreateModal()">
        <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
        </svg>
        <span>Add New Article</span>
    </button>
</div>

<div class="content-card">
    <div class="table-responsive">
        <table class="compact-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Title & Description</th>
                    <th>Image</th>
                    <th>Document</th>
                    <th>Created At</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($articles as $article)
                    <tr>
                        <td><strong style="color: #ffffff;">#{{ $article->id }}</strong></td>
                        <td>
                            <div class="article-title">{{ $article->title }}</div>
                            <div class="article-desc" title="{{ $article->description }}">{{ $article->description }}</div>
                        </td>
                        <td>
                            @if($article->image)
                                <a href="{{ asset('uploads/articles/images/' . $article->image) }}" target="_blank" class="file-badge">
                                    <svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                    {{ Str::limit($article->image, 16) }}
                                </a>
                            @else
                                <span style="color: var(--text-muted);">None</span>
                            @endif
                        </td>
                        <td>
                            @if($article->document)
                                <a href="{{ asset('uploads/articles/documents/' . $article->document) }}" target="_blank" class="file-badge">
                                    <svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                    {{ Str::limit($article->document, 16) }}
                                </a>
                            @else
                                <span style="color: var(--text-muted);">None</span>
                            @endif
                        </td>
                        <td>{{ $article->created_at ? $article->created_at->format('M d, Y') : 'N/A' }}</td>
                        <td>
                            <div class="action-btns">
                                <button class="btn-action btn-edit" onclick="openEditModal({{ json_encode($article) }})">
                                    <svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                    Edit
                                </button>
                                
                                <form action="{{ route('admin.articles.destroy', $article->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this article?');" style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-action btn-delete">
                                        <svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        Delete
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" style="text-align: center; color: var(--text-muted); padding: 1.5rem;">No articles found. Click "Add New Article" to create one.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Create Article Modal -->
<div class="modal-backdrop" id="createModal">
    <div class="modal-card">
        <div class="modal-header">
            <h2>Add New Article</h2>
            <button class="btn-close" onclick="closeModal('createModal')">&times;</button>
        </div>
        <form action="{{ route('admin.articles.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="form-group">
                <label class="form-label">Article Title</label>
                <input type="text" name="title" class="form-control" placeholder="e.g. Arterial Blood Gas (ABG) Analysis" required>
            </div>
            <div class="form-group">
                <label class="form-label">Description</label>
                <textarea name="description" class="form-control" placeholder="Enter article content description..." required></textarea>
            </div>
            <div class="form-group">
                <label class="form-label">Image File (Optional)</label>
                <input type="file" name="image" class="form-control" accept="image/*">
            </div>
            <div class="form-group">
                <label class="form-label">Document Manual File (Optional)</label>
                <input type="file" name="document" class="form-control" accept=".pdf,.doc,.docx,.txt">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-secondary" onclick="closeModal('createModal')">Cancel</button>
                <button type="submit" class="btn-primary">Save Article</button>
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
                <label class="form-label">Article Title</label>
                <input type="text" name="title" id="editTitle" class="form-control" required>
            </div>
            <div class="form-group">
                <label class="form-label">Description</label>
                <textarea name="description" id="editDescription" class="form-control" required></textarea>
            </div>
            <div class="form-group">
                <label class="form-label">Replace Image File (Optional)</label>
                <input type="file" name="image" class="form-control" accept="image/*">
                <small style="color: var(--text-muted); font-size: 0.75rem;">Current: <span id="currentImageName"></span></small>
            </div>
            <div class="form-group">
                <label class="form-label">Replace Document File (Optional)</label>
                <input type="file" name="document" class="form-control" accept=".pdf,.doc,.docx,.txt">
                <small style="color: var(--text-muted); font-size: 0.75rem;">Current: <span id="currentDocumentName"></span></small>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-secondary" onclick="closeModal('editModal')">Cancel</button>
                <button type="submit" class="btn-primary">Update Article</button>
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

    function openEditModal(article) {
        document.getElementById('editForm').action = '/admin/articles/' + article.id;
        document.getElementById('editTitle').value = article.title;
        document.getElementById('editDescription').value = article.description;
        document.getElementById('currentImageName').innerText = article.image ? article.image : 'None';
        document.getElementById('currentDocumentName').innerText = article.document ? article.document : 'None';
        document.getElementById('editModal').classList.add('show');
    }

    function closeModal(modalId) {
        document.getElementById(modalId).classList.remove('show');
    }
</script>
@endsection
