<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit User | BorrowIT</title>
    <link rel="stylesheet" href="{{ asset('css/manage-users.css') }}">
</head>
<body>
    <div class="shell">
        <aside class="sidebar">
            <div class="brand">Borrow<span>IT</span></div>
            <nav class="nav">
                <a href="{{ route('dashboard') }}">Dashboard</a>
                <a class="active" href="{{ route('users.index') }}">Manage Users</a>
                <a href="{{ route('assets.index') }}">Asset Management</a>
                <a href="{{ route('borrow.index') }}">Borrow Request</a>
                <a href="{{ route('borrow.active') }}">Active Borrow</a>
            </nav>
        </aside>

        <main class="content">
            <div class="topbar">
                <h1>Edit User</h1>
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
                <form method="POST" action="{{ route('users.update', $editUser) }}">
                    @csrf
                    @method('PUT')
                    <table>
                        <tr>
                            <th>Name</th>
                            <td><input type="text" name="name" value="{{ old('name', $editUser->name) }}" required></td>
                        </tr>
                        <tr>
                            <th>Email</th>
                            <td><input type="email" name="email" value="{{ old('email', $editUser->email) }}" required></td>
                        </tr>
                        <tr>
                            <th>Department</th>
                            <td><input type="text" name="department" value="{{ old('department', $editUser->department) }}"></td>
                        </tr>
                        <tr>
                            <th>Role</th>
                            <td><input type="text" name="role" value="{{ old('role', $editUser->role ?? 'User') }}" required></td>
                        </tr>
                        <tr>
                            <th>Status</th>
                            <td><input type="text" name="status" value="{{ old('status', $editUser->status ?? 'Active') }}" required></td>
                        </tr>
                        <tr>
                            <th>Password</th>
                            <td><input type="password" name="password" placeholder="Biarkan kosong jika tidak berubah"></td>
                        </tr>
                    </table>
                    <div style="margin-top:12px;">
                        <button class="btn" type="submit">Update</button>
                        <a class="btn" href="{{ route('users.index') }}" style="text-decoration:none;">Cancel</a>
                    </div>
                </form>
            </section>
        </main>
    </div>
</body>
</html>
