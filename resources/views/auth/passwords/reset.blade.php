<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>{{ $guard === 'admin' ? 'Admin' : 'Organization' }} Reset Password</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <style>
    body{font-family:system-ui,Arial;margin:0;background:#f6f7fb}
    .card{max-width:400px;margin:8vh auto;background:#fff;padding:24px;border-radius:14px;box-shadow:0 10px 30px rgba(0,0,0,.08)}
    input,button{width:100%;padding:12px;margin:8px 0;border:1px solid #e5e7eb;border-radius:10px}
    button{border:none;font-weight:600;cursor:pointer}
    .btn{background:#111827;color:#fff;margin-top:8px;}
    .err{color:#b91c1c;font-size:14px}
    .back-link{display:inline-block;margin-top:16px;color:#4b5563;text-decoration:none;}
    .form-group{margin-bottom:16px;}
    label{display:block;margin-bottom:4px;font-size:14px;color:#4b5563;}
  </style>
</head>
<body>
  <div class="card">
    <h2 style="margin-top:0">Reset Your Password</h2>
    
    @if (session('status'))
      <div style="color:#15803d;margin-bottom:16px;">{{ session('status') }}</div>
    @endif

    @if ($errors->any())
      <div class="err">{{ $errors->first() }}</div>
    @endif

    <form method="POST" action="{{ $guard === 'admin' ? route('admin.password.update') : route('org.password.update') }}">
      @csrf
      <input type="hidden" name="token" value="{{ $token }}">
      
      <div class="form-group">
        <label for="email">Email Address</label>
        <input id="email" type="email" name="email" value="{{ $email ?? old('email') }}" required autofocus>
      </div>
      
      <div class="form-group">
        <label for="password">New Password</label>
        <input id="password" type="password" name="password" required>
      </div>
      
      <div class="form-group">
        <label for="password-confirm">Confirm New Password</label>
        <input id="password-confirm" type="password" name="password_confirmation" required>
      </div>
      
      <button class="btn" type="submit">Reset Password</button>
    </form>
    
    <a href="{{ $guard === 'admin' ? route('admin.login') : route('org.login', ['organization' => 1]) }}" class="back-link">
      &larr; Back to login
    </a>
  </div>
</body>
</html>
