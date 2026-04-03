<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notification | BorrowIT</title>
    <link rel="stylesheet" href="{{ asset('css/manage-users.css') }}">
</head>
<body>
    <div class="shell">
        <aside class="sidebar">
            <div class="brand">Borrow<span>IT</span></div>
            <nav class="nav">
                @if (($user->role ?? 'User') === 'Admin')
                    <a href="{{ route('dashboard') }}">Dashboard</a>
                    <a href="{{ route('users.index') }}">Manage Users</a>
                    <a href="{{ route('assets.index') }}">Asset Management</a>
                    <a href="{{ route('borrow.index') }}">Borrow Request</a>
                    <a href="{{ route('borrow.active') }}">Active Borrow</a>
                @else
                    <a href="{{ route('user.dashboard') }}">Dashboard</a>
                    <a href="{{ route('user.borrowings') }}">My Borrowings</a>
                @endif
                <a class="active" href="{{ route('notifications.index') }}">Notification</a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="btn" type="submit">Logout</button>
                </form>
            </nav>
        </aside>

        <main class="content">
            <div class="topbar">
                <h1>Notification</h1>
                <div class="topbar-actions">
                    <a class="notif-button" href="{{ route('notifications.index') }}" aria-label="Notification">
                        <svg class="notif-icon" viewBox="0 0 24 24" aria-hidden="true" focusable="false">
                            <path d="M12 22a2.5 2.5 0 0 0 2.45-2h-4.9A2.5 2.5 0 0 0 12 22Zm7-6V11a7 7 0 0 0-5-6.71V3a2 2 0 0 0-4 0v1.29A7 7 0 0 0 5 11v5l-2 2v1h18v-1l-2-2Z" fill="currentColor"/>
                        </svg>
                    </a>
                    <form method="POST" action="{{ route('notifications.read') }}">
                        @csrf
                        <button class="btn" type="submit">Mark All Read</button>
                    </form>
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

            <section class="panel">
                <table>
                    <thead>
                        <tr>
                            <th>Type</th>
                            <th>Message</th>
                            <th>Date</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($notifications as $notif)
                            <tr>
                                <td>{{ $notif->type }}</td>
                                <td>
                                    @if ($notif->link)
                                        <a href="{{ $notif->link }}" style="text-decoration:none;">{{ $notif->message }}</a>
                                    @else
                                        {{ $notif->message }}
                                    @endif
                                </td>
                                <td>{{ $notif->created_at->format('d-m-Y H:i') }}</td>
                                <td>{{ $notif->read_at ? 'Read' : 'New' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4">Belum ada notifikasi.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                <div class="pagination">
                    @foreach ($notifications->getUrlRange(1, $notifications->lastPage()) as $page => $url)
                        @if ($page == $notifications->currentPage())
                            <span class="current">{{ $page }}</span>
                        @else
                            <a href="{{ $url }}">{{ $page }}</a>
                        @endif
                    @endforeach
                </div>
            </section>
        </main>
    </div>
</body>
</html>
