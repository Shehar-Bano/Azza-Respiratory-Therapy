@extends('layouts.admin')

@section('title', 'Dashboard Overview')

@section('content')
<!-- Page Header -->
<div style="margin-bottom: 1.5rem;">
    <h1 class="page-title">Dashboard Overview</h1>
    <p class="page-subtitle">Real-time system stats, daily completion metrics, and registered users.</p>
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

    <!-- Card 3: Inactive Users -->
    <div class="stat-card">
        <div class="stat-header">
            <span class="stat-title">Inactive Users</span>
            <div class="stat-icon icon-amber">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                </svg>
            </div>
        </div>
        <div class="stat-value">{{ $metrics['inactiveUsers'] }}</div>
    </div>

    <!-- Card 4: Admin Users -->
    <div class="stat-card">
        <div class="stat-header">
            <span class="stat-title">Admin Users</span>
            <div class="stat-icon icon-purple">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />
                </svg>
            </div>
        </div>
        <div class="stat-value">{{ $metrics['adminUsers'] }}</div>
    </div>

    <!-- Card 5: Daily Progress Widget (80% Completion Indicator) -->
    <div class="stat-card">
        <div class="stat-header">
            <span class="stat-title">Daily Progress</span>
            <div class="stat-icon icon-indigo">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                </svg>
            </div>
        </div>
        <div class="progress-widget">
            <div class="progress-header">
                <span style="color: var(--text-secondary); font-size: 0.75rem;">Goal Completion</span>
                <span style="color: #818cf8; font-size: 0.85rem; font-weight: 800;">80%</span>
            </div>
            <div class="progress-bar-bg">
                <div class="progress-bar-fill" style="width: 80%;"></div>
            </div>
        </div>
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
                            <span class="badge {{ $user->status === 'active' ? 'badge-active' : 'badge-inactive' }}">
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
