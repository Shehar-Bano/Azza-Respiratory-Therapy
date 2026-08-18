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

    .pagination-wrapper {
        padding: 1rem 1.25rem;
        border-top: 1px solid var(--card-border);
        display: flex;
        align-items: center;
        justify-content: space-between;
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
        <p class="page-subtitle">View, search, and sort all registered users in the application.</p>
    </div>

    <!-- Global Search Form -->
    <form action="{{ route('admin.users.index') }}" method="GET">
        @if(request('sort_by'))
            <input type="hidden" name="sort_by" value="{{ request('sort_by') }}">
        @endif
        @if(request('sort_order'))
            <input type="hidden" name="sort_order" value="{{ request('sort_order') }}">
        @endif
        <div class="search-box">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
            <input type="text" name="search" value="{{ $search }}" placeholder="Search by name, email, role...">
            <button type="submit" class="btn-search">Search</button>
            @if($search)
                <a href="{{ route('admin.users.index') }}" style="color: var(--text-muted); font-size: 0.75rem; text-decoration: none;">Clear</a>
            @endif
        </div>
    </form>
</div>

<!-- Compact Data Table with Sorting -->
<div class="content-card">
    <div class="card-header">
        <h2 class="card-title">User List (Total: {{ $users->total() }})</h2>
    </div>
    <div class="table-responsive">
        <table class="compact-table">
            <thead>
                <tr>
                    @php
                        function sortUrl($field, $currentSortBy, $currentSortOrder) {
                            $order = ($currentSortBy === $field && $currentSortOrder === 'asc') ? 'desc' : 'asc';
                            return route('admin.users.index', array_merge(request()->query(), ['sort_by' => $field, 'sort_order' => $order]));
                        }
                    @endphp
                    <th>
                        <a href="{{ sortUrl('id', $sortBy, $sortOrder) }}" class="sort-link {{ $sortBy === 'id' ? 'active' : '' }}">
                            ID
                            @if($sortBy === 'id')
                                <span>{{ $sortOrder === 'asc' ? '↑' : '↓' }}</span>
                            @endif
                        </a>
                    </th>
                    <th>
                        <a href="{{ sortUrl('name', $sortBy, $sortOrder) }}" class="sort-link {{ $sortBy === 'name' ? 'active' : '' }}">
                            Name
                            @if($sortBy === 'name')
                                <span>{{ $sortOrder === 'asc' ? '↑' : '↓' }}</span>
                            @endif
                        </a>
                    </th>
                    <th>
                        <a href="{{ sortUrl('email', $sortBy, $sortOrder) }}" class="sort-link {{ $sortBy === 'email' ? 'active' : '' }}">
                            Email
                            @if($sortBy === 'email')
                                <span>{{ $sortOrder === 'asc' ? '↑' : '↓' }}</span>
                            @endif
                        </a>
                    </th>
                    <th>
                        <a href="{{ sortUrl('role', $sortBy, $sortOrder) }}" class="sort-link {{ $sortBy === 'role' ? 'active' : '' }}">
                            Role
                            @if($sortBy === 'role')
                                <span>{{ $sortOrder === 'asc' ? '↑' : '↓' }}</span>
                            @endif
                        </a>
                    </th>
                    <th>
                        <a href="{{ sortUrl('status', $sortBy, $sortOrder) }}" class="sort-link {{ $sortBy === 'status' ? 'active' : '' }}">
                            Status
                            @if($sortBy === 'status')
                                <span>{{ $sortOrder === 'asc' ? '↑' : '↓' }}</span>
                            @endif
                        </a>
                    </th>
                    <th>
                        <a href="{{ sortUrl('created_at', $sortBy, $sortOrder) }}" class="sort-link {{ $sortBy === 'created_at' ? 'active' : '' }}">
                            Registered Date
                            @if($sortBy === 'created_at')
                                <span>{{ $sortOrder === 'asc' ? '↑' : '↓' }}</span>
                            @endif
                        </a>
                    </th>
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
                        <td>
                            <span class="badge {{ $user->role === 'admin' ? 'badge-admin' : 'badge-user' }}">
                                {{ $user->role }}
                            </span>
                        </td>
                        <td>
                            <span class="badge {{ $user->status === 'active' ? 'badge-active' : 'badge-inactive' }}">
                                {{ $user->status }}
                            </span>
                        </td>
                        <td>{{ $user->created_at ? $user->created_at->format('M d, Y H:i') : 'N/A' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" style="text-align: center; color: var(--text-muted); padding: 1.5rem;">
                            No users found matching "{{ $search }}".
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($users->hasPages())
        <div class="pagination-wrapper">
            <span style="color: var(--text-muted); font-size: 0.8rem;">
                Showing {{ $users->firstItem() }} to {{ $users->lastItem() }} of {{ $users->total() }} users
            </span>
            <div>
                {{ $users->links() }}
            </div>
        </div>
    @endif
</div>
@endsection
