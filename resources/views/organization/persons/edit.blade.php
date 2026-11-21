@extends('organization.layouts.app')

@section('content')
  <div class="flex flex-col gap-6">
    <div class="flex items-start justify-between gap-4">
      <div>
        <h1 class="text-3xl font-semibold text-gray-900">Edit Team Member</h1>
        <p class="mt-1 text-sm text-gray-500">Update person details and signature</p>
      </div>
      <a href="{{ route('org.persons.index') }}" class="inline-flex items-center gap-2 rounded-lg border border-gray-200 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 shadow-sm transition hover:bg-gray-50">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
          <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
        </svg>
        Back to Team Members
      </a>
    </div>

    <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
      <form action="{{ route('org.persons.update', $person) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
          <div>
            <label for="name" class="block text-sm font-medium text-gray-700">Full Name *</label>
            <input type="text" id="name" name="name" value="{{ old('name', $person->name) }}" required
                   class="mt-1 block w-full rounded-lg border-gray-200 bg-white px-3 py-2 text-sm text-gray-900 shadow-sm transition focus:border-gray-900 focus:outline-none focus:ring-1 focus:ring-gray-900">
            @error('name')
              <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
          </div>

          <div>
            <label for="email" class="block text-sm font-medium text-gray-700">Email Address</label>
            <input type="email" id="email" name="email" value="{{ old('email', $person->email) }}"
                   class="mt-1 block w-full rounded-lg border-gray-200 bg-white px-3 py-2 text-sm text-gray-900 shadow-sm transition focus:border-gray-900 focus:outline-none focus:ring-1 focus:ring-gray-900">
            @error('email')
              <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
          </div>

          <div>
            <label for="phone" class="block text-sm font-medium text-gray-700">Phone Number</label>
            <input type="tel" id="phone" name="phone" value="{{ old('phone', $person->phone) }}"
                   class="mt-1 block w-full rounded-lg border-gray-200 bg-white px-3 py-2 text-sm text-gray-900 shadow-sm transition focus:border-gray-900 focus:outline-none focus:ring-1 focus:ring-gray-900">
            @error('phone')
              <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
          </div>

          <div>
            <label for="occupation" class="block text-sm font-medium text-gray-700">Occupation *</label>
            <input type="text" id="occupation" name="occupation" value="{{ old('occupation', $person->occupation) }}" required
                   class="mt-1 block w-full rounded-lg border-gray-200 bg-white px-3 py-2 text-sm text-gray-900 shadow-sm transition focus:border-gray-900 focus:outline-none focus:ring-1 focus:ring-gray-900">
            @error('occupation')
              <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
          </div>
        </div>

        <div>
          <label for="signature" class="block text-sm font-medium text-gray-700">Signature</label>
          <div class="mt-1">
            @if($person->signature_path)
              <div class="mb-3">
                <p class="text-sm text-gray-500 mb-2">Current signature:</p>
                <img src="{{ asset('storage/' . $person->signature_path) }}" alt="Current Signature" class="h-16 w-auto border border-gray-200 rounded">
              </div>
            @endif
            <input type="file" id="signature" name="signature" accept="image/*"
                   class="block w-full rounded-lg border-gray-200 bg-white px-3 py-2 text-sm text-gray-900 shadow-sm transition focus:border-gray-900 focus:outline-none focus:ring-1 focus:ring-gray-900">
            <p class="mt-1 text-xs text-gray-500">Upload a new signature image (JPEG, PNG, GIF - Max 2MB). Leave empty to keep current signature.</p>
          </div>
          @error('signature')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
          @enderror
        </div>

        <div class="flex items-center justify-end gap-3 pt-4">
          <a href="{{ route('org.persons.index') }}" class="rounded-lg border border-gray-200 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm transition hover:bg-gray-50">
            Cancel
          </a>
          <button type="submit" class="rounded-lg bg-gray-900 px-4 py-2 text-sm font-medium text-white shadow-sm transition hover:bg-gray-800">
            Update Person
          </button>
        </div>
      </form>
    </div>
  </div>
@endsection
