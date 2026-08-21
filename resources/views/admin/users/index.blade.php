@extends('layouts.admin')

@section('title', 'User Management')

@section('styles')
<style>
    .page-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 1.5rem;
        flex-wrap: wrap;
        gap: 1rem;
    }

    .search-box {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        background: var(--card-bg);
        border: 1px solid var(--card-border);
        border-radius: 10px;
        padding: 0.4rem 0.75rem;
        width: 100%;
        max-width: 340px;
    }

    .search-box input {
        background: transparent;
        border: none;
        outline: none;
        color: #ffffff;
        font-size: 0.875rem;
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
    }

    .dropdown-item:hover {
        background: rgba(255, 255, 255, 0.08);
    }

    .dropdown-item.item-view:hover { color: #34d399; }
    .dropdown-item.item-suspend:hover { color: #fbbf24; }
    .dropdown-item.item-activate:hover { color: #34d399; }

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

    .user-detail-header {
        display: flex;
        align-items: center;
        gap: 1rem;
        margin-bottom: 1.25rem;
        padding-bottom: 1rem;
        border-bottom: 1px solid var(--card-border);
    }

    .user-avatar-large {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        background: linear-gradient(135deg, #6366f1, #8b5cf6);
        color: #ffffff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
        font-weight: 800;
    }

    .detail-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 0.6rem 0;
        border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        font-size: 0.85rem;
    }

    .detail-label {
        color: var(--text-muted);
        font-weight: 600;
    }

    .detail-value {
        color: #ffffff;
        font-weight: 600;
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
<div class="page-header">
    <div>
        <h1 class="page-title">User Management</h1>
        <p class="page-subtitle">View, search, sort, and manage account statuses for registered users.</p>
    </div>

    <div style="display: flex; align-items: center; gap: 0.75rem; flex-wrap: wrap;">
        <!-- Global Search Form -->
        <form action="{{ route('admin.users.index') }}" method="GET">
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
                <input type="text" name="search" value="{{ $search }}" placeholder="Search by name, email, phone, role...">
                <button type="submit" class="btn-search">Search</button>
                @if($search)
                    <a href="{{ route('admin.users.index') }}" style="color: var(--text-muted); font-size: 0.75rem; text-decoration: none;">Clear</a>
                @endif
            </div>
        </form>

        <button class="btn-action btn-add" onclick="openCreateUserModal()" style="padding: 0.5rem 0.95rem; font-size: 0.85rem;">
            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Add User
        </button>
    </div>
</div>

<!-- Compact Data Table with Sorting -->
<div class="content-card">
    <div class="card-header" style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 0.5rem;">
        <h2 class="card-title">User List (Total: {{ $users->total() }})</h2>
        <button class="btn-action btn-add" onclick="openCreateUserModal()" style="padding: 0.35rem 0.75rem; font-size: 0.8rem;">
            + Add User
        </button>
    </div>
    <div class="table-responsive">
        <table class="compact-table">
            <thead>
                <tr>
                    @php
                        function userSortUrl($field, $currentSortBy, $currentSortOrder) {
                            $order = ($currentSortBy === $field && $currentSortOrder === 'asc') ? 'desc' : 'asc';
                            return route('admin.users.index', array_merge(request()->query(), ['sort_by' => $field, 'sort_order' => $order]));
                        }
                    @endphp
                    <th>
                        <a href="{{ userSortUrl('id', $sortBy, $sortOrder) }}" class="sort-link {{ $sortBy === 'id' ? 'active' : '' }}">
                            ID
                            @if($sortBy === 'id')
                                <span>{{ $sortOrder === 'asc' ? '↑' : '↓' }}</span>
                            @endif
                        </a>
                    </th>
                    <th>
                        <a href="{{ userSortUrl('name', $sortBy, $sortOrder) }}" class="sort-link {{ $sortBy === 'name' ? 'active' : '' }}">
                            Name
                            @if($sortBy === 'name')
                                <span>{{ $sortOrder === 'asc' ? '↑' : '↓' }}</span>
                            @endif
                        </a>
                    </th>
                    <th>
                        <a href="{{ userSortUrl('email', $sortBy, $sortOrder) }}" class="sort-link {{ $sortBy === 'email' ? 'active' : '' }}">
                            Email
                            @if($sortBy === 'email')
                                <span>{{ $sortOrder === 'asc' ? '↑' : '↓' }}</span>
                            @endif
                        </a>
                    </th>
                    <th>
                        <a href="{{ userSortUrl('phone', $sortBy, $sortOrder) }}" class="sort-link {{ $sortBy === 'phone' ? 'active' : '' }}">
                            Phone
                            @if($sortBy === 'phone')
                                <span>{{ $sortOrder === 'asc' ? '↑' : '↓' }}</span>
                            @endif
                        </a>
                    </th>
                    <th>
                        <a href="{{ userSortUrl('role', $sortBy, $sortOrder) }}" class="sort-link {{ $sortBy === 'role' ? 'active' : '' }}">
                            Role
                            @if($sortBy === 'role')
                                <span>{{ $sortOrder === 'asc' ? '↑' : '↓' }}</span>
                            @endif
                        </a>
                    </th>
                    <th>
                        <a href="{{ userSortUrl('status', $sortBy, $sortOrder) }}" class="sort-link {{ $sortBy === 'status' ? 'active' : '' }}">
                            Status
                            @if($sortBy === 'status')
                                <span>{{ $sortOrder === 'asc' ? '↑' : '↓' }}</span>
                            @endif
                        </a>
                    </th>
                    <th>
                        <a href="{{ userSortUrl('created_at', $sortBy, $sortOrder) }}" class="sort-link {{ $sortBy === 'created_at' ? 'active' : '' }}">
                            Registered Date
                            @if($sortBy === 'created_at')
                                <span>{{ $sortOrder === 'asc' ? '↑' : '↓' }}</span>
                            @endif
                        </a>
                    </th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                    <tr>
                        <td><strong style="color: #ffffff;">#{{ $user->id }}</strong></td>
                        <td>
                            <div style="display: flex; align-items: center; gap: 0.65rem;">
                                <div class="avatar" style="width: 26px; height: 26px; font-size: 0.75rem;">
                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                </div>
                                <span style="font-weight: 600; color: #ffffff;">{{ $user->name }}</span>
                            </div>
                        </td>
                        <td>{{ $user->email }}</td>
                        <td>{{ $user->phone ?? 'N/A' }}</td>
                        <td>
                            <span class="badge {{ $user->role === 'admin' ? 'badge-admin' : 'badge-user' }}">
                                {{ $user->role }}
                            </span>
                        </td>
                        <td>
                            <span class="badge {{ $user->status === 'active' ? 'badge-active' : ($user->status === 'suspended' ? 'badge-suspended' : 'badge-inactive') }}">
                                {{ $user->status }}
                            </span>
                        </td>
                        <td>{{ $user->created_at ? $user->created_at->format('M d, Y H:i') : 'N/A' }}</td>
                        <td>
                            <!-- Action Dropdown with More Icon (⋮) -->
                            <div class="action-dropdown">
                                <button type="button" class="btn-more" onclick="toggleDropdown(event, 'drop-user-{{ $user->id }}')">
                                    <svg width="16" height="16" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M12 8c1.1 0 2-.9 2-2s-.9-2-2-2-2 .9-2 2 .9 2 2 2zm0 2c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2zm0 6c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2z"/>
                                    </svg>
                                </button>
                                <div id="drop-user-{{ $user->id }}" class="dropdown-menu">
                                    <a href="javascript:void(0)" onclick="openUserModal({{ json_encode($user) }})" class="dropdown-item item-view">
                                        <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                        View Detail
                                    </a>

                                    <a href="javascript:void(0)" onclick="openUpdateUserSubModal({{ json_encode($user) }})" class="dropdown-item item-activate">
                                        <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                        Update Subscription
                                    </a>

                                    @if($user->status === 'active')
                                        <form id="suspend-form-{{ $user->id }}" action="{{ route('admin.users.updateStatus', $user->id) }}" method="POST" style="margin:0;">
                                            @csrf
                                            @method('PUT')
                                            <input type="hidden" name="status" value="suspended">
                                            <button type="button" class="dropdown-item item-suspend" onclick="confirmAction({ title: 'Suspend User?', text: 'Are you sure you want to suspend this user? They will not be able to log in.', icon: 'warning', confirmText: 'Yes, Suspend', confirmClass: 'swal2-confirm btn-warning', formId: 'suspend-form-{{ $user->id }}' })" style="width:100%; border:none; background:transparent; cursor:pointer;">
                                                <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                                Suspend User
                                            </button>
                                        </form>
                                    @else
                                        <form id="activate-form-{{ $user->id }}" action="{{ route('admin.users.updateStatus', $user->id) }}" method="POST" style="margin:0;">
                                            @csrf
                                            @method('PUT')
                                            <input type="hidden" name="status" value="active">
                                            <button type="button" class="dropdown-item item-activate" onclick="confirmAction({ title: 'Activate User?', text: 'Are you sure you want to activate this user?', icon: 'question', confirmText: 'Yes, Activate', confirmClass: 'swal2-confirm', formId: 'activate-form-{{ $user->id }}' })" style="width:100%; border:none; background:transparent; cursor:pointer;">
                                                <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 11V7a4 4 0 118 0m-4 8v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2z"/></svg>
                                                Activate User
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" style="text-align: center; color: var(--text-muted); padding: 2rem;">
                            No users found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="pagination-wrapper">
        <div style="display: flex; align-items: center; gap: 1rem;">
            <span style="color: var(--text-muted); font-size: 0.8rem;">
                Showing {{ $users->firstItem() ?? 0 }} to {{ $users->lastItem() ?? 0 }} of {{ $users->total() }} users
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
            {{ $users->links() }}
        </div>
    </div>
</div>

<!-- View User Detail Modal -->
<div class="modal-backdrop" id="viewUserModal">
    <div class="modal-card">
        <div class="modal-header">
            <h2>User Details</h2>
            <button class="btn-close" onclick="closeUserModal()">&times;</button>
        </div>
        <div>
            <div class="user-detail-header">
                <div class="user-avatar-large" id="modalUserAvatar">U</div>
                <div>
                    <h3 id="modalUserName" style="font-size: 1.1rem; font-weight: 800; color: #ffffff; margin: 0;"></h3>
                    <span id="modalUserEmail" style="color: var(--text-muted); font-size: 0.8rem;"></span>
                </div>
            </div>

            <div class="detail-row">
                <span class="detail-label">User ID</span>
                <span class="detail-value" id="modalUserId"></span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Phone Number</span>
                <span class="detail-value" id="modalUserPhone">N/A</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Account Role</span>
                <span id="modalUserRoleBadge" class="badge"></span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Account Status</span>
                <span id="modalUserStatusBadge" class="badge"></span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Registered Date</span>
                <span class="detail-value" id="modalUserCreatedAt"></span>
            </div>

            <!-- Active Subscription Section -->
            <div style="margin-top: 1rem; padding-top: 0.85rem; border-top: 1px solid var(--card-border);">
                <div style="font-size: 0.825rem; font-weight: 700; color: #a5b4fc; margin-bottom: 0.5rem;">User Subscription Status</div>
                <div>
                    <div class="detail-row">
                        <span class="detail-label">Active Plan</span>
                        <span class="detail-value" id="modalSubPlan">No Active Subscription</span>
                    </div>
                    <div class="detail-row" id="subRefRow" style="display: none;">
                        <span class="detail-label">Transaction Ref</span>
                        <span class="detail-value" id="modalSubRef"></span>
                    </div>
                    <div class="detail-row" id="subExpiresRow" style="display: none;">
                        <span class="detail-label">Expires At</span>
                        <span class="detail-value" id="modalSubExpires"></span>
                    </div>
                    <div class="detail-row" id="subStatusRow" style="display: none;">
                        <span class="detail-label">Subscription Status</span>
                        <span id="modalSubStatusBadge" class="badge"></span>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn-secondary" onclick="closeUserModal()">Close</button>
        </div>
    </div>
</div>

<!-- Create User Modal -->
<div class="modal-backdrop" id="createUserModal">
    <div class="modal-card" style="max-width: 540px;">
        <div class="modal-header">
            <h2>Add New User</h2>
            <button class="btn-close" onclick="closeCreateUserModal()">&times;</button>
        </div>
        <form action="{{ route('admin.users.store') }}" method="POST">
            @csrf
            <div class="form-group">
                <label class="form-label">Full Name <span style="color:#ef4444;">*</span></label>
                <input type="text" name="name" class="form-control" placeholder="e.g. John Doe" required>
            </div>
            <div class="form-group">
                <label class="form-label">Email Address <span style="color:#ef4444;">*</span></label>
                <input type="email" name="email" class="form-control" placeholder="e.g. john@example.com" required>
            </div>
            <div class="form-group">
                <label class="form-label">Phone Number</label>
                <input type="text" name="phone" class="form-control" placeholder="e.g. +966 50 123 4567">
            </div>
            <div class="form-group">
                <label class="form-label">Password <span style="color:#ef4444;">*</span></label>
                <input type="password" name="password" class="form-control" placeholder="Minimum 6 characters" minlength="6" required>
            </div>
            <div class="form-group">
                <label class="form-label">Account Role</label>
                <select name="role" class="form-control">
                    <option value="user" selected>User</option>
                    <option value="admin">Admin</option>
                </select>
            </div>

            <!-- Subscription Option -->
            <div style="background: rgba(255, 255, 255, 0.03); border: 1px solid var(--card-border); border-radius: 10px; padding: 1rem; margin-top: 0.75rem;">
                <label style="display: flex; align-items: center; gap: 0.6rem; cursor: pointer; color: #ffffff; font-weight: 600; font-size: 0.875rem; user-select: none;">
                    <input type="checkbox" name="allow_subscription" id="allowSubCheckbox" value="1" onchange="toggleSubscriptionFields(this.checked)" style="width: 17px; height: 17px; accent-color: #6366f1; cursor: pointer;">
                    <span>Allow Subscription (Grant Active Plan)</span>
                </label>

                <div id="subscriptionFieldsSection" style="display: none; margin-top: 1rem; padding-top: 1rem; border-top: 1px dashed var(--card-border); flex-direction: column; gap: 0.85rem;">
                    <div class="form-group" style="margin-bottom: 0;">
                        <label class="form-label">Select Subscription Plan <span style="color:#ef4444;">*</span></label>
                        <select name="plan_id" id="planSelect" class="form-control" onchange="onPlanSelectChange(this)">
                            <option value="">-- Choose a Plan --</option>
                            @foreach($plans as $plan)
                                <option value="{{ $plan->plan_id }}" data-days="{{ $plan->duration_days }}" data-price="{{ $plan->price_sar ?? $plan->price }}">
                                    {{ $plan->title }} ({{ $plan->duration_days }} Days - SAR {{ $plan->price_sar ?? $plan->price }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group" style="margin-bottom: 0;">
                        <label class="form-label">Amount (SAR)</label>
                        <input type="number" step="0.01" name="amount" id="createAmountInput" class="form-control" placeholder="e.g. 397.46">
                    </div>
                    <div class="form-group" style="margin-bottom: 0;">
                        <label class="form-label">Subscription Duration (Days)</label>
                        <input type="number" name="duration_days" id="durationDaysInput" class="form-control" placeholder="e.g. 30" min="1">
                        <small style="color: var(--text-muted); font-size: 0.75rem; margin-top: 0.25rem; display: block;">Leave blank to use default plan duration.</small>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn-secondary" onclick="closeCreateUserModal()">Cancel</button>
                <button type="submit" class="btn-action btn-add" style="padding: 0.6rem 1.2rem; background: var(--primary); color: #ffffff; border-color: var(--primary);">Save User</button>
            </div>
        </form>
    </div>
</div>

<!-- Update User Subscription Modal -->
<div class="modal-backdrop" id="updateUserSubModal">
    <div class="modal-card" style="max-width: 540px;">
        <div class="modal-header">
            <h2>Update Subscription for <span id="subModalUserName" style="color: var(--primary);"></span></h2>
            <button class="btn-close" onclick="closeUpdateUserSubModal()">&times;</button>
        </div>
        <form id="updateUserSubForm" method="POST">
            @csrf
            <div style="display: flex; flex-direction: column; gap: 1rem;">
                <div class="form-group" style="margin-bottom: 0;">
                    <label class="form-label">Subscription Plan <span style="color:#ef4444;">*</span></label>
                    <select name="plan_id" id="editSubPlanSelect" class="form-control" required onchange="onEditPlanSelectChange(this)">
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
                    <label class="form-label">Duration (Days from Today)</label>
                    <input type="number" name="duration_days" id="editSubDurationInput" class="form-control" placeholder="e.g. 30" min="1">
                    <small style="color: var(--text-muted); font-size: 0.75rem; margin-top: 0.25rem; display: block;">Leave blank to use default plan duration.</small>
                </div>
                <div class="form-group" style="margin-bottom: 0;">
                    <label class="form-label">Subscription Status <span style="color:#ef4444;">*</span></label>
                    <select name="status" id="editSubStatusSelect" class="form-control" required>
                        <option value="active">Active</option>
                        <option value="suspended">Suspended</option>
                    </select>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn-secondary" onclick="closeUpdateUserSubModal()">Cancel</button>
                <button type="submit" class="btn-action btn-add" style="padding: 0.6rem 1.2rem; background: var(--primary); color: #ffffff; border-color: var(--primary);">Save Subscription</button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function openCreateUserModal() {
        document.getElementById('createUserModal').classList.add('show');
    }

    function closeCreateUserModal() {
        document.getElementById('createUserModal').classList.remove('show');
    }

    function toggleSubscriptionFields(isChecked) {
        var section = document.getElementById('subscriptionFieldsSection');
        var planSelect = document.getElementById('planSelect');
        if (isChecked) {
            section.style.display = 'flex';
            planSelect.setAttribute('required', 'required');
        } else {
            section.style.display = 'none';
            planSelect.removeAttribute('required');
        }
    }

    function onPlanSelectChange(selectElem) {
        var selectedOption = selectElem.options[selectElem.selectedIndex];
        var defaultDays = selectedOption.getAttribute('data-days');
        var defaultPrice = selectedOption.getAttribute('data-price');
        if (defaultDays) {
            document.getElementById('durationDaysInput').value = defaultDays;
        }
        if (defaultPrice) {
            document.getElementById('createAmountInput').value = defaultPrice;
        }
    }

    function openUpdateUserSubModal(user) {
        document.getElementById('subModalUserName').innerText = user.name;
        document.getElementById('updateUserSubForm').action = "{{ url('admin/users') }}/" + user.id + "/subscription";
        
        var sub = user.active_subscription;
        if (sub) {
            document.getElementById('editSubPlanSelect').value = sub.plan_id;
            document.getElementById('editSubAmountInput').value = sub.amount;
            document.getElementById('editSubStatusSelect').value = sub.status;
        } else {
            document.getElementById('editSubPlanSelect').value = '';
            document.getElementById('editSubAmountInput').value = '';
            document.getElementById('editSubStatusSelect').value = 'active';
        }
        document.getElementById('updateUserSubModal').classList.add('show');
    }

    function closeUpdateUserSubModal() {
        document.getElementById('updateUserSubModal').classList.remove('show');
    }

    function onEditPlanSelectChange(selectElem) {
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

    function openUserModal(user) {
        document.getElementById('modalUserAvatar').innerText = user.name ? user.name.charAt(0).toUpperCase() : 'U';
        document.getElementById('modalUserName').innerText = user.name;
        document.getElementById('modalUserEmail').innerText = user.email;
        document.getElementById('modalUserId').innerText = '#' + user.id;
        document.getElementById('modalUserPhone').innerText = user.phone ? user.phone : 'N/A';

        var roleBadge = document.getElementById('modalUserRoleBadge');
        roleBadge.innerText = user.role;
        roleBadge.className = 'badge ' + (user.role === 'admin' ? 'badge-admin' : 'badge-user');

        var statusBadge = document.getElementById('modalUserStatusBadge');
        statusBadge.innerText = user.status;
        statusBadge.className = 'badge ' + (user.status === 'active' ? 'badge-active' : (user.status === 'suspended' ? 'badge-suspended' : 'badge-inactive'));

        document.getElementById('modalUserCreatedAt').innerText = user.created_at ? new Date(user.created_at).toLocaleString() : 'N/A';

        // Subscription details
        if (user.active_subscription) {
            const sub = user.active_subscription;
            const planTitle = sub.plan ? sub.plan.title : ('Plan ' + sub.plan_id);
            document.getElementById('modalSubPlan').innerText = planTitle + ' (Plan ID: ' + sub.plan_id + ')';
            document.getElementById('modalSubRef').innerText = sub.transaction_reference || sub.cart_id || 'N/A';
            document.getElementById('modalSubExpires').innerText = sub.expires_at ? new Date(sub.expires_at).toLocaleDateString() : 'N/A';

            var subStatusBadge = document.getElementById('modalSubStatusBadge');
            subStatusBadge.innerText = sub.status;
            subStatusBadge.className = 'badge ' + (sub.status === 'active' ? 'badge-active' : 'badge-suspended');

            document.getElementById('subRefRow').style.display = 'flex';
            document.getElementById('subExpiresRow').style.display = 'flex';
            document.getElementById('subStatusRow').style.display = 'flex';
        } else {
            document.getElementById('modalSubPlan').innerText = 'Free Access (No Active Subscription)';
            document.getElementById('subRefRow').style.display = 'none';
            document.getElementById('subExpiresRow').style.display = 'none';
            document.getElementById('subStatusRow').style.display = 'none';
        }

        document.getElementById('viewUserModal').classList.add('show');
    }

    function closeUserModal() {
        document.getElementById('viewUserModal').classList.remove('show');
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
