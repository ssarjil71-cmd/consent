@extends('organization.layouts.app')

@section('content')
<div class="mb-8">
  <a href="{{ route('org.risk-consents.index') }}" class="inline-flex items-center text-sm text-gray-500 hover:text-gray-700">
    <svg class="mr-2 h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
      <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5" />
    </svg>
    Back to Risk Consents
  </a>
</div>

<div class="max-w-4xl mx-auto bg-white border border-gray-100 rounded-2xl shadow-sm">
  <div class="border-b border-gray-100 px-6 py-5">
    <h1 class="text-2xl font-semibold text-gray-900">Create Risk Consent</h1>
  </div>

  <form action="{{ route('org.risk-consents.store') }}" method="POST" class="px-6 py-6 space-y-8">
    @csrf

    <div class="grid gap-8 md:grid-cols-2">
      <div class="space-y-2">
        <label for="title" class="block text-sm font-medium text-gray-900">Internal title</label>
        <input
          type="text"
          id="title"
          name="title"
          value="{{ old('title') }}"
          class="w-full max-w-sm rounded-md border-gray-200 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm text-gray-800"
          placeholder="e.g. General gym participation consent"
        />
        @error('title')
          <p class="text-sm text-rose-600">{{ $message }}</p>
        @enderror
      </div>

      <div class="space-y-2">
        <label for="category_id" class="block text-sm font-medium text-gray-900">Category</label>
        <select
          id="category_id"
          name="category_id"
          class="w-full rounded-xl border-gray-200 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm text-gray-800"
        >
          <option value="">Select category</option>
          @foreach ($categories as $category)
            <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
              {{ $category->name }}
              @if ($category->organization_id)
                (Org Specific)
              @elseif ($category->organization_type_id)
                ({{ $category->organizationType->name ?? 'Type' }})
              @else
                (Global)
              @endif
            </option>
          @endforeach
        </select>
        @error('category_id')
          <p class="text-sm text-rose-600">{{ $message }}</p>
        @enderror
      </div>

      @php
        $activeValue = (string) old('is_active', '1');
      @endphp
      <div class="space-y-3">
        <span class="block text-sm font-medium text-gray-900">Status</span>
        <div class="flex items-center gap-3 rounded-xl border border-gray-200 bg-gray-50/60 px-4 py-3">
          <input type="hidden" name="is_active" value="0">
          <label for="is_active" class="inline-flex items-center gap-2 text-sm text-gray-700">
            <input
              type="checkbox"
              id="is_active"
              name="is_active"
              value="1"
              class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
              {{ $activeValue === '1' ? 'checked' : '' }}
            />
            Keep this consent active for use in workflows
          </label>
        </div>
        @error('is_active')
          <p class="text-sm text-rose-600">{{ $message }}</p>
        @enderror
      </div>

      @foreach ([
        ['key' => 'content_en', 'label' => 'Risk Consent English', 'placeholder' => 'Enter risk content here...'],
        ['key' => 'content_hi', 'label' => 'Risk Consent Hindi', 'placeholder' => 'यहाँ जोखिम संमति सामग्री दर्ज करें...'],
        ['key' => 'content_mr', 'label' => 'Risk Consent Marathi', 'placeholder' => 'येथे जोखीम संमती सामग्री प्रविष्ट करा...'],
      ] as $field)
      <div class="space-y-3">
        <div class="flex items-center justify-between">
          <label for="{{ $field['key'] }}" class="block text-sm font-medium text-gray-900">{{ $field['label'] }}</label>
          <span class="text-xs font-medium text-indigo-600 bg-indigo-50 px-2 py-1 rounded-full">{{ strtoupper(\Illuminate\Support\Str::after($field['key'], '_')) }}</span>
        </div>
        <textarea
          id="{{ $field['key'] }}"
          name="{{ $field['key'] }}"
          rows="6"
          class="w-full rounded-xl border-gray-200 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm text-gray-800"
          placeholder="{{ $field['placeholder'] }}"
        >{{ old($field['key']) }}</textarea>
        @error($field['key'])
          <p class="text-sm text-rose-600">{{ $message }}</p>
        @enderror
      </div>
      @endforeach
    </div>

    <div class="flex items-center justify-end border-t border-gray-100 pt-6">
      <button
        type="submit"
        class="inline-flex items-center gap-2 px-5 py-2.5 rounded-lg bg-indigo-600 text-sm font-medium text-white shadow hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
      >
        <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
        </svg>
        Save Risk Consent
      </button>
    </div>
  </form>
</div>
@endsection
