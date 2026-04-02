<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard | BorrowIT</title>
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
</head>
<body>
    <div class="shell">
        <aside class="sidebar">
            <div class="brand">Borrow<span>IT</span></div>
            <nav class="nav">
                <a class="active" href="{{ route('dashboard') }}">Dashboard</a>
                <a href="{{ route('users.index') }}">Manage Users</a>
                <a href="{{ route('assets.index') }}">Asset Management</a>
                <a href="{{ route('borrow.index') }}">Borrow Request</a>
                <a href="{{ route('borrow.active') }}">Active Borrow</a>
            </nav>
        </aside>

        <main class="content">
            <div class="topbar">
                <div>
                    <h1>Dashboard</h1>
                    <p>Welcome, {{ $user->name }}!</p>
                </div>
                <div class="profile">
                    <div class="avatar">{{ strtoupper(substr($user->name, 0, 1)) }}</div>
                    <span>{{ $user->name }}</span>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button class="logout" type="submit">Log Out</button>
                    </form>
                </div>
            </div>

            <section class="stats">
                <div class="stat">
                    <span>Total Assets</span>
                    <strong>{{ $stats['total_assets'] }}</strong>
                </div>
                <div class="stat">
                    <span>Available Stock</span>
                    <strong>{{ $stats['available_stock'] }}</strong>
                </div>
                <div class="stat">
                    <span>Pending Request</span>
                    <strong>{{ $stats['pending_request'] }}</strong>
                </div>
                <div class="stat">
                    <span>Active Borrow</span>
                    <strong>{{ $stats['active_borrow'] }}</strong>
                </div>
            </section>

            <section class="grid">
                <div class="panel">
                    <h3>Recent Borrow Request</h3>
                    <table>
                        <thead>
                            <tr>
                                <th>Request ID</th>
                                <th>User</th>
                                <th>Asset</th>
                                <th>Borrow Date</th>
                                <th>Duration</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($recentRequests as $request)
                                <tr>
                                    <td>{{ $request['id'] }}</td>
                                    <td>{{ $request['user'] }}</td>
                                    <td>{{ $request['asset'] }}</td>
                                    <td>{{ $request['borrow_date'] }}</td>
                                    <td>{{ $request['duration'] }}</td>
                                    <td>
                                        @php
                                            $statusClass = strtolower(str_replace(' ', '', $request['status']));
                                        @endphp
                                        <span class="status {{ $statusClass }}">{{ $request['status'] }}</span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="panel">
                    <h3>Monthly Borrowing</h3>
                    <div class="chart">
                        @foreach ($chart['values'] as $value)
                            @php $height = 20 + ($value * 6); @endphp
                            <div class="bar" style="height: {{ $height }}px;">{{ $value }}</div>
                        @endforeach
                    </div>
                    <div class="labels">
                        @foreach ($chart['labels'] as $label)
                            <div>{{ $label }}</div>
                        @endforeach
                    </div>
                </div>

                <div class="panel" style="grid-column: 1 / -1;">
                    <h3>Active Borrow</h3>
                    <table>
                        <thead>
                            <tr>
                                <th>Borrow ID</th>
                                <th>User</th>
                                <th>Asset</th>
                                <th>Borrow Date</th>
                                <th>Due Date</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($activeBorrows as $borrow)
                                <tr>
                                    <td>{{ $borrow['id'] }}</td>
                                    <td>{{ $borrow['user'] }}</td>
                                    <td>{{ $borrow['asset'] }}</td>
                                    <td>{{ $borrow['borrow_date'] }}</td>
                                    <td>{{ $borrow['due_date'] }}</td>
                                    <td>
                                        @php
                                            $statusClass = strtolower(str_replace(' ', '', $borrow['status']));
                                        @endphp
                                        <span class="status {{ $statusClass }}">{{ $borrow['status'] }}</span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </section>
        </main>
    </div>
</body>
</html>
