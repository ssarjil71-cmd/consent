@php
  $slug = $typeSlug ?? (\Illuminate\Support\Str::slug($organization->type->slug ?? ($organization->type->name ?? 'organization')));
  $entity = trim($organization->consent_label ?? '') ?: match ($slug) {
      'hospital' => 'Patient',
      'yoga' => 'Participant',
      'zumba' => 'Member',
      'wellness' => 'Client',
      default => 'Member',
  };
  $entityPlural = \Illuminate\Support\Str::plural($entity);

  $commonConsentCards = [
      ['key' => 'balance',   'title' => 'Current Balance',    'value' => '₹' . number_format($organization->transaction_amount, 2)],
      ['key' => 'today',    'title' => "Today's Consents", 'value' => $todayConsents],
      ['key' => 'approved', 'title' => 'Approved Consents', 'value' => $approvedConsents],
      ['key' => 'pending',  'title' => 'Pending Consents',  'value' => $pendingConsents],
      ['key' => 'rejected', 'title' => 'Rejected Consents', 'value' => $rejectedConsents],
      ['key' => 'total',    'title' => 'Total Consents',    'value' => $totalConsents],
      ['key' => 'persons',  'title' => 'Total Persons',     'value' => $totalPersons],
  ];

  $typeSpecific = match ($slug) {
      'hospital' => [ ['key' => 'doctors', 'title' => 'Total Doctors', 'value' => 0] ],
      'gym'      => [ ['key' => 'entity',  'title' => "Total $entityPlural", 'value' => 0], ['key' => 'trainers', 'title' => 'Active Trainers', 'value' => 0] ],
      'yoga'     => [ ['key' => 'entity',  'title' => "Total $entityPlural", 'value' => 0], ['key' => 'instructors', 'title' => 'Total Instructors', 'value' => 0] ],
      'zumba'    => [ ['key' => 'entity',  'title' => "Total $entityPlural", 'value' => 0], ['key' => 'instructors', 'title' => 'Active Instructors', 'value' => 0] ],
      'wellness' => [ ['key' => 'therapists', 'title' => 'Active Therapists', 'value' => 0], ['key' => 'entity', 'title' => "Total $entityPlural", 'value' => 0] ],
      default    => [ ['key' => 'entity',  'title' => "Total $entityPlural", 'value' => 0] ],
  };

  $cards = array_slice(array_merge($commonConsentCards, $typeSpecific), 0, 6);

  // UI styles per card key
  $styles = [
    'balance'   => ['bg' => 'bg-green-50',    'fg' => 'text-green-700',    'icon' => 'currency'],
    'today'     => ['bg' => 'bg-blue-50',    'fg' => 'text-blue-700',    'icon' => 'calendar'],
    'approved'  => ['bg' => 'bg-emerald-50', 'fg' => 'text-emerald-700', 'icon' => 'check'],
    'pending'   => ['bg' => 'bg-amber-50',   'fg' => 'text-amber-700',   'icon' => 'clock'],
    'rejected'  => ['bg' => 'bg-rose-50',    'fg' => 'text-rose-700',    'icon' => 'x'],
    'total'     => ['bg' => 'bg-slate-50',   'fg' => 'text-slate-700',   'icon' => 'sum'],
    'persons'   => ['bg' => 'bg-purple-50',  'fg' => 'text-purple-700',  'icon' => 'users'],
    'doctors'   => ['bg' => 'bg-indigo-50',  'fg' => 'text-indigo-700',  'icon' => 'user'],
    'entity'    => ['bg' => 'bg-purple-50',  'fg' => 'text-purple-700',  'icon' => 'users'],
    'trainers'  => ['bg' => 'bg-teal-50',    'fg' => 'text-teal-700',    'icon' => 'bolt'],
    'instructors'=>['bg' => 'bg-fuchsia-50', 'fg' => 'text-fuchsia-700', 'icon' => 'sparkles'],
    'therapists'=> ['bg' => 'bg-cyan-50',    'fg' => 'text-cyan-700',    'icon' => 'heart'],
  ];

  // Destination route per card key
  $cardRoutes = [
      'balance'   => route('org.balance.add'),
      'today'     => route('org.consents.index', ['filter' => 'today']),
      'approved'  => route('org.consents.index', ['filter' => 'approved']),
      'pending'   => route('org.consents.index', ['filter' => 'pending']),
      'rejected'  => route('org.consents.index', ['filter' => 'rejected']),
      'total'     => route('org.consents.index'),
      'persons'   => route('org.persons.index'),
      'entity'    => route('org.persons.index'),
      'doctors'   => route('org.persons.index'),
      'trainers'  => route('org.persons.index'),
      'instructors'=> route('org.persons.index'),
      'therapists'=> route('org.persons.index'),
  ];

  $renderIcon = function(string $name, string $classes = 'w-5 h-5') {
      return match ($name) {
          'currency'  => '<svg xmlns="http://www.w3.org/2000/svg" class="'.$classes.'" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>',
          'calendar' => '<svg xmlns="http://www.w3.org/2000/svg" class="'.$classes.'" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>',
          'check'    => '<svg xmlns="http://www.w3.org/2000/svg" class="'.$classes.'" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414L8.414 15l-4.121-4.121a1 1 0 111.414-1.414L8.414 12.172l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>',
          'clock'    => '<svg xmlns="http://www.w3.org/2000/svg" class="'.$classes.'" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>',
          'x'        => '<svg xmlns="http://www.w3.org/2000/svg" class="'.$classes.'" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/></svg>',
          'sum'      => '<svg xmlns="http://www.w3.org/2000/svg" class="'.$classes.'" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2h6v2H9zm0-4V7h6v6H9z"/></svg>',
          'user'     => '<svg xmlns="http://www.w3.org/2000/svg" class="'.$classes.'" viewBox="0 0 20 20" fill="currentColor"><path d="M10 12a5 5 0 100-10 5 5 0 000 10z"/><path fill-rule="evenodd" d="M.458 16.042A10 10 0 0110 10a10 10 0 019.542 6.042A12.944 12.944 0 0110 18c-3.866 0-7.366-.824-9.542-1.958z" clip-rule="evenodd"/></svg>',
          'users'    => '<svg xmlns="http://www.w3.org/2000/svg" class="'.$classes.'" viewBox="0 0 20 20" fill="currentColor"><path d="M13 7a3 3 0 11-6 0 3 3 0 016 0z"/><path d="M5.5 13a4.5 4.5 0 019 0v1h-9v-1z"/></svg>',
          'bolt'     => '<svg xmlns="http://www.w3.org/2000/svg" class="'.$classes.'" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>',
          'sparkles' => '<svg xmlns="http://www.w3.org/2000/svg" class="'.$classes.'" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3l2 7-7 2 7 2-2 7 2-7 7 2-7-2 2-7-2 7-7-2 7-2-2-7z"/></svg>',
          'heart'    => '<svg xmlns="http://www.w3.org/2000/svg" class="'.$classes.'" viewBox="0 0 20 20" fill="currentColor"><path d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 18.657l-6.828-6.829a4 4 0 010-5.656z"/></svg>',
          default    => '<svg xmlns="http://www.w3.org/2000/svg" class="'.$classes.'" viewBox="0 0 20 20" fill="currentColor"><circle cx="10" cy="10" r="8"/></svg>',
      };
  };
@endphp

<div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
  @foreach($cards as $index => $card)
    @php
      $style = $styles[$card['key']] ?? $styles['total'];
      $delay = $index * 60;
      $href = $cardRoutes[$card['key']] ?? null;
    @endphp
    @if($href)
      <a href="{{ $href }}" class="block focus:outline-none focus-visible:ring-2 focus-visible:ring-indigo-500 focus-visible:ring-offset-2 focus-visible:ring-offset-gray-50 rounded-xl">
        <div class="group relative overflow-hidden rounded-xl bg-white shadow-sm border border-gray-200 ring-1 ring-gray-100 transition-all duration-200 hover:-translate-y-1 hover:shadow-xl" style="animation: fadeInUp 0.4s cubic-bezier(.16,1,.3,1) {{ $delay }}ms both; min-height: 160px;">
          <div class="absolute inset-x-0 -top-24 h-36 {{ $style['bg'] }} opacity-40 blur-3xl"></div>
          <div class="relative p-5">
            <div class="flex items-center justify-between gap-3">
              <div class="flex flex-col gap-1">
                <span class="text-xs font-medium {{$style['fg']}} uppercase tracking-wide">{{ $card['title'] }}</span>
                <span class="text-3xl font-semibold text-gray-900 leading-tight">
                  {{ is_numeric($card['value']) ? number_format($card['value']) : $card['value'] }}
                </span>
              </div>
              <span class="flex h-12 w-12 items-center justify-center rounded-full {{ $style['bg'] }} {{$style['fg']}} shadow-inner ring-1 ring-white/70">
                {!! $renderIcon($style['icon'], 'w-6 h-6') !!}
              </span>
            </div>
            <div class="mt-4 flex items-center justify-between text-xs text-gray-500">
              <span class="inline-flex items-center gap-1">
                <span class="inline-block h-1.5 w-1.5 rounded-full {{ $style['fg'] }} opacity-70"></span>
                {{ now()->timezone(config('app.timezone'))->format('d M, h:i A') }}
              </span>
              <span class="opacity-0 transition-opacity duration-200 group-hover:opacity-100">View details →</span>
            </div>
          </div>
        </div>
      </a>
    @else
      <div class="group relative overflow-hidden rounded-xl bg-white shadow-sm border border-gray-200 ring-1 ring-gray-100 transition-all duration-200 hover:-translate-y-1 hover:shadow-xl" style="animation: fadeInUp 0.4s cubic-bezier(.16,1,.3,1) {{ $delay }}ms both; min-height: 160px;">
        <div class="absolute inset-x-0 -top-24 h-36 {{ $style['bg'] }} opacity-40 blur-3xl"></div>
        <div class="relative p-5">
          <div class="flex items-center justify-between gap-3">
            <div class="flex flex-col gap-1">
              <span class="text-xs font-medium {{$style['fg']}} uppercase tracking-wide">{{ $card['title'] }}</span>
              <span class="text-3xl font-semibold text-gray-900 leading-tight">
                {{ is_numeric($card['value']) ? number_format($card['value']) : $card['value'] }}
              </span>
            </div>
            <span class="flex h-12 w-12 items-center justify-center rounded-full {{ $style['bg'] }} {{$style['fg']}} shadow-inner ring-1 ring-white/70">
              {!! $renderIcon($style['icon'], 'w-6 h-6') !!}
            </span>
          </div>
          <div class="mt-4 flex items-center justify-between text-xs text-gray-500">
            <span class="inline-flex items-center gap-1">
              <span class="inline-block h-1.5 w-1.5 rounded-full {{ $style['fg'] }} opacity-70"></span>
              {{ now()->timezone(config('app.timezone'))->format('d M, h:i A') }}
            </span>
            <span class="opacity-0 transition-opacity duration-200 group-hover:opacity-100">View details →</span>
          </div>
        </div>
      </div>
    @endif
  @endforeach
</div>

<style>
@keyframes fadeInUp {
  from {
    opacity: 0;
    transform: translate3d(0, 10px, 0);
  }
  to {
    opacity: 1;
    transform: translate3d(0, 0, 0);
  }
}
</style>
