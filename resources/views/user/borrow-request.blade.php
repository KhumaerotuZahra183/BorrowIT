<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Borrow Request | BorrowIT</title>
    <link rel="stylesheet" href="{{ asset('css/manage-users.css') }}">
</head>
<body>
    <div class="shell">
        <aside class="sidebar">
            <div class="brand">Borrow<span>IT</span></div>
            <nav class="nav">
                <a href="{{ route('user.dashboard') }}">Dashboard</a>
                <a class="active" href="{{ route('user.borrowings') }}">My Borrowings</a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="btn" type="submit">Logout</button>
                </form>
            </nav>
        </aside>

        <main class="content">
            <div class="topbar">
                <h1>New Borrow Request</h1>
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
                @if ($errors->any())
                    <div class="alert">{{ $errors->first() }}</div>
                @endif
                <form method="POST" action="{{ route('user.borrowings.store') }}">
                    @csrf
                    <table>
                        <tr>
                            <th>Asset</th>
                            <td>
                                <select name="asset_id" required>
                                    <option value="">Select Asset</option>
                                    @foreach ($assets as $asset)
                                        <option value="{{ $asset->id }}">{{ $asset->asset_name }} ({{ $asset->asset_id }})</option>
                                    @endforeach
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <th>Request Date</th>
                            <td><input type="date" name="request_date" value="{{ old('request_date', now()->toDateString()) }}" required></td>
                        </tr>
                        <tr>
                            <th>Duration (days)</th>
                            <td><input type="number" name="duration_days" min="1" value="{{ old('duration_days', 1) }}" required></td>
                        </tr>
                        <tr>
                            <th>Note</th>
                            <td><input type="text" name="note" value="{{ old('note') }}"></td>
                        </tr>
                    </table>
                    <div style="margin-top:12px;">
                        <button class="btn" type="submit">Submit</button>
                        <a class="btn" href="{{ route('user.borrowings') }}" style="text-decoration:none;">Cancel</a>
                    </div>
                </form>
            </section>
        </main>
    </div>
</body>
</html>
