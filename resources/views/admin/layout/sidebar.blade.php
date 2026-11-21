<div class="sidebar" style="background-color: #fff;">
  <h4>CONSENTRX</h4>
  <div class="brand">Admin Panel</div>
  <p>Secured Consent • Protected Practice</p>

  @php
    $admin = auth('admin')->user();

    $menuSections = [
        [
            'heading' => null,
            'items' => [
                [
                    'label' => 'Dashboard',
                    'route' => 'admin.dashboard',
                    'routes' => ['admin.dashboard'],
                    'permission' => 'admin.dashboard.view',
                    'dataIcon' => 'blue',
                    'icon' => function () {
                        return '<path d="M3 3v18h18" /><path d="M7 13l3 3 7-7" />';
                    },
                    'iconWidth' => 20,
                    'iconHeight' => 20,
                    'class' => 'dashboard-icon',
                    'style' => 'margin-left:0;',
                ],
            ],
        ],
        [
            'heading' => ['key' => 'organization', 'label' => 'Organization', 'permission' => 'admin.organizations.section'],
            'items' => [
                [
                    'label' => 'All Organizations',
                    'route' => 'admin.organizations.index',
                    'routes' => ['admin.organizations.*'],
                    'permission' => 'admin.organizations.view',
                    'dataIcon' => 'teal',
                    'icon' => function () {
                        return '<rect x="3" y="4" width="18" height="14" rx="2" /><path d="M7 8h10M7 12h8" />';
                    },
                ],
                [
                    'label' => 'Add Organization',
                    'route' => 'admin.organizations.create',
                    'routes' => ['admin.organizations.create'],
                    'permission' => 'admin.organizations.create',
                    'dataIcon' => 'emerald',
                    'icon' => function () {
                        return '<circle cx="12" cy="12" r="9" /><path d="M12 8v8M8 12h8" />';
                    },
                ],
                [
                    'label' => 'Organization Types',
                    'route' => 'admin.organization_types.index',
                    'routes' => ['admin.organization_types.*'],
                    'permission' => 'admin.organization-types.view',
                    'dataIcon' => 'indigo',
                    'icon' => function () {
                        return '<path d="M7 7h10v4H7z" /><path d="M7 13h10v4H7z" />';
                    },
                ],
            ],
        ],
        [
            'heading' => ['key' => 'transactions', 'label' => 'Transactions', 'permission' => 'admin.transactions.section'],
            'items' => [
                [
                    'label' => 'All Transactions',
                    'route' => 'admin.transactions.index',
                    'routes' => ['admin.transactions.*'],
                    'permission' => 'admin.transactions.view',
                    'dataIcon' => 'amber',
                    'icon' => function () {
                        return '<rect x="2" y="5" width="20" height="14" rx="2" /><path d="M2 10h20" />';
                    },
                ],
            ],
        ],
        [
            'heading' => ['key' => 'consents', 'label' => 'Consents', 'permission' => 'admin.consents.section'],
            'items' => [
                [
                    'label' => 'All Consents',
                    'route' => 'admin.consents.index',
                    'routes' => ['admin.consents.*'],
                    'permission' => 'admin.consents.view',
                    'dataIcon' => 'violet',
                    'icon' => function () {
                        return '<path d="M7 3h10v4H7z" /><rect x="5" y="7" width="14" height="14" rx="2" />';
                    },
                ],
            ],
        ],
        [
            'heading' => ['key' => 'category', 'label' => 'Category', 'permission' => 'admin.categories.section'],
            'items' => [
                [
                    'label' => 'All Categories',
                    'route' => 'admin.categories.index',
                    'routes' => ['admin.categories.*'],
                    'permission' => 'admin.categories.view',
                    'dataIcon' => 'sky',
                    'icon' => function () {
                        return '<rect x="3" y="4" width="18" height="14" rx="2" /><path d="M7 8h10M7 12h8" />';
                    },
                ],
                [
                    'label' => 'Add Category',
                    'route' => 'admin.categories.create',
                    'routes' => ['admin.categories.create'],
                    'permission' => 'admin.categories.create',
                    'dataIcon' => 'cyan',
                    'icon' => function () {
                        return '<circle cx="12" cy="12" r="9" /><path d="M12 8v8M8 12h8" />';
                    },
                ],
            ],
        ],
        [
            'heading' => ['key' => 'system', 'label' => 'System Management', 'permission' => 'admin.system.section'],
            'items' => [
                [
                    'label' => 'Role & Permission',
                    'route' => 'admin.roles.index',
                    'routes' => ['admin.roles.*'],
                    'permission' => 'admin.roles.view',
                    'dataIcon' => 'rose',
                    'icon' => function () {
                        return '<path d="M12 2l3 5 6 .5-4 4 1 6-6-3-6 3 1-6-4-4 6-.5z" />';
                    },
                ],
                [
                    'label' => 'Permissions',
                    'route' => 'admin.permissions.index',
                    'routes' => ['admin.permissions.*'],
                    'permission' => 'admin.permissions.view',
                    'dataIcon' => 'violet',
                    'icon' => function () {
                        return '<path d="M9 21v-6a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2v6" /><path d="M7 10h10" /><path d="M12 2l5 5H7l5-5z" />';
                    },
                ],
                [
                    'label' => 'Admin Profile',
                    'route' => 'admin.profile',
                    'routes' => ['admin.profile'],
                    'permission' => 'admin.profile.view',
                    'dataIcon' => 'violet-light',
                    'icon' => function () {
                        return '<path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" /><circle cx="12" cy="7" r="4" />';
                    },
                ],
                [
                    'label' => 'Team Members',
                    'route' => 'admin.team-members.index',
                    'routes' => ['admin.team-members.*'],
                    'permission' => 'admin.team-members.view',
                    'dataIcon' => 'violet-light',
                    'icon' => function () {
                        return '<path d="M17 21v-2a4 4 0 0 0-4-4H7a4 4 0 0 0-4 4v2" /><circle cx="9" cy="7" r="4" />';
                    },
                ],
                [
                    'label' => 'Delete Consents',
                    'route' => null,
                    'routes' => [],
                    'permission' => 'admin.consents.delete',
                    'dataIcon' => 'blue',
                    'icon' => function () {
                        return '<path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z" /><line x1="12" y1="9" x2="12" y2="13" /><line x1="12" y1="17" x2="12.01" y2="17" />';
                    },
                ],
            ],
        ],
        [
            'heading' => ['key' => 'sales', 'label' => 'Sales', 'permission' => 'admin.sales.section'],
            'items' => [
                [
                    'label' => 'Sales Dashboard',
                    'route' => 'admin.sales.dashboard',
                    'routes' => ['admin.sales.*'],
                    'permission' => 'admin.sales.view',
                    'dataIcon' => 'blue',
                    'icon' => function () {
                        return '<line x1="18" y1="20" x2="18" y2="10" /><line x1="12" y1="20" x2="12" y2="4" /><line x1="6" y1="20" x2="6" y2="14" />';
                    },
                ],
            ],
        ],
        [
            'heading' => ['key' => 'plans', 'label' => 'Plan Details', 'permission' => 'admin.plans.section'],
            'items' => [
                [
                    'label' => 'Plans',
                    'route' => null,
                    'routes' => [],
                    'permission' => 'admin.plans.view',
                    'dataIcon' => 'slate',
                    'icon' => function () {
                        return '<rect x="3" y="3" width="18" height="18" rx="2" /><path d="M3 9h18M9 3v6" />';
                    },
                ],
            ],
        ],
    ];

    $userHasPermission = function ($permission) use ($admin) {
        if (!$permission) {
            return true;
        }

        if (!$admin) {
            return false;
        }

        return $admin->hasPermission($permission);
    };
  @endphp

  @foreach ($menuSections as $section)
    @php
      $visibleItems = collect($section['items'])
        ->filter(fn ($item) => $userHasPermission($item['permission']))
        ->values();
    @endphp

    @if ($visibleItems->isNotEmpty() && ($section['heading'] === null || $userHasPermission($section['heading']['permission'] ?? null)))
      @if ($section['heading'])
        <h6 data-heading="{{ $section['heading']['key'] }}">
          <span class="heading-icon">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="16" height="16">
              <path d="M12 2l3 5 6 .5-4 4 1 6-6-3-6 3 1-6-4-4 6-.5z" />
            </svg>
          </span>
          <span>{{ $section['heading']['label'] }}</span>
        </h6>
      @endif

      @foreach ($visibleItems as $item)
        @php
          $isActive = $item['routes'] ? request()->routeIs(...$item['routes']) : false;
          $iconWidth = $item['iconWidth'] ?? 18;
          $iconHeight = $item['iconHeight'] ?? 18;
          $iconClass = $item['class'] ?? '';
          $style = $item['style'] ?? '';
          $iconMarkup = $item['icon']();
        @endphp

        @if ($item['route'])
          <a href="{{ route($item['route']) }}" class="{{ $isActive ? 'active' : '' }}" data-icon="{{ $item['dataIcon'] }}" @if($style) style="{{ $style }}" @endif>
            <span class="icon-badge {{ $iconClass }}">
              <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="{{ $iconWidth }}" height="{{ $iconHeight }}">
                {!! $iconMarkup !!}
              </svg>
            </span>
            <span>{{ $item['label'] }}</span>
          </a>
        @else
          <div class="sidebar-link-disabled" data-icon="{{ $item['dataIcon'] }}">
            <span class="icon-badge {{ $iconClass }}">
              <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" width="{{ $iconWidth }}" height="{{ $iconHeight }}">
                {!! $iconMarkup !!}
              </svg>
            </span>
            <span>{{ $item['label'] }}</span>
          </div>
        @endif
      @endforeach
    @endif
  @endforeach

  <hr>
  <form method="POST" action="{{ route('admin.logout') }}">
    @csrf
    <button class="btn btn-outline-primary btn-sm w-100 mt-2">Logout</button>
  </form>
</div>
