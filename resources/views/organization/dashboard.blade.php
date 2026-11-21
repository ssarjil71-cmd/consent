@extends('organization.layouts.app')

@section('content')
  <div class="mb-8 flex items-center justify-between">
    <h1 class="text-3xl font-semibold text-gray-900">Dashboard</h1>
  </div>

  @php($slug = $typeSlug ?? ($organization->type->slug ?? \Illuminate\Support\Str::slug($organization->type->name ?? 'organization')))
  @include('organization.dashboard.cards')
@endsection
