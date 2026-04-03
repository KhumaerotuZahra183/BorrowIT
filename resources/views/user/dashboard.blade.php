<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard | BorrowIT</title>
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
</head>
<body>
    <div class="shell">
        <aside class="sidebar">
            <div class="brand">Borrow<span>IT</span></div>
            <nav class="nav">
                <a class="active" href="{{ route('user.dashboard') }}">Dashboard</a>
                <a href="{{ route('user.borrowings') }}">My Borrowings</a>
                <a href="{{ route('notifications.index') }}">Notification</a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="btn" type="submit">Logout</button>
                </form>
            </nav>
        </aside>

        <main class="content">
            <div class="topbar">
                <div>
                    <h1>Dashboard</h1>
                    <p>Welcome, {{ $user->name }}!</p>
                </div>
                <div class="topbar-actions">
                    <a class="notif-button" href="{{ route('notifications.index') }}" aria-label="Notification">
                        <svg class="notif-icon" viewBox="0 0 24 24" aria-hidden="true" focusable="false">
                            <path d="M12 22a2.5 2.5 0 0 0 2.45-2h-4.9A2.5 2.5 0 0 0 12 22Zm7-6V11a7 7 0 0 0-5-6.71V3a2 2 0 0 0-4 0v1.29A7 7 0 0 0 5 11v5l-2 2v1h18v-1l-2-2Z" fill="currentColor"/>
                        </svg>
                    </a>
                    <details class="profile-dropdown">
                        <summary>
                            <div class="avatar">{{ strtoupper(substr($user->name, 0, 1)) }}</div>
                            <span class="profile-name">{{ $user->name }}</span>
                            <span class="caret" aria-hidden="true"></span>
                        </summary>
                        <div class="profile-menu">
                            <div class="profile-meta">
                                <div class="avatar">{{ strtoupper(substr($user->name, 0, 1)) }}</div>
                                <div>
                                    <div class="profile-title">{{ $user->name }}</div>
                                    <div class="profile-role">{{ $user->role ?? 'User' }}</div>
                                </div>
                            </div>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button class="logout" type="submit">Log Out</button>
                            </form>
                        </div>
                    </details>
                </div>
            </div>

            <section class="stats">
                <div class="stat">
                    <span>My Active Borrow</span>
                    <strong>{{ $stats['active'] }}</strong>
                </div>
                <div class="stat">
                    <span>Overdue</span>
                    <strong>{{ $stats['overdue'] }}</strong>
                </div>
                <div class="stat">
                    <span>Pending Request</span>
                    <strong>{{ $stats['pending'] }}</strong>
                </div>
            </section>

            <section class="panel" style="margin-top:16px;">
                <h3>My Borrowed Assets</h3>
                <table>
                    <thead>
                        <tr>
                            <th>Borrow ID</th>
                            <th>Asset</th>
                            <th>Borrow Date</th>
                            <th>Due Date</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($activeBorrows as $borrow)
                            <tr>
                                <td>BR-{{ str_pad((string) $borrow->id, 3, '0', STR_PAD_LEFT) }}</td>
                                <td>{{ $borrow->asset->asset_name }}</td>
                                <td>{{ $borrow->borrow_date->format('d-m-Y') }}</td>
                                <td>{{ $borrow->due_date->format('d-m-Y') }}</td>
                                <td>
                                    @php
                                        $statusClass = strtolower(str_replace(' ', '', $borrow->status));
                                    @endphp
                                    <span class="status {{ $statusClass }}">{{ $borrow->status }}</span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5">Belum ada pinjaman.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </section>

            <section class="panel" style="margin-top:16px;">
                <h3>Notifications</h3>
                <ul style="margin:0; padding-left:16px;">
                    @forelse ($notifications as $notif)
                        <li>{{ $notif->message }}</li>
                    @empty
                        <li>Belum ada notifikasi.</li>
                    @endforelse
                </ul>
            </section>
        </main>
    </div>
</body>
</html>
