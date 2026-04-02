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
                <div class="profile">
                    <div class="avatar">{{ strtoupper(substr($user->name, 0, 1)) }}</div>
                    <span>{{ $user->name }}</span>
                    <form method="POST" action="{{ route('notifications.read') }}">
                        @csrf
                        <button class="btn" type="submit">Mark All Read</button>
                    </form>
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
