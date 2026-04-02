<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $userId = $request->session()->get('auth_user_id');
        if (!$userId) {
            return redirect()->route('login');
        }

        $user = User::find($userId);
        if (!$user) {
            $request->session()->forget('auth_user_id');
            return redirect()->route('login');
        }

        $stats = [
            'total_assets' => 5,
            'available_stock' => 5,
            'pending_request' => 3,
            'active_borrow' => 5,
        ];

        $recentRequests = [
            [
                'id' => 'BR-001',
                'user' => 'Zahra',
                'asset' => 'Mouse',
                'borrow_date' => '01-01-2026',
                'duration' => '2 Days',
                'status' => 'Pending',
            ],
            [
                'id' => 'BR-002',
                'user' => 'Zahra',
                'asset' => 'Mouse',
                'borrow_date' => '01-01-2026',
                'duration' => '2 Days',
                'status' => 'Approved',
            ],
            [
                'id' => 'BR-003',
                'user' => 'Zahra',
                'asset' => 'Mouse',
                'borrow_date' => '01-01-2026',
                'duration' => '2 Days',
                'status' => 'Hand Over',
            ],
        ];

        $activeBorrows = [
            [
                'id' => 'BR-001',
                'user' => 'Zahra',
                'asset' => 'Mouse',
                'borrow_date' => '01-01-2026',
                'due_date' => '04-01-2026',
                'status' => 'On Loan',
            ],
            [
                'id' => 'BR-002',
                'user' => 'Zahra',
                'asset' => 'Mouse',
                'borrow_date' => '01-01-2026',
                'due_date' => '04-01-2026',
                'status' => 'Overdue',
            ],
            [
                'id' => 'BR-003',
                'user' => 'Zahra',
                'asset' => 'Mouse',
                'borrow_date' => '01-01-2026',
                'due_date' => '04-01-2026',
                'status' => 'Returned',
            ],
        ];

        $chart = [
            'labels' => ['0', '1', '2'],
            'values' => [6, 10, 13],
        ];

        return view('dashboard', [
            'user' => $user,
            'stats' => $stats,
            'recentRequests' => $recentRequests,
            'activeBorrows' => $activeBorrows,
            'chart' => $chart,
        ]);
    }
}
