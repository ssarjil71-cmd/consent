<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Consent submitted</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-gray-50 py-10 px-4 sm:px-6 lg:px-8">
  <div class="max-w-xl mx-auto">
    <div class="mb-8 text-center">
      <h1 class="text-2xl font-semibold text-gray-900">Consent Form</h1>
      <p class="mt-1 text-sm text-gray-600">Thank you for submitting your consent.</p>
      @if(isset($organization))
        <p class="mt-1 text-xs text-gray-500">Organization ID: {{ $organization->id }}</p>
      @else
        <p class="mt-1 text-xs text-red-500">Organization not found</p>
      @endif
    </div>

    <div class="rounded-xl border border-emerald-100 bg-white shadow-sm p-6 sm:p-8 flex flex-col items-center text-center">
      <div class="mb-4 flex h-12 w-12 items-center justify-center rounded-full bg-emerald-50 text-emerald-600">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="h-7 w-7">
          <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293A1 1 0 106.293 10.707l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
        </svg>
      </div>
      <h2 class="text-xl font-semibold text-gray-900">Consent submitted successfully</h2>
      <p class="mt-2 text-sm text-gray-600">
        Thank you. Your consent form has been submitted successfully.
      </p>

      @if (session('success'))
        <p class="mt-3 text-xs text-gray-500">{{ session('success') }}</p>
      @endif

      <div class="mt-6 flex flex-col sm:flex-row gap-3 justify-center">
        @if(isset($organization))
          <a href="{{ route('consent.form', ['organization' => $organization->id]) }}" class="inline-flex items-center justify-center rounded bg-gray-900 px-4 py-2 text-sm font-medium text-white hover:bg-gray-800">
            Fill another consent
          </a>
        @else
          <a href="#" onclick="history.back()" class="inline-flex items-center justify-center rounded bg-gray-900 px-4 py-2 text-sm font-medium text-white hover:bg-gray-800">
            Go back
          </a>
        @endif
      </div>
    </div>
  </div>
</body>
</html>
