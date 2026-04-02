<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | BorrowIT</title>
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">
</head>
<body>
    <div class="frame">
        <form class="card" method="POST" action="{{ route('login.submit') }}">
            @csrf
            <h1>Login</h1>
            <div class="subtitle">BorrowIT Admin Access</div>

            @if (session('status'))
                <div class="status">{{ session('status') }}</div>
            @endif

            @if ($errors->any())
                <div class="alert">
                    {{ $errors->first() }}
                </div>
            @endif

            <label for="identifier">Username / Email</label>
            <input id="identifier" name="identifier" type="text" value="{{ old('identifier') }}" placeholder="username atau email" required>

            <label for="password">Password</label>
            <div class="password-wrap">
                <input id="password" name="password" type="password" placeholder="••••••••" required>
                <button class="toggle" type="button" data-toggle="password">Show</button>
            </div>

            <div class="actions">
                <a class="link" href="{{ route('password.change') }}">Change Password?</a>
            </div>

            <button class="btn" type="submit">Sign In</button>
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
