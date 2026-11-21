<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>{{ $guard === 'admin' ? 'Admin' : 'Organization' }} Password Reset</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <style>
    body{font-family:system-ui,Arial;margin:0;background:#f6f7fb}
    .card{max-width:400px;margin:8vh auto;background:#fff;padding:24px;border-radius:14px;box-shadow:0 10px 30px rgba(0,0,0,.08)}
    input,button{width:100%;padding:12px;margin:8px 0;border:1px solid #e5e7eb;border-radius:10px}
    button{border:none;font-weight:600;cursor:pointer}
    .btn{background:#111827;color:#fff}
    .err{color:#b91c1c;font-size:14px}
    .success{color:#15803d;font-size:14px;margin-bottom:16px;}
    .back-link{display:inline-block;margin-top:16px;color:#4b5563;text-decoration:none;}
  </style>
</head>
<body>
  <div class="card">
    <h2 style="margin-top:0">{{ $guard === 'admin' ? 'Admin' : 'Organization' }} Password Reset</h2>
    <div style="margin:16px 0">Enter your email address and we'll send you a link to reset your password.</div>

    @if (session('status'))
      <div class="success">{{ session('status') }}</div>
    @endif

    @if ($errors->any())
      <div class="err">{{ $errors->first() }}</div>
    @endif

    <form method="POST" action="{{ $guard === 'admin' ? route('admin.password.email') : route('org.password.email') }}">
      @csrf
      <input type="email" name="email" placeholder="Email" value="{{ old('email') }}" required autofocus>
      <button class="btn" type="submit">Send Password Reset Link</button>
    </form>
    
    <a href="{{ $guard === 'admin' ? route('admin.login') : route('org.login', ['organization' => 1]) }}" class="back-link">
      &larr; Back to login
    </a>
  </div>
</body>
</html>
