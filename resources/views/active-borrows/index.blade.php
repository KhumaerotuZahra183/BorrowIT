<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Active Borrow | BorrowIT</title>
    <link rel="stylesheet" href="{{ asset('css/active-borrow.css') }}">
</head>
<body>
    <div class="shell">
        <aside class="sidebar">
            <div class="brand">Borrow<span>IT</span></div>
            <nav class="nav">
                <a href="{{ route('dashboard') }}">Dashboard</a>
                <a href="{{ route('users.index') }}">Manage Users</a>
                <a href="{{ route('assets.index') }}">Asset Management</a>
                <a href="{{ route('borrow.index') }}">Borrow Request</a>
                <a class="active" href="{{ route('borrow.active') }}">Active Borrow</a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="btn" type="submit">Logout</button>
                </form>
            </nav>
        </aside>

        <main class="content">
            <div class="topbar">
                <h1>Active Borrow</h1>
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
                    <form class="search" method="GET" action="{{ route('borrow.active') }}">
                        <input type="text" name="search" placeholder="Search Request" value="{{ $search }}">
                    </form>
                </div>

                <table>
                    <thead>
                        <tr>
                            <th>Borrow ID</th>
                            <th>Request ID</th>
                            <th>User</th>
                            <th>Asset</th>
                            <th>Borrow Date</th>
                            <th>Due Date</th>
                            <th>Handover PIC</th>
                            <th>Status</th>
                            <th>Return PIC</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($borrows as $row)
                            <tr>
                                <td>BR-{{ str_pad((string) $row->id, 3, '0', STR_PAD_LEFT) }}</td>
                                <td>BR-{{ str_pad((string) $row->borrow_request_id, 3, '0', STR_PAD_LEFT) }}</td>
                                <td>{{ $row->user->name }}</td>
                                <td>{{ $row->asset->asset_name }}</td>
                                <td>{{ $row->borrow_date->format('d-m-Y') }}</td>
                                <td>{{ $row->due_date->format('d-m-Y') }}</td>
                                <td>{{ $row->handover_pic ?? '-' }}</td>
                                <td>
                                    @php
                                        $statusClass = strtolower(str_replace(' ', '', $row->status));
                                    @endphp
                                    <span class="status {{ $statusClass }}">{{ $row->status }}</span>
                                </td>
                                <td>
                                    <span>{{ $row->return_pic ?? '-' }}</span>
                                    @if (!$row->returned_at)
                                        <a class="icon" href="{{ route('borrow.return', $row) }}">✎</a>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9">Belum ada data pinjaman.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                <div class="pagination">
                    @foreach ($borrows->getUrlRange(1, $borrows->lastPage()) as $page => $url)
                        @if ($page == $borrows->currentPage())
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
