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
            </nav>
        </aside>

        <main class="content">
            <div class="topbar">
                <h1>Active Borrow</h1>
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
                                <td>{{ $row['id'] }}</td>
                                <td>{{ $row['request_id'] }}</td>
                                <td>{{ $row['user'] }}</td>
                                <td>{{ $row['asset'] }}</td>
                                <td>{{ $row['borrow_date'] }}</td>
                                <td>{{ $row['due_date'] }}</td>
                                <td>{{ $row['handover_pic'] }}</td>
                                <td>
                                    @php
                                        $statusClass = strtolower(str_replace(' ', '', $row['status']));
                                    @endphp
                                    <span class="status {{ $statusClass }}">{{ $row['status'] }}</span>
                                </td>
                                <td>
                                    <span>{{ $row['return_pic'] }}</span>
                                    <span class="icon">✎</span>
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
                    @foreach ($pages as $page)
                        @if ($page === $currentPage)
                            <span class="current">{{ $page }}</span>
                        @else
                            <a href="{{ route('borrow.active', ['page' => $page, 'search' => $search]) }}">{{ $page }}</a>
                        @endif
                    @endforeach
                </div>
            </section>
        </main>
    </div>
</body>
</html>
