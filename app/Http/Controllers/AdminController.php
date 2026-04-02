<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    private function requireAuth(Request $request)
    {
        $userId = $request->session()->get('auth_user_id');
        if (!$userId) {
            return [null, redirect()->route('login')];
        }

        $user = User::find($userId);
        if (!$user) {
            $request->session()->forget('auth_user_id');
            return [null, redirect()->route('login')];
        }

        return [$user, null];
    }

    public function users(Request $request)
    {
        [$authUser, $redirect] = $this->requireAuth($request);
        if ($redirect) {
            return $redirect;
        }

        $search = $request->query('search');
        $users = User::query()
            ->when($search, function ($query, $search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('department', 'like', "%{$search}%")
                    ->orWhere('role', 'like', "%{$search}%");
            })
            ->orderBy('name')
            ->paginate(6)
            ->withQueryString();

        return view('users.index', [
            'user' => $authUser,
            'users' => $users,
            'search' => $search,
        ]);
    }

    public function createUser(Request $request)
    {
        [$authUser, $redirect] = $this->requireAuth($request);
        if ($redirect) {
            return $redirect;
        }

        return view('users.create', [
            'user' => $authUser,
        ]);
    }

    public function storeUser(Request $request)
    {
        [$authUser, $redirect] = $this->requireAuth($request);
        if ($redirect) {
            return $redirect;
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'department' => ['nullable', 'string', 'max:255'],
            'role' => ['required', 'string', 'max:50'],
            'status' => ['required', 'string', 'max:50'],
            'password' => ['required', 'string', 'min:6'],
        ]);

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'department' => $validated['department'],
            'role' => $validated['role'],
            'status' => $validated['status'],
            'password' => Hash::make($validated['password']),
        ]);

        return redirect()->route('users.index')->with('status', 'User berhasil ditambahkan.');
    }

    public function editUser(Request $request, User $user)
    {
        [$authUser, $redirect] = $this->requireAuth($request);
        if ($redirect) {
            return $redirect;
        }

        return view('users.edit', [
            'user' => $authUser,
            'editUser' => $user,
        ]);
    }

    public function updateUser(Request $request, User $user)
    {
        [$authUser, $redirect] = $this->requireAuth($request);
        if ($redirect) {
            return $redirect;
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'department' => ['nullable', 'string', 'max:255'],
            'role' => ['required', 'string', 'max:50'],
            'status' => ['required', 'string', 'max:50'],
            'password' => ['nullable', 'string', 'min:6'],
        ]);

        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->department = $validated['department'];
        $user->role = $validated['role'];
        $user->status = $validated['status'];
        if (!empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }
        $user->save();

        return redirect()->route('users.index')->with('status', 'User berhasil diupdate.');
    }

    public function destroyUser(Request $request, User $user)
    {
        [$authUser, $redirect] = $this->requireAuth($request);
        if ($redirect) {
            return $redirect;
        }

        $user->delete();

        return redirect()->route('users.index')->with('status', 'User berhasil dihapus.');
    }

    public function assets(Request $request)
    {
        [$authUser, $redirect] = $this->requireAuth($request);
        if ($redirect) {
            return $redirect;
        }

        $search = $request->query('search');
        $assets = Asset::query()
            ->when($search, function ($query, $search) {
                $query->where('asset_id', 'like', "%{$search}%")
                    ->orWhere('asset_number', 'like', "%{$search}%")
                    ->orWhere('asset_name', 'like', "%{$search}%");
            })
            ->orderBy('asset_name')
            ->paginate(6)
            ->withQueryString();

        return view('assets.index', [
            'user' => $authUser,
            'assets' => $assets,
            'search' => $search,
        ]);
    }

    public function createAsset(Request $request)
    {
        [$authUser, $redirect] = $this->requireAuth($request);
        if ($redirect) {
            return $redirect;
        }

        return view('assets.create', [
            'user' => $authUser,
        ]);
    }

    public function storeAsset(Request $request)
    {
        [$authUser, $redirect] = $this->requireAuth($request);
        if ($redirect) {
            return $redirect;
        }

        $validated = $request->validate([
            'asset_id' => ['required', 'string', 'max:100', 'unique:assets,asset_id'],
            'asset_number' => ['required', 'string', 'max:100', 'unique:assets,asset_number'],
            'asset_name' => ['required', 'string', 'max:255'],
            'available' => ['required', 'integer', 'min:0'],
        ]);

        Asset::create($validated);

        return redirect()->route('assets.index')->with('status', 'Asset berhasil ditambahkan.');
    }

    public function editAsset(Request $request, Asset $asset)
    {
        [$authUser, $redirect] = $this->requireAuth($request);
        if ($redirect) {
            return $redirect;
        }

        return view('assets.edit', [
            'user' => $authUser,
            'asset' => $asset,
        ]);
    }

    public function updateAsset(Request $request, Asset $asset)
    {
        [$authUser, $redirect] = $this->requireAuth($request);
        if ($redirect) {
            return $redirect;
        }

        $validated = $request->validate([
            'asset_id' => ['required', 'string', 'max:100', 'unique:assets,asset_id,' . $asset->id],
            'asset_number' => ['required', 'string', 'max:100', 'unique:assets,asset_number,' . $asset->id],
            'asset_name' => ['required', 'string', 'max:255'],
            'available' => ['required', 'integer', 'min:0'],
        ]);

        $asset->update($validated);

        return redirect()->route('assets.index')->with('status', 'Asset berhasil diupdate.');
    }

    public function destroyAsset(Request $request, Asset $asset)
    {
        [$authUser, $redirect] = $this->requireAuth($request);
        if ($redirect) {
            return $redirect;
        }

        $asset->delete();

        return redirect()->route('assets.index')->with('status', 'Asset berhasil dihapus.');
    }

    public function borrowRequests(Request $request)
    {
        [$authUser, $redirect] = $this->requireAuth($request);
        if ($redirect) {
            return $redirect;
        }

        $search = $request->query('search');
        $requests = [
            [
                'id' => 'BR-001',
                'user' => 'Zahra',
                'asset' => 'Mouse',
                'borrow_date' => '01-01-2026',
                'duration' => '3 Days',
                'status' => 'Pending',
                'approve_date' => '-',
            ],
            [
                'id' => 'BR-002',
                'user' => 'Zahra',
                'asset' => 'Keyboard',
                'borrow_date' => '02-02-2026',
                'duration' => '1 Day',
                'status' => 'Approved',
                'approve_date' => '01-01-2026',
            ],
            [
                'id' => 'BR-003',
                'user' => 'Zahra',
                'asset' => 'Keyboard',
                'borrow_date' => '02-02-2026',
                'duration' => '3 Days',
                'status' => 'On Loan',
                'approve_date' => '01-01-2026',
            ],
        ];

        if ($search) {
            $requests = array_values(array_filter($requests, function ($item) use ($search) {
                return str_contains(strtolower($item['id']), strtolower($search))
                    || str_contains(strtolower($item['user']), strtolower($search))
                    || str_contains(strtolower($item['asset']), strtolower($search));
            }));
        }

        $counts = [
            'pending' => count(array_filter($requests, fn ($row) => strtolower($row['status']) === 'pending')),
            'approved' => count(array_filter($requests, fn ($row) => strtolower($row['status']) === 'approved')),
            'on_loan' => count(array_filter($requests, fn ($row) => strtolower($row['status']) === 'on loan')),
        ];

        return view('borrow-requests.index', [
            'user' => $authUser,
            'requests' => $requests,
            'search' => $search,
            'counts' => $counts,
            'pages' => [1, 2, 3],
            'currentPage' => (int) ($request->query('page', 1)),
        ]);
    }

    public function activeBorrows(Request $request)
    {
        [$authUser, $redirect] = $this->requireAuth($request);
        if ($redirect) {
            return $redirect;
        }

        $search = $request->query('search');
        $borrows = [
            [
                'id' => 'BR-010',
                'request_id' => 'BR-001',
                'user' => 'Zahra',
                'asset' => 'Mouse',
                'borrow_date' => '01-01-2026',
                'due_date' => '04-01-2026',
                'status' => 'On Loan',
                'handover_pic' => 'Staff IT',
                'return_pic' => 'Staff IT',
            ],
            [
                'id' => 'BR-011',
                'request_id' => 'BR-001',
                'user' => 'Zahra',
                'asset' => 'Remote',
                'borrow_date' => '01-01-2026',
                'due_date' => '04-01-2026',
                'status' => 'Overdue',
                'handover_pic' => 'Staff IT',
                'return_pic' => 'Staff IT',
            ],
            [
                'id' => 'BR-012',
                'request_id' => 'BR-001',
                'user' => 'Zahra',
                'asset' => 'Projector',
                'borrow_date' => '01-01-2026',
                'due_date' => '04-01-2026',
                'status' => 'Returned',
                'handover_pic' => 'Staff IT',
                'return_pic' => 'Staff IT',
            ],
        ];

        if ($search) {
            $borrows = array_values(array_filter($borrows, function ($item) use ($search) {
                return str_contains(strtolower($item['id']), strtolower($search))
                    || str_contains(strtolower($item['user']), strtolower($search))
                    || str_contains(strtolower($item['asset']), strtolower($search));
            }));
        }

        return view('active-borrows.index', [
            'user' => $authUser,
            'borrows' => $borrows,
            'search' => $search,
            'pages' => [1, 2, 3],
            'currentPage' => (int) ($request->query('page', 1)),
        ]);
    }
}
