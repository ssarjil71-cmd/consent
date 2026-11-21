<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>ConsentRX Admin</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
  <style>
    body {
      font-family: 'Inter', sans-serif;
      background:#f4f6f8;
      margin:0;
      color:#0f172a;
    }

    .sidebar {
      height:100vh;
      background:#ffffff;
      color:#0f172a;
      position:fixed;
      width:240px;
      top:0;
      left:0;
      padding:20px;
      overflow-y:auto;
      border-right:1px solid #e2e8f0;
      -ms-overflow-style:none;
      scrollbar-width:none;
    }

    .sidebar::-webkit-scrollbar { display:none; }

    .sidebar h4 {
      color:#1d4ed8;
      font-weight:700;
      letter-spacing:0.18em;
    }

    .sidebar .brand {
      font-size:15px;
      color:#0f172a;
      margin-bottom:4px;
    }

    .sidebar p {
      color:#64748b;
      font-size:13px;
      margin-bottom:16px;
    }

    .sidebar h6 {
      margin-top:24px;
      font-size:16px;
      text-transform:none;
      letter-spacing:0;
      color:#0f172a;
      font-weight:700;
      display:flex;
      align-items:center;
      gap:8px;
    }

    .sidebar h6 .heading-icon {
      display:inline-flex;
      align-items:center;
      justify-content:center;
      width:32px;
      height:32px;
      border-radius:8px;
      color:#f8fafc;
      box-shadow:0 4px 10px rgba(15, 23, 42, 0.16);
    }

    .sidebar h6[data-heading="organization"] .heading-icon { background:#0f766e; }
    .sidebar h6[data-heading="transactions"] .heading-icon { background:#b45309; }
    .sidebar h6[data-heading="consents"] .heading-icon { background:#6d28d9; }
    .sidebar h6[data-heading="category"] .heading-icon { background:#0369a1; }
    .sidebar h6[data-heading="system"] .heading-icon { background:#9d174d; }
    .sidebar h6[data-heading="plans"] .heading-icon { background:#334155; }

    .sidebar a {
      display:flex;
      align-items:center;
      gap:10px;
      padding:8px 0;
      margin-left:12px;
      font-size:13px;
      color:#334155;
      text-decoration:none;
      transition:color .2s ease;
      font-weight:500;
    }

    .sidebar a:hover {
      color:#1d4ed8;
    }

    .sidebar a.active {
      color:#1d4ed8;
      font-weight:600;
    }

    .sidebar .icon-badge {
      display:inline-flex;
      align-items:center;
      justify-content:center;
      width:28px;
      height:28px;
      border-radius:10px;
      font-size:18px;
      color:#f8fafc;
      box-shadow:0 6px 14px rgba(15, 23, 42, 0.14);
      transition:background .2s ease, box-shadow .2s ease;
    }

    .sidebar .dashboard-icon {
      width:32px;
      height:32px;
      font-size:20px;
    }

    .sidebar a[data-icon="blue"] .icon-badge { background:#1d4ed8; }
    .sidebar a[data-icon="teal"] .icon-badge { background:#0f766e; }
    .sidebar a[data-icon="emerald"] .icon-badge { background:#047857; }
    .sidebar a[data-icon="indigo"] .icon-badge { background:#4c1d95; }
    .sidebar a[data-icon="amber"] .icon-badge { background:#b45309; }
    .sidebar a[data-icon="rose"] .icon-badge { background:#9d174d; }
    .sidebar a[data-icon="violet"] .icon-badge { background:#6d28d9; }
    .sidebar a[data-icon="violet-light"] .icon-badge { background:#7c3aed; }
    .sidebar a[data-icon="slate"] .icon-badge { background:#334155; }
    .sidebar a[data-icon="sky"] .icon-badge { background:#0369a1; }
    .sidebar a[data-icon="red"] .icon-badge { background:#b91c1c; }
    .sidebar a[data-icon="cyan"] .icon-badge { background:#0e7490; }
    .sidebar div[data-icon="blue"] .icon-badge { background:#1d4ed8; }
    .sidebar div[data-icon="teal"] .icon-badge { background:#0f766e; }
    .sidebar div[data-icon="emerald"] .icon-badge { background:#047857; }
    .sidebar div[data-icon="indigo"] .icon-badge { background:#4c1d95; }
    .sidebar div[data-icon="amber"] .icon-badge { background:#b45309; }
    .sidebar div[data-icon="rose"] .icon-badge { background:#9d174d; }
    .sidebar div[data-icon="violet"] .icon-badge { background:#6d28d9; }
    .sidebar div[data-icon="violet-light"] .icon-badge { background:#7c3aed; }
    .sidebar div[data-icon="slate"] .icon-badge { background:#334155; }
    .sidebar div[data-icon="sky"] .icon-badge { background:#0369a1; }
    .sidebar div[data-icon="red"] .icon-badge { background:#b91c1c; }
    .sidebar div[data-icon="cyan"] .icon-badge { background:#0e7490; }

    .sidebar a[data-icon] .icon-badge:hover,
    .sidebar a[data-icon]:hover .icon-badge,
    .sidebar a[data-icon].active .icon-badge {
      box-shadow:0 12px 22px rgba(15, 23, 42, 0.22);
    }

    .main {
      margin-left:260px;
      padding:20px;
      height:100vh;
      overflow-y:auto;
    }

    .admin-topbar {
      position:sticky;
      top:0;
      z-index:1020;
      background:#ffffff;
      border-bottom:1px solid rgba(148, 163, 184, 0.25);
    }

    .main::-webkit-scrollbar { display:none; }

    .card {
      border:none;
      border-radius:14px;
      box-shadow:0 3px 10px rgba(15, 23, 42, 0.08);
    }

    .footer {
      text-align:center;
      color:#6b7280;
      font-size:13px;
      margin-top:40px;
    }
  </style>
</head>
<body>

@include('admin.layout.sidebar')

<div class="main">
  @include('admin.layout.header')

  <div class="container-fluid mt-4">
    @yield('content')
  </div>

  <div class="footer">
    We adhere to the provisions of the Digital Personal Data Protection Act - 2023
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
@stack('scripts')
@stack('organization-scripts')
</body>
</html>
