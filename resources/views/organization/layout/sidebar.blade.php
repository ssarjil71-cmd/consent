<div class="sidebar">
  <h4>ConsentRX</h4>
  <p class="text-muted mb-3">Organization Panel</p>

  <a href="{{ route('org.dashboard') }}" class="{{ request()->routeIs('org.dashboard') ? 'active' : '' }}">
    📊 Dashboard
  </a>

  <h6 class="mt-3 text-secondary">Categories</h6>
  <a href="{{ route('org.categories.index') }}" class="{{ request()->routeIs('org.categories.index') ? 'active' : '' }}">
    📚 My Categories
  </a>
  <a href="{{ route('org.categories.create') }}" class="{{ request()->routeIs('org.categories.create') ? 'active' : '' }}">
    ➕ Add Category
  </a>

  <h6 class="mt-3 text-secondary">Consents</h6>
  <a href="{{ route('org.consents.index') }}" class="{{ request()->routeIs('org.consents.*') ? 'active' : '' }}">
    📋 All Consents
  </a>

  <hr>
  <form method="POST" action="{{ route('org.logout') }}">
    @csrf
    <button type="submit" class="btn btn-outline-light btn-sm w-100 mt-2">
      <i class="fas fa-sign-out-alt"></i> Logout
    </button>
  </form>
</div>
