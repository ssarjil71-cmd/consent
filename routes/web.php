<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\OrganizationController;
use App\Http\Controllers\Admin\OrganizationTypeController;
use App\Http\Controllers\Admin\ConsentController;
use App\Http\Controllers\Admin\TransactionController;
use App\Http\Controllers\Admin\SalesController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\OrganizationAuthController;
use App\Http\Controllers\Organization\OrganizationDashboardController;
use App\Http\Controllers\Organization\PlanController;
use App\Http\Controllers\Organization\RiskConsentController;
use App\Http\Controllers\Organization\TermsAcceptanceController;
use App\Http\Controllers\PublicConsentController;
use App\Http\Controllers\PageController;

// Public routes
Route::get('/', function () {
    return view('welcome');
})->name('home');

// Policy pages
Route::get('/terms-and-conditions', [PageController::class, 'terms'])->name('pages.terms');
Route::get('/privacy-policy', [PageController::class, 'privacy'])->name('pages.privacy');
Route::get('/refund-policy', [PageController::class, 'refund'])->name('pages.refund');

// Test email route
Route::get('/test-email', function() {
    \Illuminate\Support\Facades\Mail::raw('This is a test email from ConsentRX', function($message) {
        $message->to('lifeshieldinfoteqsoftware@gmail.com')
               ->subject('Test Email - ConsentRX');
    });
    return 'Test email sent successfully!';
});

// Admin Authentication Routes
Route::redirect('/admin', '/admin/login')->name('admin.redirect');
Route::prefix('admin')->name('admin.')->group(function () {
    // Guest routes (not authenticated)
    Route::middleware('guest:admin')->group(function () {
        Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
        Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
        
        // Password Reset Routes
        Route::get('password/reset', [App\Http\Controllers\Auth\ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
        Route::post('password/email', [App\Http\Controllers\Auth\ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
        Route::get('password/reset/{token}', [App\Http\Controllers\Auth\ResetPasswordController::class, 'showResetForm'])->name('password.reset');
        Route::post('password/reset', [App\Http\Controllers\Auth\ResetPasswordController::class, 'reset'])->name('password.update');
    });

    // Logout route (should be outside auth middleware)
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Authenticated routes
    Route::middleware(['auth:admin'])->group(function () {
        // Dashboard
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        
        // Admin Profile
        Route::get('/profile', [AuthController::class, 'profile'])->name('profile');
        Route::put('/profile', [AuthController::class, 'updateProfile'])->name('profile.update');
        Route::put('/password', [AuthController::class, 'updatePassword'])->name('password.update');
        
        // Consents listing
        Route::get('/consents', [ConsentController::class, 'index'])->name('consents.index');
        Route::get('/consents/{consent}', [ConsentController::class, 'show'])->name('consents.show');
        
        // Transactions listing
        Route::get('/transactions', [TransactionController::class, 'index'])->name('transactions.index');
        
        // Organization Types
        Route::prefix('organization-types')->name('organization_types.')->group(function () {
            Route::get('/', [OrganizationTypeController::class, 'index'])->name('index');
            Route::get('/create', [OrganizationTypeController::class, 'create'])->name('create');
            Route::post('/', [OrganizationTypeController::class, 'store'])->name('store');
            Route::get('/{organizationType}/edit', [OrganizationTypeController::class, 'edit'])->name('edit');
            Route::put('/{organizationType}', [OrganizationTypeController::class, 'update'])->name('update');
        });

        // Organizations
        Route::prefix('organizations')->name('organizations.')->group(function () {
            Route::get('/', [OrganizationController::class, 'index'])->name('index');
            Route::get('/create', [OrganizationController::class, 'create'])->name('create');
            Route::post('/', [OrganizationController::class, 'store'])->name('store');
            Route::get('/{organization}/edit', [OrganizationController::class, 'edit'])->name('edit');
            Route::put('/{organization}', [OrganizationController::class, 'update'])->name('update');
        });
        
        // Logout
        Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

        // Categories
        Route::resource('categories', \App\Http\Controllers\Admin\CategoryController::class);

        // Get categories by organization type (AJAX)
        Route::get('/categories/by-type', [OrganizationController::class, 'categoriesByType'])
            ->name('categories.by-type');

        // Roles & Permissions
        Route::resource('roles', \App\Http\Controllers\Admin\RoleController::class);
        Route::resource('permissions', \App\Http\Controllers\Admin\PermissionController::class)->except(['show']);
        Route::resource('team-members', \App\Http\Controllers\Admin\AdminUserController::class);

        // Sales Dashboard
        Route::prefix('sales')->name('sales.')->group(function () {
            Route::get('/dashboard', [SalesController::class, 'dashboard'])->name('dashboard');
            Route::get('/organizations', [SalesController::class, 'organizations'])->name('organizations');
            Route::get('/transactions', [SalesController::class, 'transactions'])->name('transactions');
        });
    });
});

// Public Consent Form (scannable/anonymous access)
Route::get('/consent/{organization}', [PublicConsentController::class, 'show'])
    ->whereNumber('organization')
    ->name('consent.form');
Route::post('/consent/send-otp', [PublicConsentController::class, 'sendOtp'])->name('consent.send_otp');
Route::post('/consent/verify-otp', [PublicConsentController::class, 'verifyOtp'])->name('consent.verify_otp');
Route::post('/consent/kyc-verify', [PublicConsentController::class, 'verifyKyc'])->name('consent.kyc_verify');
Route::post('/consent', [PublicConsentController::class, 'store'])->name('consent.submit');
Route::get('/consent/{organization}/submitted', [PublicConsentController::class, 'submitted'])
    ->whereNumber('organization')
    ->name('consent.submitted');

// Temporary route to check database structure (remove in production)
Route::get('/check-db', function() {
    try {
        $columns = \DB::select('SHOW COLUMNS FROM consents');
        return response()->json([
            'status' => 'success',
            'columns' => array_column($columns, 'Field')
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => $e->getMessage()
        ]);
    }
})->name('check.db');

// Organization (end-user) Authentication Routes
Route::prefix('org')->name('org.')->group(function () {
    Route::get('/login', [OrganizationAuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [OrganizationAuthController::class, 'login'])->name('login.submit');
    
    // Organization Password Reset Routes - Temporarily disabled to fix conflicts
    // Route::middleware('guest:organization')->group(function () {
    //     Route::get('password/reset', [App\Http\Controllers\Auth\ForgotPasswordController::class, 'showLinkRequestForm'])->name('org.password.request');
    //     Route::post('password/email', [App\Http\Controllers\Auth\ForgotPasswordController::class, 'sendResetLinkEmail'])->name('org.password.email');
    //     Route::get('password/reset/{token}', [App\Http\Controllers\Auth\ResetPasswordController::class, 'showResetForm'])->name('org.password.reset');
    //     Route::post('password/reset', [App\Http\Controllers\Auth\ResetPasswordController::class, 'reset'])->name('org.password.reset.submit');
    // });

    Route::middleware('auth:organization')->group(function () {
        // Activation Routes (must be before dashboard)
        Route::prefix('activation')->name('activation.')->group(function () {
            Route::get('/show', [\App\Http\Controllers\Organization\ActivationController::class, 'show'])->name('show');
            Route::post('/payment', [\App\Http\Controllers\Organization\ActivationController::class, 'payment'])->name('payment');
            Route::get('/success', [\App\Http\Controllers\Organization\ActivationController::class, 'success'])->name('success');
        });
        
        // Terms Acceptance Routes (must be before dashboard)
        Route::prefix('terms')->name('terms.')->group(function () {
            Route::get('/show', [TermsAcceptanceController::class, 'show'])->name('show');
            Route::post('/accept', [TermsAcceptanceController::class, 'accept'])->name('accept');
        });

        Route::get('/dashboard', [OrganizationDashboardController::class, 'index'])
            ->middleware(['terms.acceptance', 'org.status'])
            ->name('dashboard');
        
        // Balance Management Routes
        Route::prefix('balance')->name('balance.')->group(function () {
            Route::get('/add', [App\Http\Controllers\Organization\BalanceController::class, 'showAddBalance'])->name('add');
            Route::post('/create-order', [App\Http\Controllers\Organization\BalanceController::class, 'createOrder'])->name('create-order');
            Route::post('/confirm', [App\Http\Controllers\Organization\BalanceController::class, 'confirmAddBalance'])->name('confirm');
            Route::get('/history', [App\Http\Controllers\Organization\BalanceController::class, 'history'])->name('history');
            Route::get('/test-razorpay', [App\Http\Controllers\Organization\BalanceController::class, 'testRazorpay'])->name('test-razorpay');
            Route::get('/debug', [App\Http\Controllers\Organization\BalanceController::class, 'debug'])->name('debug');
        });
        Route::post('/logout', [OrganizationAuthController::class, 'logout'])->name('logout');
        Route::get('/profile', [OrganizationDashboardController::class, 'profile'])->name('profile');
        Route::put('/profile', [OrganizationDashboardController::class, 'updateProfile'])->name('profile.update');
        Route::put('/password', [OrganizationDashboardController::class, 'updatePassword'])->name('org.password.update');
        Route::get('/transactions', [OrganizationDashboardController::class, 'transactions'])->name('transactions.index');
        Route::get('/consents', [OrganizationDashboardController::class, 'consents'])->name('consents.index');
        Route::get('/consents/{consent}', [OrganizationDashboardController::class, 'consentShow'])
            ->whereNumber('consent')
            ->name('consents.show');
        Route::post('/consents/{consent}/approve', [OrganizationDashboardController::class, 'consentApprove'])
            ->whereNumber('consent')
            ->name('consents.approve');
        Route::get('/consents/{consent}/pdf', [OrganizationDashboardController::class, 'consentPdf'])
            ->whereNumber('consent')
            ->name('consents.pdf');
        Route::get('/consents/{consent}/backup', [OrganizationDashboardController::class, 'consentBackup'])
            ->whereNumber('consent')
            ->name('consents.backup');

        // Persons (assignable staff)
        Route::get('/persons', [\App\Http\Controllers\Organization\PersonController::class, 'index'])->name('persons.index');
        Route::get('/persons/create', [\App\Http\Controllers\Organization\PersonController::class, 'create'])->name('persons.create');
        Route::post('/persons', [\App\Http\Controllers\Organization\PersonController::class, 'store'])->name('persons.store');
        Route::get('/persons/{person}/edit', [\App\Http\Controllers\Organization\PersonController::class, 'edit'])->name('persons.edit');
        Route::put('/persons/{person}', [\App\Http\Controllers\Organization\PersonController::class, 'update'])->name('persons.update');
        Route::delete('/persons/{person}', [\App\Http\Controllers\Organization\PersonController::class, 'destroy'])->name('persons.destroy');
        
        // Categories
        Route::get('/categories', [\App\Http\Controllers\Organization\CategoryController::class, 'index'])->name('categories.index');
        Route::get('/categories/create', [\App\Http\Controllers\Organization\CategoryController::class, 'create'])->name('categories.create');
        Route::post('/categories', [\App\Http\Controllers\Organization\CategoryController::class, 'store'])->name('categories.store');

        // Plans
        Route::prefix('plans')->name('plans.')->group(function () {
            Route::get('/', [PlanController::class, 'index'])->name('index');
            Route::get('/create', [PlanController::class, 'create'])->name('create');
            Route::post('/', [PlanController::class, 'store'])->name('store');
            Route::get('/{plan}/edit', [PlanController::class, 'edit'])
                ->whereNumber('plan')
                ->name('edit');
            Route::put('/{plan}', [PlanController::class, 'update'])
                ->whereNumber('plan')
                ->name('update');
            Route::delete('/{plan}', [PlanController::class, 'destroy'])
                ->whereNumber('plan')
                ->name('destroy');
        });

        Route::prefix('risk-consents')->name('risk-consents.')->group(function () {
            Route::get('/', [RiskConsentController::class, 'index'])->name('index');
            Route::get('/create', [RiskConsentController::class, 'create'])->name('create');
            Route::post('/', [RiskConsentController::class, 'store'])->name('store');
            Route::get('/{riskConsent}/edit', [RiskConsentController::class, 'edit'])
                ->whereNumber('riskConsent')
                ->name('edit');
            Route::put('/{riskConsent}', [RiskConsentController::class, 'update'])
                ->whereNumber('riskConsent')
                ->name('update');
            Route::delete('/{riskConsent}', [RiskConsentController::class, 'destroy'])
                ->whereNumber('riskConsent')
                ->name('destroy');
        });
    });
});
