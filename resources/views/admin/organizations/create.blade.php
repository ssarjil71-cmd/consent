@extends('admin.layout.master')

@section('content')
@php
  $legalLanguages = [
    'en' => [
      'label' => 'English',
      'terms_field' => 'terms_text',
      'liability_field' => 'liability_release_text',
      'risk_field' => 'risk_acknowledgement_text',
    ],
    'hi' => [
      'label' => 'Hindi',
      'terms_field' => 'terms_text_hi',
      'liability_field' => 'liability_release_text_hi',
      'risk_field' => 'risk_acknowledgement_text_hi',
    ],
    'mr' => [
      'label' => 'Marathi',
      'terms_field' => 'terms_text_mr',
      'liability_field' => 'liability_release_text_mr',
      'risk_field' => 'risk_acknowledgement_text_mr',
    ],
  ];
@endphp

<div class="card">
  <div class="card-body">
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h3>Add New Organization</h3>
      <a href="{{ route('admin.organizations.index') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Back to List
      </a>
    </div>

    <form action="{{ route('admin.organizations.store') }}" method="POST" class="needs-validation" novalidate>
      @csrf

      <div class="row">
        <!-- Basic Info -->
        <div class="col-md-6 mb-3">
          <label for="name" class="form-label">Organization Name <span class="text-danger">*</span></label>
          <input type="text" id="name" name="name" class="form-control @error('name') is-invalid @enderror"
                 value="{{ old('name') }}" required>
          @error('name')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>

        <div class="col-md-6 mb-3">
          <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
          <input type="email" id="email" name="email" class="form-control @error('email') is-invalid @enderror"
                 value="{{ old('email') }}" required>
          @error('email')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>

        <div class="col-md-6 mb-3">
          <label for="mobile" class="form-label">Mobile Number</label>
          <input type="tel" id="mobile" name="mobile" class="form-control @error('mobile') is-invalid @enderror"
                 value="{{ old('mobile') }}" placeholder="Enter 10-digit mobile number">
          @error('mobile')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>

        <div class="col-md-6 mb-3">
          <label for="password" class="form-label">Password <span class="text-danger">*</span></label>
          <input type="password" id="password" name="password" class="form-control @error('password') is-invalid @enderror" required>
          @error('password')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
          <small class="text-muted">Minimum 6 characters</small>
        </div>

        <div class="col-md-6 mb-3">
          <label for="type_id" class="form-label">Organization Type <span class="text-danger">*</span></label>
          <select id="type_id" name="type_id" class="form-select @error('type_id') is-invalid @enderror" required>
            <option value="" disabled selected>Select Type</option>
            @foreach($types as $type)
              <option value="{{ $type->id }}" {{ old('type_id') == $type->id ? 'selected' : '' }}>
                {{ $type->name }}
              </option>
            @endforeach
          </select>
          @error('type_id')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>

        <div class="col-md-6 mb-3">
          <label for="status" class="form-label">Status</label>
          <select id="status" name="status" class="form-select @error('status') is-invalid @enderror">
            <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>Active</option>
            <option value="inactive" {{ old('status', 'inactive') == 'inactive' ? 'selected' : '' }}>Inactive</option>
          </select>
          @error('status')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>

        <div class="col-md-6 mb-3">
          <label for="activation_fee" class="form-label">Activation Fee <span class="text-danger">*</span></label>
          <input type="number" step="0.01" id="activation_fee" name="activation_fee"
                 class="form-control @error('activation_fee') is-invalid @enderror" 
                 value="{{ old('activation_fee', 0) }}" required>
          @error('activation_fee')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>

        <div class="col-md-6 mb-3">
          <label for="transaction_amount" class="form-label">Transaction Amount</label>
          <input type="number" step="0.01" id="transaction_amount" name="transaction_amount"
                 class="form-control @error('transaction_amount') is-invalid @enderror" 
                 value="{{ old('transaction_amount', 0) }}">
          @error('transaction_amount')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>

        <div class="col-md-6 mb-3">
          <label for="per_consent_amount" class="form-label">Per Consent Amount</label>
          <div class="input-group">
            <span class="input-group-text">₹</span>
            <input type="number" step="0.01" id="per_consent_amount" name="per_consent_amount"
                   class="form-control @error('per_consent_amount') is-invalid @enderror" 
                   value="{{ old('per_consent_amount', 0) }}">
          </div>
          @error('per_consent_amount')
            <div class="invalid-feedback d-block">{{ $message }}</div>
          @enderror
          <small class="text-muted">Amount to be charged per consent</small>
        </div>

        <div class="col-md-4 mb-3">
          <label for="consent_label" class="form-label">Consent Label</label>
          <input type="text" id="consent_label" name="consent_label"
                 class="form-control form-control-sm @error('consent_label') is-invalid @enderror"
                 value="{{ old('consent_label') }}"
                 placeholder="e.g., Patient">
          @error('consent_label')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>

        <div class="col-12">
          <div class="mb-2 border-bottom pb-1">
            <h5 class="mb-0">Terms &amp; Conditions</h5>
          </div>
        </div>

        @foreach($legalLanguages as $code => $language)
        <div class="col-md-4 mb-3">
          <label for="terms_text_{{ $code }}" class="form-label"> {{ $language['label'] }}</label>
          <textarea id="terms_text_{{ $code }}" name="{{ $language['terms_field'] }}" rows="6"
                    class="form-control @error($language['terms_field']) is-invalid @enderror"
                    placeholder="">{{ old($language['terms_field']) }}</textarea>
          @error($language['terms_field'])
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>
        @endforeach

        <div class="col-12 mt-3">
          <div class="mb-2 border-bottom pb-1">
            <h5 class="mb-0">Liability Release</h5>
          </div>
        </div>

        @foreach($legalLanguages as $code => $language)
        <div class="col-md-4 mb-3">
          <label for="liability_release_text_{{ $code }}" class="form-label">{{ $language['label'] }}</label>
          <textarea id="liability_release_text_{{ $code }}" name="{{ $language['liability_field'] }}" rows="6"
                    class="form-control @error($language['liability_field']) is-invalid @enderror"
                    placeholder="">{{ old($language['liability_field']) }}</textarea>
          @error($language['liability_field'])
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>
        @endforeach

        <div class="col-12 mt-3">
          <div class="mb-2 border-bottom pb-1">
            <h5 class="mb-0">Risk Acknowledgement</h5>
          </div>
        </div>

        @foreach($legalLanguages as $code => $language)
        <div class="col-md-4 mb-3">
          <label for="risk_acknowledgement_text_{{ $code }}" class="form-label">{{ $language['label'] }}</label>
          <textarea id="risk_acknowledgement_text_{{ $code }}" name="{{ $language['risk_field'] }}" rows="6"
                    class="form-control @error($language['risk_field']) is-invalid @enderror"
                    placeholder="">{{ old($language['risk_field']) }}</textarea>
          @error($language['risk_field'])
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>
        @endforeach

        <!-- Assign Categories Section -->
        <div class="col-md-6 mb-3">
          <label class="form-label fw-bold">Assign Categories</label>

          @php
              $oldCategoriesValue = old('categories');
              $categoryIds = [];

              if (is_array($oldCategoriesValue)) {
                  $categoryIds = array_map('intval', $oldCategoriesValue);
              } elseif (is_string($oldCategoriesValue)) {
                  $categoryIds = array_filter(array_map('intval', explode(',', $oldCategoriesValue)));
              }

              $categoryIds = array_unique($categoryIds);
          @endphp

          <input type="hidden" id="selectedCategories" name="categories[]" value="{{ implode(',', $categoryIds) }}">

          <!-- Single Dropdown -->
          <div class="dropdown w-100">
            <button class="form-control text-start dropdown-toggle" type="button"
                    id="categoryDropdown" data-bs-toggle="dropdown" aria-expanded="false">
              <span id="selectedCategoriesText">
                @php
                    $selectedCategories = $allCategories->whereIn('id', $categoryIds)->pluck('name')->toArray();
                @endphp
                @if(!empty($selectedCategories))
                  {{ implode(', ', $selectedCategories) }}
                @else
                  Select categories...
                @endif
              </span>
            </button>

            <ul class="dropdown-menu w-100 p-2" id="categoryDropdownMenu" style="max-height: 250px; overflow-y: auto;">
              @foreach($allCategories as $category)
                <li>
                  <div class="form-check">
                    <input class="form-check-input category-checkbox" type="checkbox"
                           id="category_{{ $category->id }}" value="{{ $category->id }}"
                           {{ in_array($category->id, $categoryIds) ? 'checked' : '' }}>
                    <label class="form-check-label" for="category_{{ $category->id }}">
                      {{ $category->name }} ({{ $category->organization_id ? 'Custom' : 'Global' }})
                    </label>
                  </div>
                </li>
              @endforeach
            </ul>
          </div>

          <!-- Selected Badges -->
          <div class="mt-2 border rounded p-2 bg-light" id="selectedCategoriesBox" style="min-height:40px;">
            @foreach($allCategories as $category)
              @if(in_array($category->id, $categoryIds))
                <span class="badge bg-primary me-1 mb-1" id="badge-{{ $category->id }}">
                  {{ $category->name }}
                  <span class="remove-category ms-1" data-category-id="{{ $category->id }}" style="cursor:pointer;">×</span>
                </span>
              @endif
            @endforeach
          </div>

          <small class="text-muted d-block mt-1">Click × to remove a category</small>
          @error('categories')
            <div class="text-danger small">{{ $message }}</div>
          @enderror
        </div>
      </div>

      <div class="mt-4">
        <button type="submit" class="btn btn-primary">
          <i class="fas fa-save"></i> Create Organization
        </button>
        <a href="{{ route('admin.organizations.index') }}" class="btn btn-outline-secondary">
          <i class="fas fa-times"></i> Cancel
        </a>
      </div>
    </form>
  </div>
</div>
@endsection

@push('styles')
<style>
.remove-category {
  color: #fff;
  font-weight: bold;
  font-size: 1rem;
}
.remove-category:hover {
  color: #ffcccc;
}
#selectedCategoriesBox {
  min-height: 40px;
}
</style>
@endpush

@push('organization-scripts')
<script>
function loadCategories(typeId) {
    if (!typeId) {
        $('#categoryDropdownMenu').html('<li><div class="p-2 text-muted">Please select an organization type first</div></li>');
        return;
    }

    $.ajax({
        url: '{{ route('admin.categories.by-type') }}',
        type: 'GET',
        data: { type_id: typeId },
        success: function(response) {
            let html = '';
            if (response.length > 0) {
                response.forEach(function(category) {
                    const isChecked = $('#selectedCategories').val().split(',').includes(category.id.toString());
                    html += `
                    <li>
                        <div class="form-check">
                            <input class="form-check-input category-checkbox" type="checkbox"
                                   id="category_${category.id}" value="${category.id}"
                                   ${isChecked ? 'checked' : ''}>
                            <label class="form-check-label" for="category_${category.id}">
                                ${category.name} (${category.organization_id ? 'Custom' : 'Global'})
                            </label>
                        </div>
                    </li>`;
                });
            } else {
                html = '<li><div class="p-2 text-muted">No categories found for this organization type</div></li>';
            }
            $('#categoryDropdownMenu').html(html);
            updateSelected();
        },
        error: function() {
            console.error('Error loading categories');
        }
    });
}

document.addEventListener('DOMContentLoaded', () => {
  const dropdownBtn = document.getElementById('categoryDropdown');
  const dropdownMenu = document.getElementById('categoryDropdownMenu');
  const selectedBox = document.getElementById('selectedCategoriesBox');
  const selectedText = document.getElementById('selectedCategoriesText');
  const hiddenInput  = document.getElementById('selectedCategories');

  function updateSelected() {
    const checked = document.querySelectorAll('.category-checkbox:checked');
    selectedBox.innerHTML = '';
    const names = [];
    const ids = [];

    checked.forEach(cb => {
      const name = cb.nextElementSibling.textContent.trim().replace(/\s*\(.*\)$/,'');
      const id   = cb.value;
      ids.push(id);
      names.push(name);

      const badge = document.createElement('span');
      badge.className = 'badge bg-primary me-1 mb-1';
      badge.id = `badge-${id}`;
      badge.innerHTML = `${name} <span class="remove-category ms-1" data-category-id="${id}" style="cursor:pointer;">×</span>`;
      selectedBox.appendChild(badge);
    });

    hiddenInput.value = ids.join(',');
    selectedText.textContent = names.length ? names.join(', ') : 'Select categories...';
  }

  // Initialize with any previously selected categories
  updateSelected();

  // Checkbox change
  dropdownMenu.addEventListener('change', (e) => {
    if (e.target.classList.contains('category-checkbox')) updateSelected();
  });

  // Remove badge
  selectedBox.addEventListener('click', e => {
    if (e.target.classList.contains('remove-category')) {
      const id = e.target.dataset.categoryId;
      const cb = document.getElementById('category_' + id);
      if (cb) cb.checked = false;
      const badge = document.getElementById('badge-' + id);
      if (badge) badge.remove();
      updateSelected();
    }
  });

  // Close dropdown when clicking outside
  document.addEventListener('click', e => {
    if (!dropdownBtn.contains(e.target) && !dropdownMenu.contains(e.target)) {
      dropdownMenu.classList.remove('show');
      dropdownBtn.setAttribute('aria-expanded', 'false');
    }
  });

  // Load categories when organization type changes
  $('#type_id').on('change', function() {
    const typeId = $(this).val();
    loadCategories(typeId);
  });

  // Initialize with current type if any
  @if(old('type_id'))
    loadCategories({{ old('type_id') }});
  @endif
});
</script>

@if(session('old'))
<script>
    $(document).ready(function() {
        if ($('#type_id').val()) {
            loadCategories($('#type_id').val());
        }
    });
</script>
@endif
@endpush
