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
      <h3>Edit Organization: {{ $organization->name }}</h3>
      <a href="{{ route('admin.organizations.index') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Back to List
      </a>
    </div>

    <form action="{{ route('admin.organizations.update', $organization) }}" method="POST" class="needs-validation" novalidate>
      @csrf
      @method('PUT')

      <div class="row">
        <!-- Basic Info -->
        <div class="col-md-6 mb-3">
          <label for="name" class="form-label">Organization Name <span class="text-danger">*</span></label>
          <input type="text" id="name" name="name" class="form-control"
                 value="{{ old('name', $organization->name) }}" required>
        </div>

        <div class="col-md-6 mb-3">
          <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
          <input type="email" id="email" name="email" class="form-control"
                 value="{{ old('email', $organization->email) }}" required>
        </div>

        <div class="col-md-6 mb-3">
          <label for="mobile" class="form-label">Mobile Number</label>
          <input type="tel" id="mobile" name="mobile" class="form-control @error('mobile') is-invalid @enderror"
                 value="{{ old('mobile', $organization->mobile) }}" placeholder="Enter 10-digit mobile number">
          @error('mobile')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>

        <div class="col-md-6 mb-3">
          <label for="password" class="form-label">Password</label>
          <input type="password" id="password" name="password" class="form-control">
          <small class="text-muted">Leave blank to keep current password</small>
        </div>

        <div class="col-md-6 mb-3">
          <label for="type_id" class="form-label">Organization Type <span class="text-danger">*</span></label>
          <select id="type_id" name="type_id" class="form-select" required>
            <option value="" disabled>Select Type</option>
            @foreach($types as $type)
              <option value="{{ $type->id }}" {{ old('type_id', $organization->type_id) == $type->id ? 'selected' : '' }}>
                {{ $type->name }}
              </option>
            @endforeach
          </select>
        </div>

        <div class="col-md-6 mb-3">
          <label for="status" class="form-label">Status</label>
          <select id="status" name="status" class="form-select">
            <option value="active" {{ old('status', $organization->status) == 'active' ? 'selected' : '' }}>Active</option>
            <option value="inactive" {{ old('status', $organization->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
          </select>
        </div>

        <div class="col-md-6 mb-3">
          <label for="activation_fee" class="form-label">Activation Fee <span class="text-danger">*</span></label>
          <input type="number" step="0.01" id="activation_fee" name="activation_fee"
                 class="form-control @error('activation_fee') is-invalid @enderror" 
                 value="{{ old('activation_fee', $organization->activation_fee ?? 0) }}" required>
          @error('activation_fee')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>

        <div class="col-md-6 mb-3">
          <label for="transaction_amount" class="form-label">Transaction Amount</label>
          <input type="number" step="0.01" id="transaction_amount" name="transaction_amount"
                 class="form-control @error('transaction_amount') is-invalid @enderror" 
                 value="{{ old('transaction_amount', $organization->transaction_amount) }}">
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
                   value="{{ old('per_consent_amount', $organization->per_consent_amount) }}">
          </div>
          @error('per_consent_amount')
            <div class="invalid-feedback d-block">{{ $message }}</div>
          @enderror
          <small class="text-muted">Amount to be charged per consent</small>
        </div>

        <div class="col-md-4 mb-3">
          <label for="consent_label" class="form-label">Consent Label</label>
          <input type="text" id="consent_label" name="consent_label"
                 class="form-control form-control-sm"
                 value="{{ old('consent_label', $organization->consent_label) }}"
                 placeholder="e.g., Patient">
        </div>

        <div class="col-12">
          <div class="mb-2 border-bottom pb-1">
            <h5 class="mb-0">Terms &amp; Conditions</h5>
          </div>
        </div>

        @foreach($legalLanguages as $code => $language)
        @php
          $termsField = $language['terms_field'];
        @endphp
        <div class="col-md-4 mb-3">
          <label for="terms_text_{{ $code }}" class="form-label">{{ $language['label'] }}</label>
          <textarea id="terms_text_{{ $code }}" name="{{ $termsField }}" rows="6"
                    class="form-control @error($termsField) is-invalid @enderror"
                    placeholder="">{{ old($termsField, $organization->{$termsField}) }}</textarea>
          @error($termsField)
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
        @php
          $liabilityField = $language['liability_field'];
        @endphp
        <div class="col-md-4 mb-3">
          <label for="liability_release_text_{{ $code }}" class="form-label">{{ $language['label'] }}</label>
          <textarea id="liability_release_text_{{ $code }}" name="{{ $liabilityField }}" rows="6"
                    class="form-control @error($liabilityField) is-invalid @enderror"
                    placeholder="">{{ old($liabilityField, $organization->{$liabilityField}) }}</textarea>
          @error($liabilityField)
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
        @php
          $riskField = $language['risk_field'];
        @endphp
        <div class="col-md-4 mb-3">
          <label for="risk_acknowledgement_text_{{ $code }}" class="form-label">{{ $language['label'] }}</label>
          <textarea id="risk_acknowledgement_text_{{ $code }}" name="{{ $riskField }}" rows="6"
                    class="form-control @error($riskField) is-invalid @enderror"
                    placeholder="">{{ old($riskField, $organization->{$riskField}) }}</textarea>
          @error($riskField)
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div>
        @endforeach

        <!-- ✅ Assign Categories Section -->
        <div class="col-md-6 mb-3">
          <label class="form-label fw-bold">Assign Categories</label>

          <input type="hidden" id="selectedCategories" name="categories[]"
                 value="{{ implode(',', $assignedCategoryIds) }}">

          <!-- Single Dropdown -->
          <div class="dropdown w-100">
            <button class="form-control text-start dropdown-toggle" type="button"
                    id="categoryDropdown" data-bs-toggle="dropdown" aria-expanded="false">
              <span id="selectedCategoriesText">
                @if(count($assignedCategoryIds))
                  {{ implode(', ', $allCategories->whereIn('id',$assignedCategoryIds)->pluck('name')->toArray()) }}
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
                           {{ in_array($category->id, $assignedCategoryIds) ? 'checked' : '' }}>
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
              @if(in_array($category->id, $assignedCategoryIds))
                <span class="badge bg-primary me-1 mb-1" id="badge-{{ $category->id }}">
                  {{ $category->name }}
                  <span class="remove-category ms-1" data-category-id="{{ $category->id }}" style="cursor:pointer;">×</span>
                </span>
              @endif
            @endforeach
          </div>

          <small class="text-muted d-block mt-1">Click × to remove a category</small>
        </div>
      </div>

      <div class="mt-4">
        <button type="submit" class="btn btn-primary">
          <i class="fas fa-save"></i> Update Organization
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
    if (!dropdownBtn.contains(e.target) && !e.target.closest('.dropdown-menu')) {
      const dropdown = bootstrap.Dropdown.getInstance(dropdownBtn);
      if (dropdown) dropdown.hide();
    }
  });

  updateSelected();
});
</script>
@endpush
