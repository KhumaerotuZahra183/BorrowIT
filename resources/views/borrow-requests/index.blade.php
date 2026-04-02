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
            <div class="brand">Borrow<span>IT</span></div>
            <nav class="nav">
                <a href="{{ route('dashboard') }}">Dashboard</a>
                <a href="{{ route('users.index') }}">Manage Users</a>
                <a href="{{ route('assets.index') }}">Asset Management</a>
                <a class="active" href="{{ route('borrow.index') }}">Borrow Request</a>
                <a href="{{ route('borrow.active') }}">Active Borrow</a>
                <a href="{{ route('notifications.index') }}">Notification</a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="btn" type="submit">Logout</button>
                </form>
            </nav>
        </aside>

        <main class="content">
            <div class="topbar">
                <h1>Borrow Request</h1>
                <div class="profile">
                    <div class="avatar">{{ strtoupper(substr($user->name, 0, 1)) }}</div>
                    <span>{{ $user->name }}</span>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button class="logout" type="submit">Log Out</button>
                    </form>
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
