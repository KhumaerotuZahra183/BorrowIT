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
            <div class="brand"><img class="brand-logo" src="{{ asset('BIT2-removebg-preview 1.png') }}" alt="BorrowIT"></div>
            <nav class="nav">
                <a class="active" href="{{ route('dashboard') }}">Dashboard</a>
                <a href="{{ route('users.index') }}">Manage Users</a>
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
                <div>
                    <h1>Dashboard</h1>
                    <p>Welcome, {{ $user->name }}!</p>
                </div>
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
                <div class="panel recent">
                    <div class="panel-header">
                        <h3>Recent Borrow Request</h3>
                        <a class="panel-link" href="{{ route('borrow.index') }}">View All</a>
                    </div>
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
                </div>

                <div class="panel monthly">
                    <div class="panel-header">
                        <h3>Monthly Borrowing</h3>
                        <form method="GET" action="{{ route('dashboard') }}" class="panel-filters">
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
                        @php $maxValue = max($chart['values'] ?? [1]); @endphp
                        @foreach ($chart['values'] as $index => $value)
                            @php $height = (int) round(($maxValue > 0 ? ($value / $maxValue) : 0) * 150); @endphp
                            <div class="chart-item">
                                <div class="bar-col">
                                    <div class="bar-fill" style="height: {{ $height }}px;">
                                        <span class="bar-value">{{ $value }}</span>
                                    </div>
                                </div>
                                <span class="bar-label">{{ $chart['labels'][$index] ?? '' }}</span>
                            </div>
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

                <div class="panel active">
                    <div class="panel-header">
                        <h3>Active Borrow</h3>
                        <a class="panel-link" href="{{ route('borrow.active') }}">View All</a>
                    </div>
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
                                            if ($borrow->returned_at) {
                                                $displayStatus = 'Returned';
                                            } elseif ($borrow->due_date && $borrow->due_date->toDateString() < now()->toDateString()) {
                                                $displayStatus = 'Overdue';
                                            } else {
                                                $displayStatus = 'Borrow';
                                            }
                                            $statusClass = strtolower(str_replace(' ', '', $displayStatus));
                                        @endphp
                                        <span class="status {{ $statusClass }}">{{ $displayStatus }}</span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
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
