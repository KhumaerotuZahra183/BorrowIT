<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\Borrow;
use App\Models\BorrowRequest;
use App\Models\User;
use App\Services\Notifier;
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

        if (($user->role ?? 'User') !== 'Admin') {
            return [null, redirect()->route('user.dashboard')];
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
            'asset_number' => ['required', 'string', 'max:30', 'unique:assets,asset_number'],
            'asset_name' => ['required', 'string', 'max:255'],
            'available' => ['required', 'integer', 'min:0'],
        ]);

        $validated['asset_id'] = Asset::generateAssetId();
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
            'asset_number' => ['required', 'string', 'max:30', 'unique:assets,asset_number,' . $asset->id],
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
        $requests = BorrowRequest::with(['user', 'asset'])
            ->when($search, function ($query, $search) {
                $query->where('status', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($userQuery) use ($search) {
                        $userQuery->where('name', 'like', "%{$search}%");
                    })
                    ->orWhereHas('asset', function ($assetQuery) use ($search) {
                        $assetQuery->where('asset_name', 'like', "%{$search}%");
                    });
            })
            ->orderByDesc('created_at')
            ->paginate(8)
            ->withQueryString();

        $counts = [
            'pending' => BorrowRequest::where('status', 'Pending')->count(),
            'approved' => BorrowRequest::where('status', 'Approved')->count(),
            'on_loan' => BorrowRequest::where('status', 'Borrow')->count(),
        ];

        return view('borrow-requests.index', [
            'user' => $authUser,
            'requests' => $requests,
            'search' => $search,
            'counts' => $counts,
        ]);
    }

    public function approveRequest(Request $request, BorrowRequest $borrowRequest)
    {
        [$authUser, $redirect] = $this->requireAuth($request);
        if ($redirect) {
            return $redirect;
        }

        if ($borrowRequest->status !== 'Pending') {
            return back();
        }

        $borrowRequest->status = 'Approved';
        $borrowRequest->approve_date = now()->toDateString();
        $borrowRequest->save();

        Notifier::notify($borrowRequest->user, 'Request Approved', 'Request approved. Please wait for hand over.', route('user.borrowings'));

        return back();
    }

    public function rejectRequest(Request $request, BorrowRequest $borrowRequest)
    {
        [$authUser, $redirect] = $this->requireAuth($request);
        if ($redirect) {
            return $redirect;
        }

        if ($borrowRequest->status !== 'Pending') {
            return back();
        }

        $borrowRequest->status = 'Rejected';
        $borrowRequest->approve_date = now()->toDateString();
        $borrowRequest->save();

        Notifier::notify($borrowRequest->user, 'Request Rejected', 'Request rejected by admin.', route('user.borrowings'));

        return back();
    }

    public function handoverForm(Request $request, BorrowRequest $borrowRequest)
    {
        [$authUser, $redirect] = $this->requireAuth($request);
        if ($redirect) {
            return $redirect;
        }

        return view('borrow-requests.handover', [
            'user' => $authUser,
            'borrowRequest' => $borrowRequest,
        ]);
    }

    public function handoverStore(Request $request, BorrowRequest $borrowRequest)
    {
        [$authUser, $redirect] = $this->requireAuth($request);
        if ($redirect) {
            return $redirect;
        }

        $validated = $request->validate([
            'handover_pic' => ['required', 'string', 'max:255'],
            'borrow_date' => ['required', 'date'],
        ]);

        $asset = $borrowRequest->asset;
        if ($asset->available <= 0) {
            return back()->withErrors(['handover_pic' => 'Stok asset tidak tersedia.']);
        }

        $dueDate = date('Y-m-d', strtotime($validated['borrow_date'] . ' +' . $borrowRequest->duration_days . ' days'));

        $borrowRequest->status = 'Borrow';
        $borrowRequest->handover_pic = $validated['handover_pic'];
        $borrowRequest->save();

        Borrow::create([
            'borrow_request_id' => $borrowRequest->id,
            'user_id' => $borrowRequest->user_id,
            'asset_id' => $borrowRequest->asset_id,
            'borrow_date' => $validated['borrow_date'],
            'due_date' => $dueDate,
            'status' => 'Borrow',
            'handover_pic' => $validated['handover_pic'],
        ]);

        $asset->available = max(0, $asset->available - 1);
        $asset->save();

        Notifier::notify($borrowRequest->user, 'Hand Over', 'Asset sudah diserahkan.', route('user.borrowings'));

        return redirect()->route('borrow.index');
    }

    public function activeBorrows(Request $request)
    {
        [$authUser, $redirect] = $this->requireAuth($request);
        if ($redirect) {
            return $redirect;
        }

        $search = $request->query('search');
        $borrows = Borrow::with(['user', 'asset', 'request'])
            ->when($search, function ($query, $search) {
                $query->where('status', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($userQuery) use ($search) {
                        $userQuery->where('name', 'like', "%{$search}%");
                    })
                    ->orWhereHas('asset', function ($assetQuery) use ($search) {
                        $assetQuery->where('asset_name', 'like', "%{$search}%");
                    });
            })
            ->orderByDesc('borrow_date')
            ->paginate(8)
            ->withQueryString();

        foreach ($borrows as $borrow) {
            if (!$borrow->returned_at && $borrow->due_date < now()->toDateString()) {
                if ($borrow->status !== 'Overdue') {
                    $borrow->status = 'Overdue';
                    $borrow->save();
                }
                if (!$borrow->overdue_notified_at || $borrow->overdue_notified_at->toDateString() !== now()->toDateString()) {
                    $borrow->overdue_notified_at = now();
                    $borrow->save();
                    Notifier::notify($borrow->user, 'Overdue', 'Borrow item overdue.', route('user.borrowings'));
                    $admins = User::where('role', 'Admin')->get();
                    foreach ($admins as $admin) {
                        Notifier::notify($admin, 'Overdue', 'User overdue detected.', route('borrow.active'));
                    }
                }
            } elseif (!$borrow->returned_at && $borrow->status !== 'Borrow') {
                $borrow->status = 'Borrow';
                $borrow->save();
            }
        }

        return view('active-borrows.index', [
            'user' => $authUser,
            'borrows' => $borrows,
            'search' => $search,
        ]);
    }

    public function returnForm(Request $request, Borrow $borrow)
    {
        [$authUser, $redirect] = $this->requireAuth($request);
        if ($redirect) {
            return $redirect;
        }

        return view('active-borrows.return', [
            'user' => $authUser,
            'borrow' => $borrow,
        ]);
    }

    public function returnStore(Request $request, Borrow $borrow)
    {
        [$authUser, $redirect] = $this->requireAuth($request);
        if ($redirect) {
            return $redirect;
        }

        $validated = $request->validate([
            'return_pic' => ['required', 'string', 'max:255'],
        ]);

        if (!$borrow->returned_at) {
            $borrow->status = 'Returned';
            $borrow->return_pic = $validated['return_pic'];
            $borrow->returned_at = now();
            $borrow->save();

            $asset = $borrow->asset;
            $asset->available += 1;
            $asset->save();

            $borrow->request->status = 'Returned';
            $borrow->request->save();

            Notifier::notify($borrow->user, 'Returned', 'Asset sudah dikembalikan.', route('user.borrowings'));
            $admins = User::where('role', 'Admin')->get();
            foreach ($admins as $admin) {
                Notifier::notify($admin, 'Returned', 'User returned asset.', route('borrow.active'));
            }
        }

        return redirect()->route('borrow.active');
    }
}
