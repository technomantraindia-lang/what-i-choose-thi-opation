<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - MadhavFood</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height: 100vh; display: flex; align-items: center; justify-content: center; }
        .login-container { background: white; border-radius: 10px; box-shadow: 0 10px 25px rgba(0,0,0,0.2); padding: 40px; max-width: 400px; width: 100%; margin: 20px; }
        .login-header { text-align: center; margin-bottom: 30px; }
        .login-header h1 { color: #333; font-weight: 600; margin-bottom: 10px; }
        .login-header p { color: #666; font-size: 14px; }
        .form-control { border-radius: 5px; border: 1px solid #ddd; padding: 12px; }
        .form-control:focus { box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25); border-color: #667eea; }
        .btn-login { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border: none; color: white; padding: 12px; font-weight: 600; border-radius: 5px; width: 100%; }
        .btn-login:hover { color: white; opacity: 0.9; }
        .alert { border-radius: 5px; }
        .form-group { margin-bottom: 20px; }
        label { color: #333; font-weight: 500; margin-bottom: 8px; }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-header">
            <h1>MadhavFood</h1>
            <p>Admin Panel Login</p>
        </div>

        @if ($errors->any())
            <div class="alert alert-danger">
                {{ $errors->first() }}
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif

        <form method="POST" action="{{ route('admin.login.post') }}">
            @csrf
            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" required>
                @error('email')<span class="invalid-feedback">{{ $message }}</span>@enderror
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" required>
                @error('password')<span class="invalid-feedback">{{ $message }}</span>@enderror
            </div>

            <button type="submit" class="btn btn-login">Login</button>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
