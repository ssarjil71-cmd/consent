@extends('organization.layouts.app')

@section('content')
  <div class="flex flex-col gap-6">
    <div class="flex items-start justify-between gap-4">
      <div>
        <h1 class="text-3xl font-semibold text-gray-900">Team Members</h1>
      </div>
      <a href="{{ route('org.persons.create') }}" class="inline-flex items-center gap-2 rounded-lg bg-gray-900 px-4 py-2.5 text-sm font-medium text-white shadow-sm transition hover:bg-gray-800">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" /></svg>
        Add Person
      </a>
    </div>

    @if($persons->isEmpty())
      <div class="rounded-2xl border border-dashed border-gray-200 bg-white p-10 text-center">
        <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-gray-100 text-gray-500">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" viewBox="0 0 20 20" fill="currentColor"><path d="M10 2a6 6 0 00-6 6v3a2 2 0 01-.293 1.024l-.666 1.11A1 1 0 003.89 14h12.22a1 1 0 00.85-1.553l-.666-1.11A2 2 0 0115 11V8a6 6 0 00-5-5.917V2z" /><path d="M7 15a3 3 0 006 0H7z" /></svg>
        </div>
        <h2 class="mt-4 text-lg font-semibold text-gray-900">No team members yet</h2>
        <p class="mt-1 text-sm text-gray-500">Start by adding your first staff member to assign consents and track activity.</p>
        <a href="{{ route('org.persons.create') }}" class="mt-4 inline-flex items-center gap-2 rounded-lg border border-gray-900 px-4 py-2 text-sm font-medium text-gray-900 transition hover:bg-gray-900 hover:text-white">Add Person</a>
      </div>
    @else
      <div class="overflow-hidden rounded-2xl border border-gray-100 bg-white shadow-sm">
        <table class="min-w-full divide-y divide-gray-100">
          <thead class="bg-gray-50">
            <tr class="text-left text-xs font-semibold uppercase tracking-wide text-gray-500">
              <th scope="col" class="px-6 py-3">Name</th>
              <th scope="col" class="px-6 py-3">Email</th>
              <th scope="col" class="px-6 py-3">Phone</th>
              <th scope="col" class="px-6 py-3">Occupation</th>
              <th scope="col" class="px-6 py-3">Signature</th>
              <th scope="col" class="px-6 py-3 text-right">Actions</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-100 text-sm text-gray-700">
            @foreach($persons as $person)
              <tr class="transition hover:bg-gray-50/75">
                <td class="px-6 py-4">
                  <div class="font-medium text-gray-900">{{ $person->name }}</div>
                </td>
                <td class="px-6 py-4 text-gray-600">{{ $person->email ?? '—' }}</td>
                <td class="px-6 py-4 text-gray-600">{{ $person->phone ?? '—' }}</td>
                <td class="px-6 py-4 text-gray-600">{{ $person->occupation ?? '—' }}</td>
                <td class="px-6 py-4">
                  @if($person->signature_path)
                    <img src="{{ asset('storage/' . $person->signature_path) }}" alt="Signature" class="h-8 w-auto border border-gray-200 rounded">
                  @else
                    <span class="text-gray-400 text-sm">No signature</span>
                  @endif
                </td>
                <td class="px-6 py-4 text-right">
                  <div class="inline-flex items-center gap-2">
                    <a href="{{ route('org.persons.edit', $person) }}" class="inline-flex items-center gap-1 rounded-lg border border-gray-200 bg-white px-3 py-1.5 text-xs font-medium text-gray-700 transition hover:bg-gray-50">
                      <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                      </svg>
                      Edit
                    </a>
                    <form action="{{ route('org.persons.destroy', $person) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this person?')">
                      @csrf
                      @method('DELETE')
                      <button type="submit" class="inline-flex items-center gap-1 rounded-lg border border-red-200 bg-white px-3 py-1.5 text-xs font-medium text-red-600 transition hover:bg-red-50">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" viewBox="0 0 20 20" fill="currentColor">
                          <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                        </svg>
                        Delete
                      </button>
                    </form>
                  </div>
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>

        <div class="border-t border-gray-100 bg-gray-50 px-6 py-4">
          {{ $persons->links() }}
        </div>
      </div>
    @endif
  </div>
@endsection
