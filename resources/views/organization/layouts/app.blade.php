<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>{{ $organization->name ?? 'Organization' }} • ConsentRX</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="preconnect" href="https://fonts.googleapis.com"><link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
  <style>
    body{font-family:Inter,system-ui,Arial}
    .scrollbar-hide::-webkit-scrollbar { display: none; }
    .scrollbar-hide { -ms-overflow-style: none; scrollbar-width: none; }
    .sidebar-icon svg { stroke-width: 1.8px; fill: none; }
  </style>
  @stack('styles')
</head>
<body class="bg-gray-50">
  <div class="h-screen flex overflow-hidden">
    <!-- Sidebar -->
    <aside class="hidden w-72 shrink-0 border-r border-gray-200 bg-white md:flex md:h-screen md:flex-col md:overflow-hidden">
      @php
        $navLinks = [
          ['label' => 'Dashboard', 'route' => route('org.dashboard'), 'pattern' => 'org.dashboard', 'icon' => 'chart-bar'],
          ['label' => 'Categories', 'route' => route('org.categories.index'), 'pattern' => 'org.categories.*', 'icon' => 'collection'],
          ['label' => 'Consents', 'route' => route('org.consents.index'), 'pattern' => 'org.consents.*', 'icon' => 'document'],
          ['label' => 'Risk Consent', 'route' => route('org.risk-consents.index'), 'pattern' => 'org.risk-consents.*', 'icon' => 'shield'],
          ['label' => 'Add Risk Consent', 'route' => route('org.risk-consents.create'), 'pattern' => 'org.risk-consents.create', 'icon' => 'plus-circle', 'child' => true],
          ['label' => 'All Risk Consents', 'route' => route('org.risk-consents.index'), 'pattern' => 'org.risk-consents.index', 'icon' => 'clipboard-list', 'child' => true],
          ['label' => 'Persons', 'route' => route('org.persons.index'), 'pattern' => 'org.persons.*', 'icon' => 'users'],
          ['label' => 'All Persons', 'route' => route('org.persons.index'), 'pattern' => 'org.persons.index', 'icon' => 'users', 'child' => true],
          ['label' => 'Add Person', 'route' => route('org.persons.create'), 'pattern' => 'org.persons.create', 'icon' => 'user-plus', 'child' => true],
          ['label' => 'Transactions', 'route' => route('org.transactions.index'), 'pattern' => 'org.transactions.*', 'icon' => 'credit-card'],
          ['label' => 'Plan Details', 'route' => '#', 'pattern' => null, 'icon' => 'plan'],
          ['label' => 'Profile', 'route' => route('org.profile'), 'pattern' => 'org.profile', 'icon' => 'user-circle'],
        ];

        $iconFor = function (string $name, string $classes = 'h-5 w-5') {
          return match ($name) {
            'chart-bar'   => '<svg xmlns="http://www.w3.org/2000/svg" class="'.$classes.'" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 3v18h18" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 17V9m4 8v-4m4 4V5" /></svg>',
            'collection'  => '<svg xmlns="http://www.w3.org/2000/svg" class="'.$classes.'" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 6h16M4 10h16M4 14h10" /></svg>',
            'document'    => '<svg xmlns="http://www.w3.org/2000/svg" class="'.$classes.'" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 3h7l5 5v13a1 1 0 01-1 1H7a1 1 0 01-1-1V4a1 1 0 011-1z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M14 3v4h4" /></svg>',
            'users'       => '<svg xmlns="http://www.w3.org/2000/svg" class="'.$classes.'" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20v-2a4 4 0 00-4-4H7a4 4 0 00-4 4v2" /><circle cx="9" cy="7" r="4" stroke-width="1.5" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M23 20v-2a4 4 0 00-3-3.87" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 3.13A4 4 0 0118 7" /></svg>',
            'user-plus'   => '<svg xmlns="http://www.w3.org/2000/svg" class="'.$classes.'" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 14a7 7 0 00-7 7" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11v6m3-3h-6" /></svg>',
            'credit-card' => '<svg xmlns="http://www.w3.org/2000/svg" class="'.$classes.'" fill="none" viewBox="0 0 24 24" stroke="currentColor"><rect width="18" height="12" x="3" y="6" rx="2" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 10h18" /></svg>',
            'shield'       => '<svg xmlns="http://www.w3.org/2000/svg" class="'.$classes.'" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 3l7 4v5a9 9 0 01-7 8.66A9 9 0 015 12V7l7-4z" /></svg>',
            'plus-circle'  => '<svg xmlns="http://www.w3.org/2000/svg" class="'.$classes.'" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 9v6m3-3H9m11 0a8 8 0 11-16 0 8 8 0 0116 0z" /></svg>',
            'clipboard-list'=> '<svg xmlns="http://www.w3.org/2000/svg" class="'.$classes.'" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5h6M9 3h6a2 2 0 012 2v1h1a2 2 0 012 2v11a2 2 0 01-2 2H7a2 2 0 01-2-2V8a2 2 0 012-2h1V5a2 2 0 012-2z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h3" /></svg>',
            'sparkles'    => '<svg xmlns="http://www.w3.org/2000/svg" class="'.$classes.'" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 3v3m0 12v3m9-9h-3M6 12H3m15.364 6.364l-2.121-2.121m0-8.486l2.121-2.121M6.636 6.636L8.757 8.757m0 6.486l-2.121 2.121" /></svg>',
            'plan'        => '<svg xmlns="http://www.w3.org/2000/svg" class="'.$classes.'" fill="none" viewBox="0 0 24 24" stroke="currentColor"><rect x="3" y="4" width="8" height="7" rx="1.5"/><rect x="13" y="4" width="8" height="7" rx="1.5"/><rect x="3" y="13" width="8" height="7" rx="1.5"/><rect x="13" y="13" width="8" height="7" rx="1.5"/></svg>',
            'user-circle' => '<svg xmlns="http://www.w3.org/2000/svg" class="'.$classes.'" fill="none" viewBox="0 0 24 24" stroke="currentColor"><circle cx="12" cy="12" r="9"/><circle cx="12" cy="9" r="3.5"/><path stroke-linecap="round" stroke-linejoin="round" d="M7 18c1.2-2.2 3-3.5 5-3.5s3.8 1.3 5 3.5"/></svg>',
            'cog'         => '<svg xmlns="http://www.w3.org/2000/svg" class="'.$classes.'" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M10.325 4.317l.367-1.385a1 1 0 011.916 0l.367 1.385a1 1 0 00.95.726h1.462a1 1 0 01.98 1.198l-.288 1.15a1 1 0 00.284.95l1.04 1.04a1 1 0 01-.707 1.707h-1.142a1 1 0 00-.95.676l-.447 1.342a1 1 0 01-1.9 0l-.447-1.342a1 1 0 00-.95-.676H8.682a1 1 0 01-.707-1.707l1.04-1.04a1 1 0 00.284-.95l-.288-1.15A1 1 0 019.99 5.043h1.462a1 1 0 00.873-.726z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /></svg>',
            default       => '<svg xmlns="http://www.w3.org/2000/svg" class="'.$classes.'" viewBox="0 0 20 20" fill="currentColor"><circle cx="10" cy="10" r="8"/></svg>',
          };
        };

        $iconStyles = [
          'chart-bar'     => 'bg-blue-600 text-white shadow-xl group-hover:bg-blue-700 group-hover:text-white',
          'collection'    => 'bg-purple-600 text-white shadow-xl group-hover:bg-purple-700 group-hover:text-white',
          'document'      => 'bg-emerald-600 text-white shadow-xl group-hover:bg-emerald-700 group-hover:text-white',
          'users'         => 'bg-teal-600 text-white shadow-xl group-hover:bg-teal-700 group-hover:text-white',
          'user-plus'     => 'bg-rose-600 text-white shadow-xl group-hover:bg-rose-700 group-hover:text-white',
          'credit-card'   => 'bg-amber-600 text-white shadow-xl group-hover:bg-amber-700 group-hover:text-white',
          'shield'        => 'bg-slate-600 text-white shadow-xl group-hover:bg-slate-700 group-hover:text-white',
          'plus-circle'   => 'bg-cyan-600 text-white shadow-xl group-hover:bg-cyan-700 group-hover:text-white',
          'clipboard-list'=> 'bg-indigo-600 text-white shadow-xl group-hover:bg-indigo-700 group-hover:text-white',
          'sparkles'      => 'bg-fuchsia-600 text-white shadow-xl group-hover:bg-fuchsia-700 group-hover:text-white',
          'plan'          => 'bg-sky-600 text-white shadow-xl group-hover:bg-sky-700 group-hover:text-white',
          'user-circle'   => 'bg-gray-700 text-white shadow-xl group-hover:bg-gray-800 group-hover:text-white',
          'cog'           => 'bg-gray-600 text-white shadow-xl group-hover:bg-gray-700 group-hover:text-white',
        ];
      @endphp

      <div class="relative overflow-hidden bg-white p-6 text-gray-800">
        <div class="relative space-y-2">
          <div class="text-xs font-semibold uppercase tracking-[0.2em] text-gray-500">ConsentRX</div>
          <div class="text-xl font-semibold text-gray-900">{{ $organization->name }}</div>
          <div class="flex items-center gap-2 text-xs text-gray-500">
            <span class="inline-flex h-6 w-6 items-center justify-center rounded-full bg-gray-200/70 text-gray-600">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="currentColor"><path d="M10 2a6 6 0 00-6 6v3a2 2 0 01-.293 1.024l-.666 1.11A1 1 0 003.89 14h12.22a1 1 0 00.85-1.553l-.666-1.11A2 2 0 0115 11V8a6 6 0 00-5-5.917V2z" /><path d="M7 15a3 3 0 006 0H7z" /></svg>
            </span>
            Secured Consent • Protected Practice
          </div>
        </div>
      </div>

      <nav class="scrollbar-hide flex-1 space-y-2 overflow-y-auto px-6 py-6">
        @foreach ($navLinks as $link)
          @php
            $isActive = $link['pattern'] ? request()->routeIs($link['pattern']) : false;
            $isChild = $link['child'] ?? false;
            $paddingClasses = $isChild ? 'pl-12 pr-4' : 'px-4';
            $stateClasses = $isActive
              ? 'text-blue-700 font-semibold bg-blue-50 ring-1 ring-blue-100'
              : 'text-slate-600 hover:bg-gray-100 hover:text-gray-900';
            $wrapperClasses = trim("group flex items-center gap-3 rounded-xl py-3 text-sm font-medium transition-all {$paddingClasses} {$stateClasses}");
            $iconWrapperBase = $isChild ? 'h-7 w-7' : 'h-9 w-9';
            if ($isActive) {
              $iconWrapperClasses = trim("flex {$iconWrapperBase} items-center justify-center rounded-lg bg-blue-700 text-white shadow-2xl");
            } else {
              $iconStyle = $iconStyles[$link['icon']] ?? 'bg-gray-100 text-gray-500 group-hover:bg-gray-200 group-hover:text-gray-700';
              $iconWrapperClasses = trim("flex {$iconWrapperBase} items-center justify-center rounded-lg transition {$iconStyle}");
            }
          @endphp
          <a href="{{ $link['route'] }}" class="{{ $wrapperClasses }}">
            <span class="{{ $iconWrapperClasses }} sidebar-icon">
              {!! $iconFor($link['icon'], 'h-6 w-6') !!}
            </span>
            <span>{{ $link['label'] }}</span>
          </a>
        @endforeach
      </nav>
    </aside>

    <!-- Main -->
    <div class="flex-1 flex flex-col overflow-hidden">
      <!-- Topbar -->
      <header class="sticky top-0 z-40 bg-white">
        <div class="max-w-6xl mx-auto flex h-16 items-center justify-between px-6">
          <div class="font-semibold text-gray-800 truncate">{{ $organization->name }}</div>
          <div class="flex items-center gap-6">
            <div class="text-sm text-gray-600">Balance: <span class="font-semibold">₹{{ number_format($organization->transaction_amount ?? 0, 2) }}</span></div>
            <form method="POST" action="{{ route('org.logout') }}">
              @csrf
              <button class="px-3 py-1.5 rounded bg-gray-900 text-white text-sm">Logout</button>
            </form>
          </div>
        </div>
      </header>

      <!-- Content -->
      <main class="scrollbar-hide flex-1 overflow-y-auto">
        <div class="max-w-6xl mx-auto px-6 py-6">
          @yield('content')
        </div>

        <!-- Footer -->
        <footer class="max-w-6xl mx-auto px-6 pb-6 text-center text-xs text-gray-500">
          We adhere to the provisions of the Digital Personal Data Protection Act - 2023.
        </footer>
      </main>
    </div>
  </div>
  @stack('scripts')
</body>
</html>
