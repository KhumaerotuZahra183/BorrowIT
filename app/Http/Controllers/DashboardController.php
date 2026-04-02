<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\Borrow;
use App\Models\BorrowRequest;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
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

    public function index(Request $request)
    {
        [$user, $redirect] = $this->requireAuth($request);
        if ($redirect) {
            return $redirect;
        }

        if (($user->role ?? 'User') !== 'Admin') {
            return redirect()->route('user.dashboard');
        }

        $stats = [
            'total_assets' => Asset::count(),
            'available_stock' => (int) Asset::sum('available'),
            'pending_request' => BorrowRequest::where('status', 'Pending')->count(),
            'active_borrow' => Borrow::whereNull('returned_at')->count(),
        ];

        $recentRequests = BorrowRequest::with(['user', 'asset'])
            ->orderByDesc('created_at')
            ->limit(5)
            ->get();

        $activeBorrows = Borrow::with(['user', 'asset'])
            ->orderByDesc('borrow_date')
            ->limit(5)
            ->get();

        $year = (int) $request->query('year', now()->year);
        $month = (int) $request->query('month', 0);

        $chartValues = [];
        $chartLabels = [];
        for ($m = 1; $m <= 12; $m++) {
            $chartLabels[] = (string) $m;
            $chartValues[] = Borrow::whereYear('borrow_date', $year)->whereMonth('borrow_date', $m)->count();
        }

        $borrowQuery = Borrow::with('asset')->whereYear('borrow_date', $year);
        if ($month > 0) {
            $borrowQuery->whereMonth('borrow_date', $month);
        }
        $mostBorrowed = (clone $borrowQuery)
            ->selectRaw('asset_id, COUNT(*) as total')
            ->groupBy('asset_id')
            ->orderByDesc('total')
            ->limit(5)
            ->get()
            ->map(function ($row) {
                return [
                    'name' => optional($row->asset)->asset_name,
                    'total' => $row->total,
                ];
            });

        $returnedItems = (clone $borrowQuery)
            ->whereNotNull('returned_at')
            ->selectRaw('asset_id, COUNT(*) as total')
            ->groupBy('asset_id')
            ->orderByDesc('total')
            ->limit(5)
            ->get()
            ->map(function ($row) {
                return [
                    'name' => optional($row->asset)->asset_name,
                    'total' => $row->total,
                ];
            });

        return view('dashboard', [
            'user' => $user,
            'stats' => $stats,
            'recentRequests' => $recentRequests,
            'activeBorrows' => $activeBorrows,
            'notifications' => Notification::where('user_id', $user->id)->orderByDesc('created_at')->limit(5)->get(),
            'chart' => [
                'labels' => $chartLabels,
                'values' => $chartValues,
            ],
            'filters' => [
                'year' => $year,
                'month' => $month,
            ],
            'mostBorrowed' => $mostBorrowed,
            'returnedItems' => $returnedItems,
        ]);
    }
}
