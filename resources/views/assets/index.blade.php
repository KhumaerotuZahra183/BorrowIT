<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Asset Management | BorrowIT</title>
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
                <a href="#">Borrow Request</a>
                <a href="#">Active Borrow</a>
            </nav>
        </aside>

        <main class="content">
            <div class="topbar">
                <h1>Asset Management</h1>
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
                    <form class="search" method="GET" action="{{ route('assets.index') }}">
                        <input type="text" name="search" placeholder="Search Asset" value="{{ $search }}">
                    </form>
                    <button class="btn" type="button">+ Add New Asset</button>
                </div>

                <table>
                    <thead>
                        <tr>
                            <th>Asset ID</th>
                            <th>Asset Number</th>
                            <th>Asset Name</th>
                            <th>Available</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($assets as $row)
                            <tr>
                                <td>{{ $row->asset_id }}</td>
                                <td>{{ $row->asset_number }}</td>
                                <td>{{ $row->asset_name }}</td>
                                <td>{{ $row->available }}</td>
                                <td>
                                    <div class="actions">
                                        <button type="button" title="Edit">Edit</button>
                                        <button type="button" title="Delete">Delete</button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5">Belum ada data asset.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                <div class="pagination">
                    @foreach ($assets->getUrlRange(1, $assets->lastPage()) as $page => $url)
                        @if ($page == $assets->currentPage())
                            <span class="current">{{ $page }}</span>
                        @else
                            <a href="{{ $url }}">{{ $page }}</a>
                        @endif
                    @endforeach
                </div>
            </section>
        </main>
    </div>
</body>
</html>
