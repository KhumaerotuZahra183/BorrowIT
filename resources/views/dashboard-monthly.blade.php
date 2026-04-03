<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Monthly Borrowing | BorrowIT</title>
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
    <link rel="stylesheet" href="{{ asset('css/dashboard-monthly.css') }}">
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
                    <h1>Monthly Borrowing</h1>
                    <p>Ringkasan peminjaman per bulan.</p>
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

            <section class="monthly-shell">
                <div class="panel monthly-main">
                    <div class="panel-header">
                        <h3>Borrowing by Month</h3>
                        <form method="GET" action="{{ route('dashboard.monthly') }}" class="panel-filters">
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
                            <button class="btn" type="submit">Apply Filter</button>
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
                </div>

                <div class="panel monthly-side">
                    <h3>Borrow by Equipment</h3>
                    @php
                        $pieData = $mostBorrowed->filter(fn ($row) => ($row['total'] ?? 0) > 0)->values();
                        $pieTotal = (int) $pieData->sum('total');
                        $colors = ['#7c3aed', '#f97316', '#14b8a6', '#22c55e', '#eab308', '#ec4899'];
                        $offset = 0;
                    @endphp
                    <div class="pie-card" style="height: 100%;">
                        <div class="pie-title">Borrow by Equipment - {{ $filters['month'] ? \Carbon\Carbon::create()->month($filters['month'])->format('F') : 'All Months' }} {{ $filters['year'] }}</div>
                        <svg class="pie" viewBox="0 0 36 36" aria-hidden="true">
                            @if ($pieTotal === 0)
                                <circle class="pie-bg" cx="18" cy="18" r="15.9155"></circle>
                            @else
                                @foreach ($pieData as $i => $row)
                                    @php
                                        $percent = ($row['total'] / $pieTotal) * 100;
                                        $dash = $percent . ' ' . (100 - $percent);
                                        $color = $colors[$i % count($colors)];
                                    @endphp
                                    <circle class="pie-slice"
                                        cx="18" cy="18" r="15.9155"
                                        stroke="{{ $color }}"
                                        stroke-dasharray="{{ $dash }}"
                                        stroke-dashoffset="{{ 25 - $offset }}"
                                    ></circle>
                                    @php $offset += $percent; @endphp
                                @endforeach
                            @endif
                        </svg>
                        <ul class="pie-legend">
                            @forelse ($pieData as $i => $row)
                                <li>
                                    <span class="legend-dot" style="background: {{ $colors[$i % count($colors)] }}"></span>
                                    <span>{{ $row['name'] ?? '-' }}</span>
                                    <strong>{{ $row['total'] }}</strong>
                                </li>
                            @empty
                                <li><span>No data</span><strong>0</strong></li>
                            @endforelse
                        </ul>
                    </div>
                </div>

                <div class="panel monthly-table">
                    <div class="panel-header" style="justify-content: space-between; display: flex; align-items: center;">
                        <h3 style="text-transform: uppercase;">Most Borrowed Items {{ $filters['month'] ? \Carbon\Carbon::create()->day(1)->month($filters['month'])->format('F') : 'All Months' }} {{ $filters['year'] }}</h3>
                        <button class="btn" style="background: #e2e8f0; color: #1e293b; border: none; font-weight: 500; font-size: 14px; padding: 8px 16px;">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="vertical-align: middle; margin-right: 4px;">
                                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                                <polyline points="7 10 12 15 17 10"></polyline>
                                <line x1="12" y1="15" x2="12" y2="3"></line>
                            </svg>
                            Export Excel
                        </button>
                    </div>
                    <table style="margin-top: 10px;">
                        <thead style="background: #f1f5f9;">
                            <tr>
                                <th style="text-align: center; border-radius: 8px 0 0 8px;">Rank</th>
                                <th>Asset Name</th>
                                <th style="border-radius: 0 8px 8px 0;">Total Borrow</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($mostBorrowed as $index => $row)
                                <tr>
                                    <td style="text-align: center; font-weight: 600;">{{ $index + 1 }}</td>
                                    <td>{{ $row['name'] ?? '-' }}</td>
                                    <td>{{ $row['total'] }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" style="text-align: center;">Belum ada data.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="panel monthly-table" style="margin-top: 2px;">
                    <div class="panel-header" style="justify-content: space-between; display: flex; align-items: center;">
                        <h3 style="text-transform: capitalize;">Returned Items {{ $filters['month'] ? \Carbon\Carbon::create()->day(1)->month($filters['month'])->format('F') : 'All Months' }} {{ $filters['year'] }}</h3>
                        <button class="btn" style="background: #e2e8f0; color: #3b82f6; border: none; font-weight: 500; font-size: 14px; padding: 8px 16px;">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="vertical-align: middle; margin-right: 4px;">
                                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                                <polyline points="7 10 12 15 17 10"></polyline>
                                <line x1="12" y1="15" x2="12" y2="3"></line>
                            </svg>
                            Export Excel
                        </button>
                    </div>
                    <table style="margin-top: 10px;">
                        <thead style="background: #e2e8f0; border-radius: 8px;">
                            <tr>
                                <th style="text-align: center; border-radius: 8px 0 0 8px;">No</th>
                                <th>Asset Name</th>
                                <th>Name</th>
                                <th>Borrow Date</th>
                                <th>Due Date</th>
                                <th>PIC Receiver</th>
                                <th style="border-radius: 0 8px 8px 0;">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($returnedItems as $index => $row)
                                <tr>
                                    <td style="text-align: center; font-weight: 600;">{{ $index + 1 }}</td>
                                    <td style="font-weight: 500;">{{ optional($row->asset)->asset_name }}</td>
                                    <td>{{ optional($row->user)->name }}</td>
                                    <td>{{ $row->borrow_date->format('d-m-Y') }}</td>
                                    <td>{{ $row->due_date->format('d-m-Y') }}</td>
                                    <td>{{ $row->return_pic ?? '-' }}</td>
                                    <td style="color: #3b82f6;">Returned</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" style="text-align: center;">Belum ada data.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="panel monthly-table" style="margin-top: 2px;">
                    <div class="panel-header">
                        <h3>Borrow Detail</h3>
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
                            @forelse ($borrows as $borrow)
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
                            @empty
                                <tr>
                                    <td colspan="6">Belum ada data.</td>
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
                </div>
            </section>
        </main>
    </div>
</body>
</html>
