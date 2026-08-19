@extends('layouts.admin')

@section('title', 'Dashboard Overview')

@section('content')
<!-- Page Header -->
<div style="margin-bottom: 1.5rem;">
    <h1 class="page-title">Dashboard Overview</h1>
    <p class="page-subtitle">Real-time system stats, active/suspended metrics, and registered users.</p>
</div>

<!-- Compact Metric Cards Grid (p-4, #161e2e background, #1f2937 border) -->
<div class="stats-grid">
    <!-- Card 1: Total Users -->
    <div class="stat-card">
        <div class="stat-header">
            <span class="stat-title">Total Users</span>
            <div class="stat-icon icon-blue">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
            </div>
        </div>
        <div class="stat-value">{{ $metrics['totalUsers'] }}</div>
    </div>

    <!-- Card 2: Active Users -->
    <div class="stat-card">
        <div class="stat-header">
            <span class="stat-title">Active Users</span>
            <div class="stat-icon icon-green">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
        </div>
        <div class="stat-value">{{ $metrics['activeUsers'] }}</div>
    </div>

    <!-- Card 3: Suspended Users -->
    <div class="stat-card">
        <div class="stat-header">
            <span class="stat-title">Suspended Users</span>
            <div class="stat-icon icon-amber">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                </svg>
            </div>
        </div>
        <div class="stat-value">{{ $metrics['suspendedUsers'] }}</div>
    </div>
</div>

<!-- Compact Data Table (sm size: py-2.5 px-3 slim padding) -->
<div class="content-card">
    <div class="card-header">
        <h2 class="card-title">Registered Users Summary</h2>
    </div>
    <div class="table-responsive">
        <table class="compact-table">
            <thead>
                <tr>
                    <th>User ID</th>
                    <th>Full Name</th>
                    <th>Email Address</th>
                    <th>Role</th>
                    <th>Status</th>
                    <th>Registered Date</th>
                </tr>
            </thead>
            <tbody>
                @forelse($metrics['recentUsers'] as $user)
                    <tr>
                        <td><span style="font-weight: 700; color: #ffffff;">#{{ $user->id }}</span></td>
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
                            <span class="badge {{ $user->status === 'active' ? 'badge-active' : ($user->status === 'suspended' ? 'badge-suspended' : 'badge-inactive') }}">
                                {{ $user->status }}
                            </span>
                        </td>
                        <td>{{ $user->created_at ? $user->created_at->format('M d, Y H:i') : 'N/A' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" style="text-align: center; color: var(--text-muted); padding: 1.5rem;">No users found in database.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
