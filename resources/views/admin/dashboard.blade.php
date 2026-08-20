@extends('layouts.admin')

@section('title', 'Dashboard Overview')

@section('styles')
<style>
    .plans-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 1.25rem;
        margin-bottom: 1.75rem;
    }

    .plan-card {
        background: #161e2e;
        border: 1px solid var(--card-border);
        border-radius: 12px;
        padding: 1.25rem;
        position: relative;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.25);
        transition: transform 0.2s ease, border-color 0.2s ease;
    }

    .plan-card:hover {
        transform: translateY(-2px);
        border-color: rgba(99, 102, 241, 0.4);
    }

    .plan-card.featured {
        border: 1px solid rgba(168, 85, 247, 0.5);
        background: linear-gradient(135deg, rgba(22, 30, 46, 0.95), rgba(88, 28, 135, 0.15));
    }

    .plan-badge-id {
        display: inline-block;
        padding: 0.2rem 0.55rem;
        border-radius: 6px;
        font-size: 0.725rem;
        font-weight: 700;
        background: rgba(99, 102, 241, 0.15);
        color: #a5b4fc;
        border: 1px solid rgba(99, 102, 241, 0.3);
    }

    .plan-title {
        font-size: 1.1rem;
        font-weight: 800;
        color: #ffffff;
        margin-top: 0.35rem;
        margin-bottom: 0.25rem;
    }

    .price-box {
        display: flex;
        align-items: baseline;
        gap: 0.5rem;
        margin: 0.75rem 0;
        flex-wrap: wrap;
    }

    .price-amount-usd {
        font-size: 1.5rem;
        font-weight: 800;
        color: #34d399;
    }

    .price-amount-sar {
        font-size: 0.9rem;
        font-weight: 600;
        color: #c084fc;
        background: rgba(168, 85, 247, 0.12);
        padding: 0.25rem 0.6rem;
        border-radius: 6px;
        border: 1px solid rgba(168, 85, 247, 0.25);
    }

    .plan-access {
        color: var(--text-secondary);
        font-size: 0.8rem;
        line-height: 1.4;
        margin-bottom: 0.85rem;
        background: rgba(11, 15, 25, 0.5);
        padding: 0.5rem 0.75rem;
        border-radius: 6px;
        border: 1px solid rgba(255, 255, 255, 0.05);
    }

    .feature-list {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .feature-item {
        display: flex;
        align-items: center;
        gap: 0.45rem;
        font-size: 0.8rem;
        color: #cbd5e1;
        margin-bottom: 0.4rem;
    }

    .feature-item svg {
        color: #34d399;
        width: 14px;
        height: 14px;
        flex-shrink: 0;
    }
</style>
@endsection

@section('content')
<!-- Page Header -->
<div style="margin-bottom: 1.5rem;">
    <h1 class="page-title">Dashboard Overview</h1>
    <p class="page-subtitle">Real-time system stats, subscription plans, active/suspended metrics, and registered users.</p>
</div>

<!-- Compact Metric Cards Grid -->
<div class="stats-grid" style="grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));">
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

    <!-- Card 4: Active Subscription Plans -->
    <div class="stat-card">
        <div class="stat-header">
            <span class="stat-title">Subscription Plans</span>
            <div class="stat-icon" style="background: rgba(168, 85, 247, 0.15); color: #c084fc;">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
        </div>
        <div class="stat-value">{{ $metrics['totalPlans'] }}</div>
    </div>
</div>

<!-- Subscription Plans Grid Section -->
<div style="margin-bottom: 1.75rem;">
    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1rem;">
        <div>
            <h2 class="card-title" style="font-size: 1.1rem; font-weight: 800; color: #ffffff;">Subscription Plans Overview</h2>
            <p style="color: var(--text-muted); font-size: 0.8rem; margin-top: 0.15rem;">Configured pricing plans with USD & SAR currencies.</p>
        </div>
        <a href="{{ route('admin.plans.index') }}" style="font-size: 0.8rem; font-weight: 600; color: #818cf8; text-decoration: none;">Manage Plans &rarr;</a>
    </div>

    <div class="plans-grid">
        @forelse($subscriptionPlans as $plan)
            <div class="plan-card {{ $plan->plan_id === '2' ? 'featured' : '' }}">
                <div>
                    <div style="display: flex; align-items: center; justify-content: space-between;">
                        <span class="plan-badge-id">Plan ID: {{ $plan->plan_id }}</span>
                        @if($plan->duration_days > 0)
                            <span style="color: #818cf8; font-size: 0.75rem; font-weight: 600;">{{ $plan->duration_days }} Days</span>
                        @else
                            <span style="color: var(--text-muted); font-size: 0.75rem; font-weight: 600;">Lifetime Free</span>
                        @endif
                    </div>

                    <h3 class="plan-title">{{ $plan->title }}</h3>

                    <div class="price-box">
                        <span class="price-amount-usd">${{ $plan->price_usd ?? $plan->price }}/MO</span>
                        <span class="price-amount-sar">{{ $plan->price_sar }} SAR</span>
                    </div>

                    <div class="plan-access">
                        <strong style="color: #ffffff;">Access:</strong> {{ $plan->access }}
                    </div>

                    <ul class="feature-list">
                        @if(is_array($plan->features))
                            @foreach($plan->features as $feature)
                                <li class="feature-item">
                                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                    <span>{{ $feature }}</span>
                                </li>
                            @endforeach
                        @endif
                    </ul>
                </div>
            </div>
        @empty
            <div style="grid-column: 1 / -1; color: var(--text-muted); padding: 1.5rem; text-align: center; background: #161e2e; border-radius: 10px;">
                No subscription plans configured.
            </div>
        @endforelse
    </div>
</div>

<!-- Compact Registered Users Table -->
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
