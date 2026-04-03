<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users | BorrowIT</title>
    <link rel="stylesheet" href="{{ asset('css/manage-users.css') }}">
</head>
<body>
    <div class="shell">
        <aside class="sidebar">
            <div class="brand"><img class="brand-logo" src="{{ asset('BIT2-removebg-preview 1.png') }}" alt="BorrowIT"></div>
            <nav class="nav">
                <a href="{{ route('dashboard') }}">Dashboard</a>
                <a class="active" href="{{ route('users.index') }}">Manage Users</a>
                <a href="{{ route('assets.index') }}">Asset Management</a>
                <a href="{{ route('borrow.index') }}">Borrow Request</a>
                <a href="{{ route('borrow.active') }}">Active Borrow</a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="btn" type="submit">Logout</button>
                </form>
            </nav>
        </aside>

        <main class="content">
            <div class="topbar">
                <h1>Manage Users</h1>
                <div class="topbar-actions">
                    <form method="POST" action="{{ route('notifications.read') }}" class="notif-form">
                    @php
                        $unreadCount = $unreadCount ?? \App\Models\Notification::where('user_id', $user->id)->whereNull('read_at')->count();
                    @endphp
                        @csrf
                        <button class="notif-button" type="submit" aria-label="Mark all read">
                            <svg class="notif-icon" viewBox="0 0 24 24" aria-hidden="true" focusable="false">
                                <path d="M12 22a2.5 2.5 0 0 0 2.45-2h-4.9A2.5 2.5 0 0 0 12 22Zm7-6V11a7 7 0 0 0-5-6.71V3a2 2 0 0 0-4 0v1.29A7 7 0 0 0 5 11v5l-2 2v1h18v-1l-2-2Z" fill="currentColor"/>
                            </svg>
                            @if ($unreadCount > 0)
                                <span class="notif-dot" aria-hidden="true"></span>
                            @endif
                        </button>
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
                <div class="toolbar">
                    <form class="search" method="GET" action="{{ route('users.index') }}">
                        <input type="text" name="search" placeholder="Search User" value="{{ $search }}">
                    </form>
                    <a class="btn" href="{{ route('users.create') }}" style="text-decoration:none;">+ Add New User</a>
                </div>

                @if (session('status'))
                    <div class="status">{{ session('status') }}</div>
                @endif
                <table>
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Department</th>
                            <th>Role</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($users as $row)
                            <tr>
                                <td>{{ $row->name }}</td>
                                <td>{{ $row->email }}</td>
                                <td>{{ $row->department ?? '-' }}</td>
                                <td>{{ $row->role ?? 'User' }}</td>
                                <td><span class="status">{{ $row->status ?? 'Active' }}</span></td>
                                <td>
                                    <div class="actions">
                                        <a class="btn" href="{{ route('users.edit', $row) }}" style="text-decoration:none;">Edit</a>
                                        <form method="POST" action="{{ route('users.destroy', $row) }}" onsubmit="return confirm('Hapus user ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn" type="submit">Delete</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6">Belum ada data user.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                <div class="pagination">
                    @foreach ($users->getUrlRange(1, $users->lastPage()) as $page => $url)
                        @if ($page == $users->currentPage())
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
