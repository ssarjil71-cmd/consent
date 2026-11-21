@extends('organization.layouts.app')

@section('content')
<div class="space-y-6">
  <div>
    <h1 class="text-2xl font-semibold">Add Person</h1>
    <p class="text-gray-600 text-sm">{{ $organization->name }}</p>
  </div>

  <div class="bg-white border border-gray-100 rounded-xl shadow-sm p-6 max-w-2xl">
    <form method="POST" action="{{ route('org.persons.store') }}" class="space-y-5" enctype="multipart/form-data">
      @csrf
      <input type="hidden" name="consent_id" value="{{ $consentId }}" />

      <div>
        <label class="block text-sm font-medium text-gray-700">Name</label>
        <input type="text" name="name" value="{{ old('name') }}" class="mt-1 block w-full border rounded-md px-3 py-2" required>
        @error('name')<p class="text-sm text-rose-600 mt-1">{{ $message }}</p>@enderror
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
          <label class="block text-sm font-medium text-gray-700">Email</label>
          <input type="email" name="email" value="{{ old('email') }}" class="mt-1 block w-full border rounded-md px-3 py-2">
          @error('email')<p class="text-sm text-rose-600 mt-1">{{ $message }}</p>@enderror
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700">Phone</label>
          <input type="text" name="phone" value="{{ old('phone') }}" class="mt-1 block w-full border rounded-md px-3 py-2">
          @error('phone')<p class="text-sm text-rose-600 mt-1">{{ $message }}</p>@enderror
        </div>
      </div>

      <div>
        <label class="block text-sm font-medium text-gray-700">Occupation</label>
        <input type="text" name="occupation" value="{{ old('occupation') }}" class="mt-1 block w-full border rounded-md px-3 py-2" required>
        @error('occupation')<p class="text-sm text-rose-600 mt-1">{{ $message }}</p>@enderror
      </div>

      <div>
        <label class="block text-sm font-medium text-gray-700">Signature</label>
        <input type="file" name="signature" accept="image/*" class="mt-1 block w-full border rounded-md px-3 py-2">
        @error('signature')<p class="text-sm text-rose-600 mt-1">{{ $message }}</p>@enderror
      </div>

      <div class="flex items-center gap-3">
        <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-md">Save</button>
        <a href="{{ $consentId ? route('org.consents.show', $consentId) : route('org.consents.index') }}" class="px-4 py-2 border rounded-md">Cancel</a>
      </div>
      
    </form>
  </div>
</div>
@endsection
