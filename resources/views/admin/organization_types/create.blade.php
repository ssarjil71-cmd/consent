@extends('admin.layout.master')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header" style="background-color: #228b6aff;">
                    <h3 class="card-title text-white">Add New Organization Type</h3>
                </div>
                <!-- /.card-header -->
                <!-- form start -->
                <form method="POST" action="{{ route('admin.organization_types.store') }}">
                    @csrf
                    <div class="card-body">
                        <div class="form-group">
                            <label for="name" class="font-weight-bold">Name <span class="text-danger">*</span></label>
                            <input type="text" 
                                   class="form-control form-control-lg @error('name') is-invalid @enderror" 
                                   id="name" 
                                   name="name" 
                                   value="{{ old('name') }}" 
                                   placeholder="Enter organization type name"
                                   required>
                            @error('name')
                                <div class="invalid-feedback">
                                    <strong>{{ $message }}</strong>
                                </div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <label for="description" class="font-weight-bold">Description</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" 
                                      name="description" 
                                      rows="3" 
                                      placeholder="Enter description for this organization type">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">
                                    <strong>{{ $message }}</strong>
                                </div>
                            @enderror
                        </div>

                        <!-- Terms & Conditions Section -->
                        <div class="card mb-4">
                            <div class="card-header bg-light">
                                <h5 class="card-title mb-0">
                                    <i class="fas fa-file-contract text-info mr-2"></i>
                                    Terms & Conditions
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="form-group">
                                    <label for="terms_text" class="font-weight-bold">English Content</label>
                                    <textarea class="form-control @error('terms_text') is-invalid @enderror" 
                                              id="terms_text" 
                                              name="terms_text" 
                                              rows="6" 
                                              placeholder="Enter terms and conditions content in English">{{ old('terms_text') }}</textarea>
                                    @error('terms_text')
                                        <div class="invalid-feedback">
                                            <strong>{{ $message }}</strong>
                                        </div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Liability Release Section -->
                        <div class="card mb-4">
                            <div class="card-header bg-light">
                                <h5 class="card-title mb-0">
                                    <i class="fas fa-shield-alt text-warning mr-2"></i>
                                    Liability Release
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="form-group">
                                    <label for="liability_release_text" class="font-weight-bold">English Content</label>
                                    <textarea class="form-control @error('liability_release_text') is-invalid @enderror" 
                                              id="liability_release_text" 
                                              name="liability_release_text" 
                                              rows="6" 
                                              placeholder="Enter liability release content in English">{{ old('liability_release_text') }}</textarea>
                                    @error('liability_release_text')
                                        <div class="invalid-feedback">
                                            <strong>{{ $message }}</strong>
                                        </div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Risk Acknowledgement Section -->
                        <div class="card mb-4">
                            <div class="card-header bg-light">
                                <h5 class="card-title mb-0">
                                    <i class="fas fa-exclamation-triangle text-danger mr-2"></i>
                                    Risk Acknowledgement
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="form-group">
                                    <label for="risk_acknowledgement_text" class="font-weight-bold">English Content</label>
                                    <textarea class="form-control @error('risk_acknowledgement_text') is-invalid @enderror" 
                                              id="risk_acknowledgement_text" 
                                              name="risk_acknowledgement_text" 
                                              rows="6" 
                                              placeholder="Enter risk acknowledgement content in English">{{ old('risk_acknowledgement_text') }}</textarea>
                                    @error('risk_acknowledgement_text')
                                        <div class="invalid-feedback">
                                            <strong>{{ $message }}</strong>
                                        </div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- /.card-body -->

                    <div class="card-footer bg-light">
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('admin.organization_types.index') }}" class="btn btn-secondary btn-lg">
                                <i class="fas fa-arrow-left mr-2"></i>Cancel
                            </a>
                            <button type="submit" class="btn btn-lg text-white" style="background-color: #228b6aff; border-color: #228b6aff;">
                                <i class="fas fa-plus mr-2"></i>Create Organization Type
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
