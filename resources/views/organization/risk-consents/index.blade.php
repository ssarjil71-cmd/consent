@extends('organization.layouts.app')

@section('content')
<div class="mb-6">
  <div class="flex items-center justify-between">
    <div>
      <h1 class="text-2xl font-semibold text-gray-900">Risk Consent Library</h1>
    </div>
    <a
      href="{{ route('org.risk-consents.create') }}"
      class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
    >
      <svg class="mr-2 h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
      </svg>
      Create Risk Consent
    </a>
  </div>
</div>

@if ($riskConsents->isEmpty())
  <div class="bg-white border border-dashed border-gray-200 rounded-xl p-10 text-center">
    <svg class="mx-auto h-12 w-12 text-gray-300" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
      <path stroke-linecap="round" stroke-linejoin="round" d="M3 7.5l7.5-3 4.5 1.5L21 4.5m-18 3v9l7.5 3m-7.5-12l7.5 3m0 0v9m0-9l4.5-1.5m0 0l7.5-3v9l-7.5 3m0-9v9" />
    </svg>
    <h2 class="mt-4 text-lg font-semibold text-gray-900">No risk consents yet</h2>
    <p class="mt-2 text-sm text-gray-600">Create your first multilingual risk consent template to share with clients.</p>
    <a
      href="{{ route('org.risk-consents.create') }}"
      class="mt-6 inline-flex items-center gap-2 rounded-lg bg-indigo-600 px-4 py-2.5 text-sm font-medium text-white shadow hover:bg-indigo-700"
    >
      <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
      </svg>
      Create Risk Consent
    </a>
  </div>
@else
  <div class="bg-white border border-gray-100 rounded-2xl shadow-sm">
    <div class="overflow-x-auto">
      <table class="min-w-full divide-y divide-gray-200 text-sm">
        <thead class="bg-gray-50">
          <tr>
            <th scope="col" class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Category</th>
            <th scope="col" class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Created</th>
            <th scope="col" class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Updated</th>
            <th scope="col" class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Languages</th>
            <th scope="col" class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Status</th>
            <th scope="col" class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wide text-gray-500">Actions</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-100 bg-white">
          @foreach ($riskConsents as $riskConsent)
            @php
              $statusClasses = $riskConsent->is_active
                ? 'bg-emerald-50 text-emerald-700'
                : 'bg-gray-100 text-gray-600';
            @endphp
            <tr>
              <td class="whitespace-nowrap px-4 py-3 text-gray-700">
                @if ($riskConsent->category)
                  {{ $riskConsent->category->name }}
                @else
                  <span class="text-gray-400">—</span>
                @endif
              </td>
              <td class="whitespace-nowrap px-4 py-3 text-gray-700">
                {{ optional($riskConsent->created_at)->timezone(config('app.timezone'))->format('d M Y') ?? '—' }}
              </td>
              <td class="whitespace-nowrap px-4 py-3 text-gray-700">
                {{ optional($riskConsent->updated_at)->timezone(config('app.timezone'))->format('d M Y') ?? '—' }}
              </td>
              <td class="whitespace-nowrap px-4 py-3 text-gray-700">
                {{ $riskConsent->active_languages->map(fn ($key) => strtoupper($key))->implode(', ') ?: 'None' }}
              </td>
              <td class="whitespace-nowrap px-4 py-3">
                <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-medium {{ $statusClasses }}">
                  {{ $riskConsent->is_active ? 'Active' : 'Inactive' }}
                </span>
              </td>
              <td class="whitespace-nowrap px-4 py-3 text-right">
                <a
                  href="{{ route('org.risk-consents.edit', $riskConsent) }}"
                  class="inline-flex items-center rounded-full bg-indigo-50 px-3 py-1 text-xs font-medium text-indigo-600 hover:bg-indigo-100"
                >
                  Edit consent
                </a>
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>
@endif
@endsection
