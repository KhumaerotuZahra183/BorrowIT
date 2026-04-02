<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Change Password | BorrowIT</title>
    <link rel="stylesheet" href="{{ asset('css/change-password.css') }}">
</head>
<body>
    <div class="frame">
        <form class="card" method="POST" action="{{ route('password.change.submit') }}">
            @csrf
            <h1>Change Password</h1>
            <div class="subtitle">
                {{ $user ? 'Perbarui password akun Anda.' : 'Lupa password? Masukkan username/email lalu buat password baru.' }}
            </div>

            @if (session('status'))
                <div class="status">{{ session('status') }}</div>
            @endif

            @if ($errors->any())
                <div class="alert">{{ $errors->first() }}</div>
            @endif

            <label for="identifier">Username / Email</label>
            <input id="identifier" name="identifier" type="text" value="{{ old('identifier', $user?->email ?? '') }}" placeholder="username atau email" required>

            @if ($user)
                <label for="current_password">Current Password</label>
                <div class="password-wrap">
                    <input id="current_password" name="current_password" type="password" placeholder="••••••••">
                    <button class="toggle" type="button" data-toggle="current_password">Show</button>
                </div>
            @endif

            <label for="new_password">New Password</label>
            <div class="password-wrap">
                <input id="new_password" name="new_password" type="password" placeholder="••••••••" required>
                <button class="toggle" type="button" data-toggle="new_password">Show</button>
            </div>

            <label for="new_password_confirmation">Confirm Password</label>
            <div class="password-wrap">
                <input id="new_password_confirmation" name="new_password_confirmation" type="password" placeholder="••••••••" required>
                <button class="toggle" type="button" data-toggle="new_password_confirmation">Show</button>
            </div>

            <div class="note">Gunakan minimal 6 karakter.</div>

            <button class="btn" type="submit">Submit</button>
            <a class="back" href="{{ route('login') }}">Kembali ke Login</a>
        </form>
    </div>

    <script>
        document.querySelectorAll('[data-toggle]').forEach((button) => {
            button.addEventListener('click', () => {
                const input = document.getElementById(button.dataset.toggle);
                if (!input) return;
                const isPassword = input.getAttribute('type') === 'password';
                input.setAttribute('type', isPassword ? 'text' : 'password');
                button.textContent = isPassword ? 'Hide' : 'Show';
            });
        });
    </script>
</body>
</html>
