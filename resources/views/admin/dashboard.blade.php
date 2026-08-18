<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Azza Respiratory Therapy</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg-color: #0b0f19;
            --sidebar-bg: #111827;
            --card-bg: #1f2937;
            --card-border: rgba(255, 255, 255, 0.08);
            --primary: #6366f1;
            --primary-glow: rgba(99, 102, 241, 0.25);
            --text-main: #f9fafb;
            --text-muted: #9ca3af;
            --accent-green: #10b981;
            --accent-amber: #f59e0b;
            --accent-purple: #8b5cf6;
            --accent-blue: #3b82f6;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif;
        }

        body {
            background-color: var(--bg-color);
            color: var(--text-main);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* Top Navbar */
        .navbar {
            background: var(--sidebar-bg);
            border-bottom: 1px solid var(--card-border);
            padding: 1rem 2rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 100;
            backdrop-filter: blur(10px);
        }

        .nav-brand {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            font-size: 1.25rem;
            font-weight: 800;
            color: #ffffff;
        }

        .brand-icon {
            width: 38px;
            height: 38px;
            background: linear-gradient(135deg, var(--primary) 0%, #4338ca 100%);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 12px var(--primary-glow);
        }

        .brand-icon svg {
            width: 22px;
            height: 22px;
            fill: #ffffff;
        }

        .nav-user {
            display: flex;
            align-items: center;
            gap: 1.25rem;
        }

        .user-profile {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            background: rgba(255, 255, 255, 0.05);
            padding: 0.4rem 0.85rem;
            border-radius: 100px;
            border: 1px solid var(--card-border);
        }

        .avatar {
            width: 32px;
            height: 32px;
            background: var(--primary);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 0.875rem;
            color: #ffffff;
        }

        .user-info {
            display: flex;
            flex-direction: column;
        }

        .user-name {
            font-size: 0.875rem;
            font-weight: 600;
            color: #ffffff;
            line-height: 1.2;
        }

        .user-role {
            font-size: 0.75rem;
            color: var(--primary);
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .btn-logout {
            background: rgba(239, 68, 68, 0.12);
            border: 1px solid rgba(239, 68, 68, 0.3);
            color: #fca5a5;
            padding: 0.5rem 1.1rem;
            border-radius: 10px;
            font-size: 0.85rem;
            font-weight: 600;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.2s ease;
        }

        .btn-logout:hover {
            background: rgba(239, 68, 68, 0.25);
            color: #ffffff;
            transform: translateY(-1px);
        }

        /* Container Layout */
        .main-container {
            flex: 1;
            max-width: 1280px;
            width: 100%;
            margin: 0 auto;
            padding: 2rem;
        }

        .header-section {
            margin-bottom: 2rem;
        }

        .header-section h1 {
            font-size: 1.85rem;
            font-weight: 800;
            color: #ffffff;
            margin-bottom: 0.4rem;
        }

        .header-section p {
            color: var(--text-muted);
            font-size: 0.95rem;
        }

        /* Stat Cards Grid */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2.5rem;
        }

        .stat-card {
            background: var(--card-bg);
            border: 1px solid var(--card-border);
            border-radius: 18px;
            padding: 1.5rem;
            position: relative;
            overflow: hidden;
            transition: transform 0.25s ease, box-shadow 0.25s ease;
        }

        .stat-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 25px -5px rgba(0, 0, 0, 0.4);
        }

        .stat-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 1rem;
        }

        .stat-title {
            font-size: 0.875rem;
            font-weight: 600;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .stat-icon {
            width: 44px;
            height: 44px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .stat-icon svg {
            width: 22px;
            height: 22px;
            stroke-width: 2;
        }

        .icon-blue { background: rgba(59, 130, 246, 0.15); color: #60a5fa; }
        .icon-green { background: rgba(16, 185, 129, 0.15); color: #34d399; }
        .icon-amber { background: rgba(245, 158, 11, 0.15); color: #fbbf24; }
        .icon-purple { background: rgba(139, 92, 246, 0.15); color: #c084fc; }

        .stat-value {
            font-size: 2.25rem;
            font-weight: 800;
            color: #ffffff;
            line-height: 1;
        }

        /* Recent Users Table Card */
        .content-card {
            background: var(--card-bg);
            border: 1px solid var(--card-border);
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
        }

        .card-header {
            padding: 1.5rem 1.75rem;
            border-bottom: 1px solid var(--card-border);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .card-title {
            font-size: 1.15rem;
            font-weight: 700;
            color: #ffffff;
        }

        .table-responsive {
            width: 100%;
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            text-align: left;
        }

        th {
            background: rgba(17, 24, 39, 0.6);
            padding: 1rem 1.75rem;
            font-size: 0.775rem;
            font-weight: 700;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.05em;
            border-bottom: 1px solid var(--card-border);
        }

        td {
            padding: 1.1rem 1.75rem;
            font-size: 0.9rem;
            color: #e5e7eb;
            border-bottom: 1px solid rgba(255, 255, 255, 0.04);
        }

        tr:last-child td {
            border-bottom: none;
        }

        tr:hover td {
            background: rgba(255, 255, 255, 0.02);
        }

        .user-cell {
            display: flex;
            align-items: center;
            gap: 0.85rem;
        }

        .user-avatar-sm {
            width: 34px;
            height: 34px;
            border-radius: 50%;
            background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 0.85rem;
            color: #ffffff;
        }

        .badge {
            display: inline-flex;
            align-items: center;
            padding: 0.3rem 0.75rem;
            border-radius: 100px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: capitalize;
        }

        .badge-admin {
            background: rgba(139, 92, 246, 0.18);
            border: 1px solid rgba(139, 92, 246, 0.35);
            color: #c084fc;
        }

        .badge-user {
            background: rgba(59, 130, 246, 0.18);
            border: 1px solid rgba(59, 130, 246, 0.35);
            color: #60a5fa;
        }

        .badge-active {
            background: rgba(16, 185, 129, 0.18);
            border: 1px solid rgba(16, 185, 129, 0.35);
            color: #34d399;
        }

        .badge-inactive {
            background: rgba(239, 68, 68, 0.18);
            border: 1px solid rgba(239, 68, 68, 0.35);
            color: #fca5a5;
        }

        footer {
            padding: 1.5rem;
            text-align: center;
            font-size: 0.825rem;
            color: var(--text-muted);
            border-top: 1px solid var(--card-border);
            margin-top: auto;
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <div class="nav-brand">
            <div class="brand-icon">
                <svg viewBox="0 0 24 24"><path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/></svg>
            </div>
            <span>AZZA Admin</span>
        </div>

        <div class="nav-user">
            <div class="user-profile">
                <div class="avatar">{{ strtoupper(substr($user->name, 0, 1)) }}</div>
                <div class="user-info">
                    <span class="user-name">{{ $user->name }}</span>
                    <span class="user-role">{{ $user->role }}</span>
                </div>
            </div>

            <form action="{{ route('logout') }}" method="POST" style="margin:0;">
                @csrf
                <button type="submit" class="btn-logout">
                    <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                    </svg>
                    <span>Logout</span>
                </button>
            </form>
        </div>
    </nav>

    <main class="main-container">
        <div class="header-section">
            <h1>System Overview</h1>
            <p>Welcome back, {{ $user->name }}. Here is your application summary.</p>
        </div>

        <div class="stats-grid">
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
        </div>

        <div class="content-card">
            <div class="card-header">
                <h2 class="card-title">Recent Registered Users</h2>
            </div>
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>User</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Status</th>
                            <th>Registered Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($metrics['recentUsers'] as $recentUser)
                            <tr>
                                <td>
                                    <div class="user-cell">
                                        <div class="user-avatar-sm">{{ strtoupper(substr($recentUser->name, 0, 1)) }}</div>
                                        <span>{{ $recentUser->name }}</span>
                                    </div>
                                </td>
                                <td>{{ $recentUser->email }}</td>
                                <td>
                                    <span class="badge {{ $recentUser->role === 'admin' ? 'badge-admin' : 'badge-user' }}">
                                        {{ $recentUser->role }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge {{ $recentUser->status === 'active' ? 'badge-active' : 'badge-inactive' }}">
                                        {{ $recentUser->status }}
                                    </span>
                                </td>
                                <td>{{ $recentUser->created_at ? $recentUser->created_at->format('M d, Y H:i') : 'N/A' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" style="text-align: center; color: var(--text-muted);">No users found</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <footer>
        &copy; {{ date('Y') }} Azza Respiratory Therapy. Powered by Laravel Sanctum.
    </footer>
</body>
</html>
