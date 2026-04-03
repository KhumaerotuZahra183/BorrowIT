<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Borrow Request | BorrowIT</title>
    <link rel="stylesheet" href="{{ asset('css/borrow-request.css') }}">
</head>
<body>
    <div class="shell">
        <aside class="sidebar">
            <div class="brand"><img class="brand-logo" src="{{ asset('BIT2-removebg-preview 1.png') }}" alt="BorrowIT"></div>
            <nav class="nav">
                <a href="{{ route('dashboard') }}">Dashboard</a>
                <a href="{{ route('users.index') }}">Manage Users</a>
                <a href="{{ route('assets.index') }}">Asset Management</a>
                <a class="active" href="{{ route('borrow.index') }}">Borrow Request</a>
                <a href="{{ route('borrow.active') }}">Active Borrow</a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="btn" type="submit">Logout</button>
                </form>
            </nav>
        </aside>

        <main class="content">
            <div class="topbar">
                <h1>Borrow Request</h1>
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
                    <div class="chips">
                        <div class="chip">Pending <span>{{ $counts['pending'] }}</span></div>
                        <div class="chip">Approved <span>{{ $counts['approved'] }}</span></div>
                        <div class="chip">Borrowed <span>{{ $counts['on_loan'] }}</span></div>
                    </div>
                    <form class="search" method="GET" action="{{ route('borrow.index') }}">
                        <input type="text" name="search" placeholder="Search Request" value="{{ $search }}">
                    </form>
                </div>

                <table>
                    <thead>
                        <tr>
                            <th>Request ID</th>
                            <th>User</th>
                            <th>Asset</th>
                            <th>Request Date</th>
                            <th>Duration</th>
                            <th>Status</th>
                            <th>Approve Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($requests as $row)
                            <tr>
                                <td>BR-{{ str_pad((string) $row->id, 3, '0', STR_PAD_LEFT) }}</td>
                                <td>{{ $row->user->name }}</td>
                                <td>{{ $row->asset->asset_name }}</td>
                                <td>{{ $row->request_date->format('d-m-Y') }}</td>
                                <td>{{ $row->duration_days }} Days</td>
                                <td>
                                    @php
                                        $statusKey = strtolower(str_replace(' ', '', $row->status));
                                    @endphp
                                    <div class="status-wrap">
                                        <span class="status {{ $statusKey }}">{{ $row->status }}</span>
                                        @if ($row->status === 'Pending')
                                            <div class="icons">
                                                <form method="POST" action="{{ route('borrow.approve', $row) }}">
                                                    @csrf
                                                    <button class="icon approve" type="submit">✓</button>
                                                </form>
                                                <form method="POST" action="{{ route('borrow.reject', $row) }}">
                                                    @csrf
                                                    <button class="icon reject" type="submit">✕</button>
                                                </form>
                                            </div>
                                        @elseif ($row->status === 'Approved')
                                            <a class="pill" href="{{ route('borrow.handover', $row) }}">Hand Over</a>
                                        @endif
                                    </div>
                                </td>
                                <td>{{ $row->approve_date ? $row->approve_date->format('d-m-Y') : '-' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7">Belum ada data request.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                <div class="pagination">
                    @foreach ($requests->getUrlRange(1, $requests->lastPage()) as $page => $url)
                        @if ($page == $requests->currentPage())
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
