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
                <a href="{{ route('notifications.index') }}">Notification</a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="btn" type="submit">Logout</button>
                </form>
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
                                    <td>BR-{{ str_pad((string) $request->id, 3, '0', STR_PAD_LEFT) }}</td>
                                    <td>{{ $request->user->name }}</td>
                                    <td>{{ $request->asset->asset_name }}</td>
                                    <td>{{ $request->request_date->format('d-m-Y') }}</td>
                                    <td>{{ $request->duration_days }} Days</td>
                                    <td>
                                        @php
                                            $statusClass = strtolower(str_replace(' ', '', $request->status));
                                        @endphp
                                        <span class="status {{ $statusClass }}">{{ $request->status }}</span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <div style="margin-top:8px;">
                        <a class="btn" href="{{ route('borrow.index') }}" style="text-decoration:none;">View All</a>
                    </div>
                </div>

                <div class="panel">
                    <div style="display:flex; align-items:center; justify-content:space-between;">
                        <h3>Monthly Borrowing</h3>
                        <form method="GET" action="{{ route('dashboard') }}" style="display:flex; gap:8px; align-items:center;">
                            <select name="month">
                                <option value="0">All Months</option>
                                @for ($m = 1; $m <= 12; $m++)
                                    <option value="{{ $m }}" @selected($filters['month'] == $m)>{{ $m }}</option>
                                @endfor
                            </select>
                            <select name="year">
                                @for ($y = now()->year - 2; $y <= now()->year + 1; $y++)
                                    <option value="{{ $y }}" @selected($filters['year'] == $y)>{{ $y }}</option>
                                @endfor
                            </select>
                            <button class="btn" type="submit">Filter</button>
                        </form>
                    </div>
                    <div class="chart" id="monthlyChart">
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
                    <div id="monthlyDetail" style="display:none; margin-top:12px;">
                        <h4 style="margin:0 0 6px;">Most Borrowed Items</h4>
                        <ul style="margin:0; padding-left:16px;">
                            @forelse ($mostBorrowed as $row)
                                <li>{{ $row['name'] ?? '-' }} ({{ $row['total'] }})</li>
                            @empty
                                <li>No data</li>
                            @endforelse
                        </ul>
                        <h4 style="margin:12px 0 6px;">Returned Items</h4>
                        <ul style="margin:0; padding-left:16px;">
                            @forelse ($returnedItems as $row)
                                <li>{{ $row['name'] ?? '-' }} ({{ $row['total'] }})</li>
                            @empty
                                <li>No data</li>
                            @endforelse
                        </ul>
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
                                    <td>BR-{{ str_pad((string) $borrow->id, 3, '0', STR_PAD_LEFT) }}</td>
                                    <td>{{ $borrow->user->name }}</td>
                                    <td>{{ $borrow->asset->asset_name }}</td>
                                    <td>{{ $borrow->borrow_date->format('d-m-Y') }}</td>
                                    <td>{{ $borrow->due_date->format('d-m-Y') }}</td>
                                    <td>
                                        @php
                                            $statusClass = strtolower(str_replace(' ', '', $borrow->status));
                                        @endphp
                                        <span class="status {{ $statusClass }}">{{ $borrow->status }}</span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <div style="margin-top:8px;">
                        <a class="btn" href="{{ route('borrow.active') }}" style="text-decoration:none;">View All</a>
                    </div>
                </div>
                <div class="panel" style="grid-column: 1 / -1;">
                    <h3>Notifications</h3>
                    <ul style="margin:0; padding-left:16px;">
                        @forelse ($notifications as $notif)
                            <li>{{ $notif->message }}</li>
                        @empty
                            <li>Belum ada notifikasi.</li>
                        @endforelse
                    </ul>
                </div>
            </section>
        </main>
    </div>
    <script>
        const chart = document.getElementById('monthlyChart');
        const detail = document.getElementById('monthlyDetail');
        if (chart && detail) {
            chart.addEventListener('click', () => {
                detail.style.display = detail.style.display === 'none' ? 'block' : 'none';
            });
        }
    </script>
</body>
</html>
