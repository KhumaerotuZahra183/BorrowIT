<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Return Asset | BorrowIT</title>
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
                <a href="{{ route('notifications.index') }}">Notification</a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="btn" type="submit">Logout</button>
                </form>
            </nav>
        </aside>

        <main class="content">
            <div class="topbar">
                <h1>Return Asset</h1>
                <div class="topbar-actions">
                    <a class="notif-button" href="{{ route('notifications.index') }}" aria-label="Notification">
                        <svg class="notif-icon" viewBox="0 0 24 24" aria-hidden="true" focusable="false">
                            <path d="M12 22a2.5 2.5 0 0 0 2.45-2h-4.9A2.5 2.5 0 0 0 12 22Zm7-6V11a7 7 0 0 0-5-6.71V3a2 2 0 0 0-4 0v1.29A7 7 0 0 0 5 11v5l-2 2v1h18v-1l-2-2Z" fill="currentColor"/>
                        </svg>
                    </a>
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
                <form method="POST" action="{{ route('borrow.return.store', $borrow) }}">
                    @csrf
                    <table>
                        <tr>
                            <th>Borrow ID</th>
                            <td>BR-{{ str_pad((string) $borrow->id, 3, '0', STR_PAD_LEFT) }}</td>
                        </tr>
                        <tr>
                            <th>User</th>
                            <td>{{ $borrow->user->name }}</td>
                        </tr>
                        <tr>
                            <th>Asset</th>
                            <td>{{ $borrow->asset->asset_name }}</td>
                        </tr>
                        <tr>
                            <th>Return PIC</th>
                            <td><input type="text" name="return_pic" value="{{ old('return_pic') }}" required></td>
                        </tr>
                    </table>
                    <div style="margin-top:12px;">
                        <button class="btn" type="submit">Save</button>
                        <a class="btn" href="{{ route('borrow.active') }}" style="text-decoration:none;">Cancel</a>
                    </div>
                </form>
            </section>
        </main>
    </div>
</body>
</html>
