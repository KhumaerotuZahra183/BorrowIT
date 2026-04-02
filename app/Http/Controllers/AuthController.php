<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $validated = $request->validate([
            'identifier' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        $identifier = $validated['identifier'];
        $user = User::query()
            ->where('email', $identifier)
            ->orWhere('name', $identifier)
            ->first();

        if (!$user || !Hash::check($validated['password'], $user->password)) {
            return back()
                ->withInput(['identifier' => $identifier])
                ->withErrors(['identifier' => 'Username/email atau password salah.']);
        }

        $request->session()->put('auth_user_id', $user->id);

        if (($user->role ?? 'User') === 'Admin') {
            return redirect()->route('dashboard');
        }

        return redirect()->route('user.dashboard');
    }

    public function logout(Request $request)
    {
        $request->session()->forget('auth_user_id');

        return redirect()->route('login');
    }

    public function showChangePassword(Request $request)
    {
        $user = null;
        if ($request->session()->has('auth_user_id')) {
            $user = User::find($request->session()->get('auth_user_id'));
        }

        return view('auth.change-password', [
            'user' => $user,
        ]);
    }

    public function changePassword(Request $request)
    {
        $loggedInUserId = $request->session()->get('auth_user_id');

        $rules = [
            'identifier' => ['required', 'string'],
            'new_password' => ['required', 'string', 'min:6', 'confirmed'],
        ];

        if ($loggedInUserId) {
            $rules['current_password'] = ['required', 'string'];
        }

        $validated = $request->validate($rules);

        if ($loggedInUserId) {
            $user = User::find($loggedInUserId);
        } else {
            $user = User::query()
                ->where('email', $validated['identifier'])
                ->orWhere('name', $validated['identifier'])
                ->first();
        }

        if (!$user) {
            return back()->withErrors(['identifier' => 'Akun tidak ditemukan.']);
        }

        if ($loggedInUserId && !Hash::check($validated['current_password'], $user->password)) {
            return back()->withErrors(['current_password' => 'Password saat ini tidak cocok.']);
        }

        $user->password = Hash::make($validated['new_password']);
        $user->save();

        if (!$loggedInUserId) {
            return redirect()->route('login')->with('status', 'Password berhasil diubah. Silakan login.');
        }

        return back()->with('status', 'Password berhasil diubah.');
    }
}
