<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Asset | BorrowIT</title>
    <link rel="stylesheet" href="{{ asset('css/asset-management.css') }}">
</head>
<body>
    <div class="shell">
        <aside class="sidebar">
            <div class="brand">Borrow<span>IT</span></div>
            <nav class="nav">
                <a href="{{ route('dashboard') }}">Dashboard</a>
                <a href="{{ route('users.index') }}">Manage Users</a>
                <a class="active" href="{{ route('assets.index') }}">Asset Management</a>
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
                <h1>Edit Asset</h1>
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
                @if ($errors->any())
                    <div class="alert">{{ $errors->first() }}</div>
                @endif
                <form method="POST" action="{{ route('assets.update', $asset) }}">
                    @csrf
                    @method('PUT')
                    <table>
                        <tr>
                            <th>Asset ID</th>
                            <td><input type="text" name="asset_id" value="{{ old('asset_id', $asset->asset_id) }}" readonly></td>
                        </tr>
                        <tr>
                            <th>Asset Number</th>
                            <td><input type="text" name="asset_number" value="{{ old('asset_number', $asset->asset_number) }}" required></td>
                        </tr>
                        <tr>
                            <th>Asset Name</th>
                            <td><input type="text" name="asset_name" value="{{ old('asset_name', $asset->asset_name) }}" required></td>
                        </tr>
                        <tr>
                            <th>Available</th>
                            <td><input type="number" name="available" value="{{ old('available', $asset->available) }}" min="0" required></td>
                        </tr>
                    </table>
                    <div style="margin-top:12px;">
                        <button class="btn" type="submit">Update</button>
                        <a class="btn" href="{{ route('assets.index') }}" style="text-decoration:none;">Cancel</a>
                    </div>
                </form>
            </section>
        </main>
    </div>
</body>
</html>
