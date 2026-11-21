@extends('organization.layouts.app')

@section('head')
<meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
<meta http-equiv="Pragma" content="no-cache">
<meta http-equiv="Expires" content="0">
@endsection

@section('content')
<div class="modal fade show d-block" id="termsModal" tabindex="-1" role="dialog" style="background-color: rgba(0,0,0,0.8); z-index: 9999;">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header text-white">
                <h5 class="modal-title">
                    <i class="fas fa-exclamation-triangle"></i>
                    Terms & Conditions Acceptance Required
                </h5>
            </div>
            <div class="modal-body">
                <div class="alert">
                    <h5><i class="fas fa-info-circle"></i> Important Notice</h5>
                    <p class="mb-0">Before you can access your dashboard, you must read and accept the following terms and conditions that apply to your organization type: <strong>{{ $organization->name }}</strong></p>
                    <div class="mt-3 p-3 rounded">
                        <small class="text-muted">
                            <strong>This Agreement for Use of the ConsentRx Platform is made between:</strong><br><br>
                            Aryaneel Infotex, having its office at KK Market, Office No. 87 & 88, D wing, 5th Floor, Dhankawadi, Pune 411043, hereinafter referred to as the "Service Provider",<br><br>
                            AND {{ $organization->name }}, located at {{ $organization->address ?? 'Organization Address' }}, hereinafter referred to as the "Hospital".
                        </small>
                    </div>
                </div>

                <form method="POST" action="{{ route('org.terms.accept') }}" id="termsForm">
                    @csrf

                    <!-- Terms & Conditions Section -->
                    @if($orgType->terms_text)
                    <div class="card mb-3">
                        <div class="card-header">
                            <h4 class="card-title">
                                <i class="fas fa-file-contract"></i> Terms & Conditions
                            </h4>
                        </div>
                        <div class="card-body">
                            <div class="terms-content">
                                {!! nl2br(e($orgType->terms_text)) !!}
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Liability Release Section -->
                    @if($orgType->liability_release_text)
                    <div class="card mb-3">
                        <div class="card-header">
                            <h4 class="card-title">
                                <i class="fas fa-shield-alt"></i> Liability Release
                            </h4>
                        </div>
                        <div class="card-body">
                            <div class="terms-content">
                                {!! nl2br(e($orgType->liability_release_text)) !!}
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Risk Acknowledgement Section -->
                    @if($orgType->risk_acknowledgement_text)
                    <div class="card mb-3">
                        <div class="card-header">
                            <h4 class="card-title">
                                <i class="fas fa-exclamation-triangle"></i> Risk Acknowledgement
                            </h4>
                        </div>
                        <div class="card-body">
                            <div class="terms-content">
                                {!! nl2br(e($orgType->risk_acknowledgement_text)) !!}
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Single Acceptance Checkbox -->
                    <div class="card mb-3">
                        <div class="card-body text-center">
                            <div class="form-check form-check-lg">
                                <input class="form-check-input" type="checkbox" id="acceptAllTerms" name="accept_all_terms" required>
                                <label class="form-check-label" for="acceptAllTerms">
                                    <strong>I accept all terms and conditions</strong>
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="alert">
                        <i class="fas fa-info-circle"></i>
                        <strong>Note:</strong> You must accept all applicable terms to continue using the system.
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="submit" form="termsForm" class="btn btn-success btn-lg" id="acceptBtn">
                    <i class="fas fa-check"></i> Accept Terms & Continue
                </button>
            </div>
        </div>
    </div>
</div>

<style>
/* Terms Acceptance Modal - Version 2.0 */
.terms-content {
    max-height: 400px;
    overflow-y: scroll !important;
    overflow-x: hidden;
    padding: 20px;
    white-space: pre-wrap;
    line-height: 1.8;
    font-size: 14px;
    word-wrap: break-word;
}

.modal.show.d-block {
    display: block !important;
}

.modal-lg {
    max-width: 85%;
}

.modal-content {
    border: none;
    border-radius: 12px;
    box-shadow: 0 20px 60px rgba(0,0,0,0.3);
    overflow: hidden;
}

.modal-header {
    border: none;
    background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
    padding: 20px 25px;
}

.modal-title {
    font-weight: 600;
    font-size: 1.25rem;
}

.modal-body {
    padding: 25px;
    background: #ffffff;
    max-height: 70vh;
    overflow-y: auto;
}

.modal-footer {
    border: none;
    background: #f8f9fa;
    padding: 20px 25px;
}

.form-check {
    padding: 15px;
    background: #f8f9fa;
    border-radius: 8px;
    border-left: 4px solid #28a745;
    margin-top: 15px !important;
    transition: all 0.3s ease;
}

.form-check:hover {
    background: #e9ecef;
    transform: translateX(5px);
}

.form-check-input {
    width: 20px;
    height: 20px;
    margin-top: 2px;
}

.form-check-input:checked {
    background-color: #28a745;
    border-color: #28a745;
    box-shadow: 0 0 0 2px rgba(40, 167, 69, 0.25);
}

.form-check-label {
    font-size: 15px;
    font-weight: 500;
    color: #495057;
    cursor: pointer;
    margin-left: 8px;
}

.card {
    border: none;
    border-radius: 10px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.08);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    overflow: hidden;
}

.card:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.15);
}

.card-header {
    border: none;
    padding: 18px 20px;
    font-weight: 600;
}

.card-body {
    padding: 20px;
}

.card-outline.card-info .card-header {
    background: linear-gradient(135deg, #17a2b8 0%, #138496 100%);
    color: white;
}

.card-outline.card-warning .card-header {
    background: linear-gradient(135deg, #ffc107 0%, #e0a800 100%);
    color: #212529;
}

.card-outline.card-danger .card-header {
    background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
    color: white;
}

.alert {
    border: none;
    border-radius: 8px;
    padding: 15px 20px;
    font-weight: 500;
}

.alert-warning {
    background: linear-gradient(135deg, #fff3cd 0%, #ffeeba 100%);
    color: #856404;
    border-left: 4px solid #ffc107;
}

.alert-info {
    background: linear-gradient(135deg, #d1ecf1 0%, #bee5eb 100%);
    color: #0c5460;
    border-left: 4px solid #17a2b8;
}

.btn-success {
    background: linear-gradient(135deg, #28a745 0%, #218838 100%);
    border: none;
    padding: 12px 30px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    border-radius: 8px;
    transition: all 0.3s ease;
    box-shadow: 0 4px 15px rgba(40, 167, 69, 0.3);
}

.btn-success:hover:not(:disabled) {
    background: linear-gradient(135deg, #218838 0%, #1e7e34 100%);
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(40, 167, 69, 0.4);
}

.btn-success:disabled {
    background: #6c757d;
    cursor: not-allowed;
    opacity: 0.6;
}

/* Hide background completely */
.main-sidebar,
.main-header,
.content-wrapper,
.wrapper {
    pointer-events: none;
    opacity: 0.3;
    visibility: visible;
    filter: blur(2px);
    transition: all 0.3s ease;
    position: relative;
    z-index: 1;
}

.modal-content {
    pointer-events: auto;
    z-index: 99999;
    position: relative;
}

/* Additional hiding for all background elements */
body > .wrapper,
.navbar-nav,
.sidebar,
.main-footer,
.content-header,
.breadcrumb {
    pointer-events: none !important;
    opacity: 0.3 !important;
    visibility: visible !important;
}

/* Ensure modal is on top */
#termsModal {
    position: fixed !important;
    top: 0 !important;
    left: 0 !important;
    width: 100% !important;
    height: 100% !important;
    z-index: 999999 !important;
    background-color: rgba(0,0,0,0.5) !important;
    overflow-y: auto !important;
}

.modal-dialog {
    z-index: 1000000 !important;
    margin-top: 50px;
    margin-left: 100px;
}

/* Allow body scrolling for modal content */
body {
    overflow: visible !important;
}

.modal {
    overflow: hidden !important;
}

/* Scrollbar styling */
.terms-content::-webkit-scrollbar {
    width: 12px;
}

.terms-content::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 6px;
    border: 1px solid #e0e0e0;
}

.terms-content::-webkit-scrollbar-thumb {
    background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
    border-radius: 6px;
    border: 2px solid #ffffff;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.terms-content::-webkit-scrollbar-thumb:hover {
    background: linear-gradient(135deg, #218838 0%, #1ea085 100%);
    box-shadow: 0 3px 6px rgba(0,0,0,0.15);
}

.terms-content::-webkit-scrollbar-thumb:active {
    background: linear-gradient(135deg, #1e7e34 0%, #1c7d6e 100%);
}

.terms-content::-webkit-scrollbar-corner {
    background: #f1f1f1;
}

/* Animations */
@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.card {
    animation: fadeInUp 0.5s ease forwards;
}

.card:nth-child(2) {
    animation-delay: 0.1s;
}

.card:nth-child(3) {
    animation-delay: 0.2s;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Prevent closing modal with escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            e.preventDefault();
            e.stopPropagation();
        }
    });

    // Prevent clicking outside modal to close
    document.getElementById('termsModal').addEventListener('click', function(e) {
        if (e.target === this) {
            e.preventDefault();
            e.stopPropagation();
        }
    });

    const acceptAllCheckbox = document.getElementById('acceptAllTerms');
    const acceptBtn = document.getElementById('acceptBtn');
    
    function checkAllAccepted() {
        if (acceptAllCheckbox.checked) {
            acceptBtn.disabled = false;
            acceptBtn.classList.remove('disabled');
        } else {
            acceptBtn.disabled = true;
            acceptBtn.classList.add('disabled');
        }
    }
    
    acceptAllCheckbox.addEventListener('change', checkAllAccepted);
    
    // Initial check
    checkAllAccepted();
});
</script>
@endsection
