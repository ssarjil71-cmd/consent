<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Organization Login</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <style>
    body{font-family:system-ui,Arial;margin:0;background:#f6f7fb}
    .card{max-width:380px;margin:8vh auto;background:#fff;padding:24px;border-radius:14px;box-shadow:0 10px 30px rgba(0,0,0,.08)}
    input,button,select{width:100%;padding:12px;margin:8px 0;border:1px solid #e5e7eb;border-radius:10px}
    button{border:none;font-weight:600;cursor:pointer}
    .btn{background:#111827;color:#fff}
    .err{color:#b91c1c;font-size:14px}
    .muted{color:#6b7280;font-size:14px}
  </style>
</head>
<body>
  <div class="card">
    <h2 style="margin-top:0">Organization Login</h2>
    <div class="muted">Sign in to your organization portal</div>

    @if ($errors->any())
      <div class="err" style="margin-top:8px">{{ $errors->first() }}</div>
    @endif

    <form method="POST" action="{{ route('org.login.submit') }}">
      @csrf
      <select name="organization_id" required>
        <option value="">Select Organization</option>
        @if(isset($organizations))
          @foreach($organizations as $organization)
            <option value="{{ $organization->id }}">{{ $organization->name }}</option>
          @endforeach
        @endif
      </select>
      <input type="email" name="email" placeholder="Email" value="{{ old('email') }}" required>
      <input type="password" name="password" placeholder="Password" required>
      <div style="display: flex; justify-content: space-between; align-items: center; margin: 8px 0;">
        <label style="display:flex;align-items:center;gap:8px">
          <input type="checkbox" name="remember" style="width:auto"> Remember me
        </label>
        <a href="{{ route('org.password.request') }}" style="font-size: 14px; color: #4b5563; text-decoration: none;">
          Forgot Password?
        </a>
      </div>
      <button class="btn" type="submit">Login</button>
    </form>
  </div>
</body>
</html>
