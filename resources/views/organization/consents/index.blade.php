@extends('organization.layouts.app')

@section('content')
<div class="flex items-center justify-between mb-6">
  <div>
    <h1 class="text-2xl font-semibold">All Consents</h1>
    <p class="text-gray-600 text-sm">Submitted consents for {{ $organization->name }}</p>
  </div>
</div>

<div class="bg-white border border-gray-100 rounded-xl shadow-sm overflow-hidden">
  <div class="overflow-x-auto">
    <table class="min-w-full text-sm">
      <thead class="bg-gray-50 text-gray-600">
        <tr>
          <th class="px-4 py-3 text-left">ID</th>
          <th class="px-4 py-3 text-left">Name</th>
          <th class="px-4 py-3 text-left">Email</th>
          <th class="px-4 py-3 text-left">Phone</th>
          <th class="px-4 py-3 text-left">Status</th>
          <th class="px-4 py-3 text-left">Submitted</th>
          <th class="px-4 py-3 text-left">PDF</th>
          <th class="px-4 py-3 text-left">Backup</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-gray-100">
        @forelse($consents as $consent)
          <tr>
            <td class="px-4 py-3">{{ $consent->id }}</td>
            <td class="px-4 py-3">
              <a href="{{ route('org.consents.show', $consent->id) }}" class="text-indigo-600 hover:underline">
                {{ $consent->name }}
              </a>
            </td>
            <td class="px-4 py-3">{{ $consent->email ?: '—' }}</td>
            <td class="px-4 py-3">{{ $consent->phone ?: '—' }}</td>
            <td class="px-4 py-3">
              <span class="inline-flex px-2 py-1 rounded-full text-xs {{ $consent->status === 'approved' ? 'bg-emerald-50 text-emerald-700' : ($consent->status === 'rejected' ? 'bg-rose-50 text-rose-700' : 'bg-amber-50 text-amber-700') }}">
                {{ ucfirst($consent->status) }}
              </span>
            </td>
            <td class="px-4 py-3">{{ $consent->created_at->timezone(config('app.timezone'))->format('d M Y, h:i A') }}</td>
            <td class="px-4 py-3">
              <a href="#" onclick="checkConsentStatus('{{$consent->status}}', '{{ route('org.consents.pdf', $consent->id) }}')" class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md text-indigo-700 bg-indigo-100 hover:bg-indigo-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                <svg class="-ml-0.5 mr-1.5 h-4 w-4" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                  <path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                </svg>
                PDF
              </a>
            </td>
            <td class="px-4 py-3">
              <a href="#" onclick="downloadBackup(this, '{{$consent->status}}', '{{ route('org.consents.backup', $consent->id) }}', {{$consent->id}})" class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md text-red-700 bg-red-100 hover:bg-red-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500" id="backup-btn-{{$consent->id}}">
                <svg class="-ml-0.5 mr-1.5 h-4 w-4" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                  <path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                </svg>
                <span id="backup-text-{{$consent->id}}">Backup</span>
              </a>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="8" class="px-4 py-6 text-center text-gray-500">No consents found.</td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>
  <div class="p-4">{{ $consents->links() }}</div>
</div>

@push('scripts')
<script>
    // Check and restore download status on page load
    document.addEventListener('DOMContentLoaded', function() {
        @forelse($consents as $consent)
            restoreBackupStatus({{$consent->id}});
        @empty
        @endforelse
    });

    function restoreBackupStatus(consentId) {
        const statusKey = 'backup_downloaded_' + consentId;
        const isDownloaded = localStorage.getItem(statusKey);
        
        if (isDownloaded === 'true') {
            const btnElement = document.getElementById('backup-btn-' + consentId);
            const textElement = document.getElementById('backup-text-' + consentId);
            
            // Set to completed state
            btnElement.classList.remove('text-red-700', 'bg-red-100', 'hover:bg-red-200', 'focus:ring-red-500', 'text-blue-700', 'bg-blue-100', 'hover:bg-blue-200', 'focus:ring-blue-500');
            btnElement.classList.add('text-green-700', 'bg-green-100', 'hover:bg-green-200', 'focus:ring-green-500');
            textElement.textContent = 'Download Completed';
        }
    }

    function checkConsentStatus(consentStatus, pdfUrl) {
        if (consentStatus === 'pending') {
            alert('This consent is pending. First, approve this consent.');
        } else if (consentStatus === 'approved') {
            window.open(pdfUrl, '_blank');
        }
    }

    function downloadBackup(button, consentStatus, backupUrl, consentId) {
        if (consentStatus === 'pending') {
            alert('This consent is pending. First, approve this consent.');
            return;
        }

        if (consentStatus !== 'approved') {
            alert('Backup is only available for approved consents.');
            return;
        }

        // Change to downloading state
        const btnElement = document.getElementById('backup-btn-' + consentId);
        const textElement = document.getElementById('backup-text-' + consentId);
        
        // Store current state to restore after download
        const wasAlreadyDownloaded = btnElement.classList.contains('bg-green-100');
        
        // Remove current color classes and add blue (downloading) classes
        btnElement.classList.remove('text-red-700', 'bg-red-100', 'hover:bg-red-200', 'focus:ring-red-500', 'text-green-700', 'bg-green-100', 'hover:bg-green-200', 'focus:ring-green-500');
        btnElement.classList.add('text-blue-700', 'bg-blue-100', 'hover:bg-blue-200', 'focus:ring-blue-500');
        textElement.textContent = 'Downloading...';

        // Create a temporary link to download the file
        const link = document.createElement('a');
        link.href = backupUrl;
        link.target = '_blank';
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);

        // After a short delay, show completed state and save to localStorage
        setTimeout(() => {
            // Remove blue classes and add green (completed) classes
            btnElement.classList.remove('text-blue-700', 'bg-blue-100', 'hover:bg-blue-200', 'focus:ring-blue-500');
            btnElement.classList.add('text-green-700', 'bg-green-100', 'hover:bg-green-200', 'focus:ring-green-500');
            textElement.textContent = 'Download Completed';
            
            // Save download status to localStorage
            localStorage.setItem('backup_downloaded_' + consentId, 'true');
        }, 2000);
    }
</script>
@endpush
@endsection
