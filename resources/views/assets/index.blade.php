<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Asset Management | BorrowIT</title>
    <link rel="stylesheet" href="{{ asset('css/asset-management.css') }}">
</head>
<body>
    <div class="shell">
        <aside class="sidebar">
            <div class="brand">Borrow<span>IT</span></div>
            <nav class="nav">
                <a href="{{ route('dashboard') }}">Dashboard</a>
                <a href="{{ route('users.index') }}">Manage Users</a>
                <a class="active" href="{{ route('assets.index') }}">Asset Management</a>
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
                <h1>Asset Management</h1>
                <div class="topbar-actions">
                    <form method="POST" action="{{ route('notifications.read') }}" class="notif-form">
                        @csrf
                        <button class="notif-button" type="submit" aria-label="Mark all read">
                            <svg class="notif-icon" viewBox="0 0 24 24" aria-hidden="true" focusable="false">
                                <path d="M12 22a2.5 2.5 0 0 0 2.45-2h-4.9A2.5 2.5 0 0 0 12 22Zm7-6V11a7 7 0 0 0-5-6.71V3a2 2 0 0 0-4 0v1.29A7 7 0 0 0 5 11v5l-2 2v1h18v-1l-2-2Z" fill="currentColor"/>
                            </svg>
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
                    <form class="search" method="GET" action="{{ route('assets.index') }}">
                        <input type="text" name="search" placeholder="Search Asset" value="{{ $search }}">
                    </form>
                    <a class="btn" href="{{ route('assets.create') }}" style="text-decoration:none;">+ Add New Asset</a>
                </div>

                @if (session('status'))
                    <div class="status">{{ session('status') }}</div>
                @endif
                <table>
                    <thead>
                        <tr>
                            <th>Asset ID</th>
                            <th>Asset Number</th>
                            <th>Asset Name</th>
                            <th>Available</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($assets as $row)
                            <tr>
                                <td>{{ $row->asset_id }}</td>
                                <td>{{ $row->asset_number }}</td>
                                <td>{{ $row->asset_name }}</td>
                                <td>{{ $row->available }}</td>
                                <td>
                                    <div class="actions">
                                        <a class="btn" href="{{ route('assets.edit', $row) }}" style="text-decoration:none;">Edit</a>
                                        <form method="POST" action="{{ route('assets.destroy', $row) }}" onsubmit="return confirm('Hapus asset ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn" type="submit">Delete</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5">Belum ada data asset.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                <div class="pagination">
                    @foreach ($assets->getUrlRange(1, $assets->lastPage()) as $page => $url)
                        @if ($page == $assets->currentPage())
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
