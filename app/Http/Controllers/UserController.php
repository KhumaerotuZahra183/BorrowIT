<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\Borrow;
use App\Models\BorrowRequest;
use App\Models\Notification;
use App\Models\User;
use App\Services\Notifier;
use Illuminate\Http\Request;

class UserController extends Controller
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

    public function dashboard(Request $request)
    {
        [$authUser, $redirect] = $this->requireAuth($request);
        if ($redirect) {
            return $redirect;
        }

        $pending = BorrowRequest::where('user_id', $authUser->id)->where('status', 'Pending')->count();
        $active = Borrow::where('user_id', $authUser->id)->whereNull('returned_at')->count();
        $overdue = Borrow::where('user_id', $authUser->id)
            ->whereNull('returned_at')
            ->where('due_date', '<', now()->toDateString())
            ->count();

        $activeBorrows = Borrow::with('asset')
            ->where('user_id', $authUser->id)
            ->orderByDesc('borrow_date')
            ->limit(5)
            ->get();

        foreach ($activeBorrows as $borrow) {
            if (!$borrow->returned_at && $borrow->due_date < now()->toDateString()) {
                if ($borrow->status !== 'Overdue') {
                    $borrow->status = 'Overdue';
                    $borrow->save();
                }
                if (!$borrow->overdue_notified_at || $borrow->overdue_notified_at->toDateString() !== now()->toDateString()) {
                    $borrow->overdue_notified_at = now();
                    $borrow->save();
                    Notifier::notify($authUser, 'Overdue', 'Borrow item overdue.', route('user.borrowings'));
                }
            } elseif (!$borrow->returned_at && $borrow->status !== 'Borrow') {
                $borrow->status = 'Borrow';
                $borrow->save();
            }
        }

        return view('user.dashboard', [
            'user' => $authUser,
            'stats' => [
                'active' => $active,
                'overdue' => $overdue,
                'pending' => $pending,
            ],
            'activeBorrows' => $activeBorrows,
            'notifications' => Notification::where('user_id', $authUser->id)->orderByDesc('created_at')->limit(5)->get(),
        ]);
    }

    public function myBorrowings(Request $request)
    {
        [$authUser, $redirect] = $this->requireAuth($request);
        if ($redirect) {
            return $redirect;
        }

        $search = $request->query('search');
        $requests = BorrowRequest::with('asset')
            ->where('user_id', $authUser->id)
            ->when($search, function ($query, $search) {
                $query->where('status', 'like', "%{$search}%")
                    ->orWhereHas('asset', function ($assetQuery) use ($search) {
                        $assetQuery->where('asset_name', 'like', "%{$search}%");
                    });
            })
            ->orderByDesc('created_at')
            ->paginate(8)
            ->withQueryString();

        return view('user.borrowings', [
            'user' => $authUser,
            'requests' => $requests,
            'search' => $search,
        ]);
    }

    public function createBorrowRequest(Request $request)
    {
        [$authUser, $redirect] = $this->requireAuth($request);
        if ($redirect) {
            return $redirect;
        }

        $assets = Asset::query()->where('available', '>', 0)->orderBy('asset_name')->get();

        return view('user.borrow-request', [
            'user' => $authUser,
            'assets' => $assets,
        ]);
    }

    public function storeBorrowRequest(Request $request)
    {
        [$authUser, $redirect] = $this->requireAuth($request);
        if ($redirect) {
            return $redirect;
        }

        $validated = $request->validate([
            'asset_id' => ['required', 'exists:assets,id'],
            'request_date' => ['required', 'date'],
            'duration_days' => ['required', 'integer', 'min:1'],
            'note' => ['nullable', 'string', 'max:500'],
        ]);

        $borrowRequest = BorrowRequest::create([
            'user_id' => $authUser->id,
            'asset_id' => $validated['asset_id'],
            'request_date' => $validated['request_date'],
            'duration_days' => $validated['duration_days'],
            'status' => 'Pending',
            'note' => $validated['note'] ?? null,
        ]);

        $admins = User::where('role', 'Admin')->get();
        foreach ($admins as $admin) {
            Notifier::notify($admin, 'New Request', "New borrow request from {$authUser->name}", route('borrow.index'));
        }

        return redirect()->route('user.borrowings')->with('status', 'Request berhasil dikirim.');
    }
}
