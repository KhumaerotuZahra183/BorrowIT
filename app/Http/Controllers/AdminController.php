<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\User;
use Illuminate\Http\Request;

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
}
