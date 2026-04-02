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
                        <div class="chip">On Loan <span>{{ $counts['on_loan'] }}</span></div>
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
                                <td>{{ $row['id'] }}</td>
                                <td>{{ $row['user'] }}</td>
                                <td>{{ $row['asset'] }}</td>
                                <td>{{ $row['borrow_date'] }}</td>
                                <td>{{ $row['duration'] }}</td>
                                <td>
                                    @php
                                        $statusKey = strtolower(str_replace(' ', '', $row['status']));
                                    @endphp
                                    <div class="status-wrap">
                                        <span class="status {{ $statusKey }}">{{ $row['status'] }}</span>
                                        @if ($row['status'] === 'Pending')
                                            <div class="icons">
                                                <span class="icon approve">✓</span>
                                                <span class="icon reject">✕</span>
                                            </div>
                                        @elseif ($row['status'] === 'Approved')
                                            <span class="pill">Hand Over</span>
                                        @endif
                                    </div>
                                </td>
                                <td>{{ $row['approve_date'] }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7">Belum ada data request.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                <div class="pagination">
                    @foreach ($pages as $page)
                        @if ($page === $currentPage)
                            <span class="current">{{ $page }}</span>
                        @else
                            <a href="{{ route('borrow.index', ['page' => $page, 'search' => $search]) }}">{{ $page }}</a>
                        @endif
                    @endforeach
                </div>
            </section>
        </main>
    </div>
</body>
</html>
