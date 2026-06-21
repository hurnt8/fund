<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoanController;
use Illuminate\Support\Facades\Redirect;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\Auth\ClientLoginController;
use App\Http\Controllers\Auth\OtpController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\Auth\InvitationController;
use App\Http\Controllers\Auth\StaffLoginController;
use App\Http\Controllers\Dashboard\ClientDashboardController;
use App\Http\Controllers\Dashboard\AdminDashboardController;
use App\Http\Controllers\Dashboard\SuperAdminDashboardController;
use App\Http\Controllers\Admin\UserManagementController;
use App\Http\Controllers\Admin\LoanRequestController as AdminLoanRequestController;
use App\Http\Controllers\Admin\ContractTemplateController;
use App\Http\Controllers\SuperAdmin\LoanRequestController as SuperAdminLoanRequestController;
use App\Http\Controllers\Client\LoanRequestController as ClientLoanRequestController;
use App\Http\Controllers\Client\AppController as ClientAppController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

$supportedLocales = ['fr', 'en', 'pl', 'es'];

Route::get('/', function (Request $request) use ($supportedLocales) {
    $locale = 'en';

    $header = $request->header('Accept-Language', '');
    if ($header) {
        $languages = [];
        foreach (explode(',', $header) as $part) {
            [$tag, $q] = array_pad(explode(';q=', trim($part)), 2, '1');
            $languages[strtolower(trim($tag))] = (float) $q;
        }
        arsort($languages);

        foreach (array_keys($languages) as $tag) {
            $short = substr($tag, 0, 2);
            if (in_array($short, $supportedLocales)) {
                $locale = $short;
                break;
            }
        }
    }

    return redirect("/{$locale}");
});

Route::group(['prefix' => '{locale}', 'middleware' => 'setLocale', 'where' => ['locale' => 'fr|en|pl|es']], function () {
    Route::get('/', function () {
        return view('welcome');
    })->name('home');


    Route::get('/simulate', function () {
        // return view('simulate');
        // return view('/#simulate');
        return Redirect::to('/#simulate');

    })->name('simulate');

    Route::get('/about', function () {
        return view('about');
    })->name('about');

    Route::get('/contact', function () {
        return view('contact');
    })->name('contact');

    Route::get('/apply-loan', function () {
        return view('apply-loan');
    })->name('loan');

    Route::get('/loan/complete', [LoanController::class, 'showDocuments'])->name('loan.complete');

    Route::get('/terms', function () {
        return view('terms');
    })->name('terms');

    Route::get('/privacy', function () {
        return view('privacy');
    })->name('privacy');

    Route::get('/faq', function () {
        return view('faq');
    })->name('faq');

    Route::get('/services', function () {
        return view('services');
    })->name('services');

    Route::get('/services/auto-loan', function () {
        return view('service-d-auto-loan');
    })->name('services.auto');

    Route::get('/services/personal-loan', function () {
        return view('service-d-personal-loan');
    })->name('services.personal');

    Route::get('/services/home-loan', function () {
        return view('service-d-home-loan');
    })->name('services.home');

    Route::get('/services/study-loan', function () {
        return view('service-d-study-loan');
    })->name('services.study');

    Route::get('/services/business-loan', function () {
        return view('service-d-business-loan');
    })->name('services.business');

    Route::get('/services/bike-loan', function () {
        return view('service-d-bike-loan');
    })->name('services.bike');

});
Route::post('/loan/simulate', [LoanController::class, 'simulate'])->name('loan.simulate');
Route::post('/contact/send', [ContactController::class, 'sendMail'])->name('contact.send');
Route::post('/subscribe/send', [ContactController::class, 'subscribeMail'])->name('subscribe.send');
Route::post('/loan/request', [LoanController::class, 'sendMail'])->name('loan.request');
Route::post('/loan/documents', [LoanController::class, 'sendDocuments'])->name('loan.documents');

// ── Locale switcher (for auth pages without {locale} prefix) ────────────────
Route::get('/lang/{lang}', function (Request $request, $lang) {
    if (in_array($lang, ['fr', 'en', 'pl', 'es'])) {
        session(['locale' => $lang]);
    }
    $back = $request->headers->get('referer', url('/'));
    return redirect($back);
})->name('lang.switch');

// ── Authentication ──────────────────────────────────────────────────────────

// Account invitation / activation (public — no auth required)
Route::get('/invitation/{token}',  [InvitationController::class, 'show'])->name('invitation.show');
Route::post('/invitation/{token}', [InvitationController::class, 'activate'])->name('invitation.activate');

// Client login
Route::get('/login',  [ClientLoginController::class, 'showLoginForm'])->name('login')->middleware('guest');
Route::post('/login', [ClientLoginController::class, 'login'])->name('login.submit')->middleware('guest');
Route::post('/logout',[ClientLoginController::class, 'logout'])->name('logout');

// OTP verification
Route::get('/otp-verify',  [OtpController::class, 'show'])->name('otp.show');
Route::post('/otp-verify', [OtpController::class, 'verify'])->name('otp.verify');
Route::post('/otp-resend', [OtpController::class, 'resend'])->name('otp.resend');

// Forgot / reset password (clients)
Route::get('/forgot-password',         [ForgotPasswordController::class, 'show'])->name('password.request')->middleware('guest');
Route::post('/forgot-password',        [ForgotPasswordController::class, 'send'])->name('password.email')->middleware('guest');
Route::get('/reset-password/{token}',  [ResetPasswordController::class, 'show'])->name('password.reset')->middleware('guest');
Route::post('/reset-password',         [ResetPasswordController::class, 'reset'])->name('password.update')->middleware('guest');

// Staff login (admin / super-admin)
Route::get('/staff/login',  [StaffLoginController::class, 'showLoginForm'])->name('staff.login')->middleware('guest');
Route::post('/staff/login', [StaffLoginController::class, 'login'])->name('staff.login.submit')->middleware('guest');
Route::post('/staff/logout',[StaffLoginController::class, 'logout'])->name('staff.logout');

// ── Client dashboard ────────────────────────────────────────────────────────
Route::middleware(['auth', 'role:client'])->prefix('dashboard')->name('client.')->group(function () {
    Route::get('/', [ClientDashboardController::class, 'index'])->name('dashboard');
    Route::get('/loans',       [ClientLoanRequestController::class, 'index'])->name('loans');
    Route::get('/loans/{loan}',[ClientLoanRequestController::class, 'show'])->name('loans.show');
});

// ── Application mobile client (PWA) ─────────────────────────────────────────
Route::middleware(['auth', 'role:client', 'client.locale'])->prefix('app')->name('client.app.')->group(function () {
    Route::get('/',                    [ClientAppController::class, 'index'])->name('home');

    // Dossiers (alias "dossiers" pour la navigation + route loans conservee)
    Route::get('/dossiers',            [ClientAppController::class, 'loans'])->name('dossiers');
    Route::get('/loans',               [ClientAppController::class, 'loans'])->name('loans');
    Route::get('/loans/{loan}',        [ClientAppController::class, 'loanShow'])->name('loans.show');

    Route::get('/analytics',           [ClientAppController::class, 'analytics'])->name('analytics');
    Route::get('/profile',             [ClientAppController::class, 'profile'])->name('profile');
    Route::post('/profile',            [ClientAppController::class, 'updateProfile'])->name('profile.update');

    // Transferts : hub central (bouton FAB nav) + sous-pages
    Route::get('/transfers',           [\App\Http\Controllers\Client\TransferController::class, 'hub'])->name('transfers');
    Route::get('/transfer/send',       [\App\Http\Controllers\Client\TransferController::class, 'sendForm'])->name('transfer.send');
    Route::post('/transfer/send',      [\App\Http\Controllers\Client\TransferController::class, 'sendProcess'])->name('transfer.send.process');
    Route::get('/transfer/receive',    [\App\Http\Controllers\Client\TransferController::class, 'receive'])->name('transfer.receive');
    Route::get('/transfer/confirmation', [\App\Http\Controllers\Client\TransferController::class, 'confirmation'])->name('transfer.confirmation');

    Route::post('/locale', function (\Illuminate\Http\Request $request) {
        $locale = $request->input('locale', 'fr');
        if (in_array($locale, ['fr','en','pl','es'])) {
            $request->user()->update(['locale' => $locale]);
        }
        return back();
    })->name('locale');
});
Route::get('/manifest.json', [ClientAppController::class, 'manifest'])->name('pwa.manifest');
Route::get('/sw.js',         [ClientAppController::class, 'serviceWorker'])->name('pwa.sw');

// ── Admin dashboard ─────────────────────────────────────────────────────────
Route::middleware(['auth', 'role:admin|super-admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');

    // Gestion des demandes de prêt
    Route::get('/loans',                           [AdminLoanRequestController::class, 'index'])->name('loans.index');
    Route::get('/loans/create',                    [AdminLoanRequestController::class, 'create'])->name('loans.create');
    Route::post('/loans',                          [AdminLoanRequestController::class, 'store'])->name('loans.store');
    Route::get('/loans/{loan}',                    [AdminLoanRequestController::class, 'show'])->name('loans.show');
    Route::get('/loans/{loan}/edit',               [AdminLoanRequestController::class, 'edit'])->name('loans.edit');
    Route::put('/loans/{loan}',                    [AdminLoanRequestController::class, 'update'])->name('loans.update');
    Route::delete('/loans/{loan}',                 [AdminLoanRequestController::class, 'destroy'])->name('loans.destroy');
    Route::get('/loans/{loan}/contract',           [AdminLoanRequestController::class, 'contract'])->name('loans.contract');
    Route::post('/loans/{loan}/contract',          [AdminLoanRequestController::class, 'updateContract'])->name('loans.contract.update');
    Route::post('/loans/{loan}/validate',          [AdminLoanRequestController::class, 'validateLoan'])->name('loans.validate');
    Route::post('/loans/{loan}/signed',            [AdminLoanRequestController::class, 'markSigned'])->name('loans.signed');
    Route::patch('/loans/{loan}/status',           [AdminLoanRequestController::class, 'updateStatus'])->name('loans.status');

    // Modèles de contrats
    Route::get('/contract-templates',             [ContractTemplateController::class, 'index'])->name('contract-templates.index');
    Route::get('/contract-templates/create',      [ContractTemplateController::class, 'create'])->name('contract-templates.create');
    Route::post('/contract-templates',            [ContractTemplateController::class, 'store'])->name('contract-templates.store');
    Route::get('/contract-templates/{contractTemplate}/edit',    [ContractTemplateController::class, 'edit'])->name('contract-templates.edit');
    Route::put('/contract-templates/{contractTemplate}',         [ContractTemplateController::class, 'update'])->name('contract-templates.update');
    Route::delete('/contract-templates/{contractTemplate}',      [ContractTemplateController::class, 'destroy'])->name('contract-templates.destroy');
    Route::get('/contract-templates/{contractTemplate}/preview',          [ContractTemplateController::class, 'preview'])->name('contract-templates.preview');
    Route::get('/contract-templates/{contractTemplate}/docx-frame',      [ContractTemplateController::class, 'docxFrame'])->name('contract-templates.docx-frame');
    Route::get('/contract-templates/{contractTemplate}/download-docx',   [ContractTemplateController::class, 'downloadDocx'])->name('contract-templates.download-docx');
    Route::post('/contract-templates/{contractTemplate}/save-content',   [ContractTemplateController::class, 'saveContent'])->name('contract-templates.save-content');
    Route::post('/contract-templates/{contractTemplate}/reset-docx-edit',[ContractTemplateController::class, 'resetDocxEdit'])->name('contract-templates.reset-docx-edit');
    Route::get('/contract-templates/{contractTemplate}/missing-vars',   [ContractTemplateController::class, 'missingVars'])->name('contract-templates.missing-vars');

    // User management
    Route::get('/users',                        [UserManagementController::class, 'index'])->name('users');
    Route::post('/users',                       [UserManagementController::class, 'store'])->name('users.store');
    Route::put('/users/{user}',                 [UserManagementController::class, 'update'])->name('users.update');
    Route::delete('/users/{user}',              [UserManagementController::class, 'destroy'])->name('users.destroy');
    Route::post('/users/{user}/resend-invite',  [UserManagementController::class, 'resendInvitation'])->name('users.resend-invite');
});

// ── Super Admin dashboard ───────────────────────────────────────────────────
Route::middleware(['auth', 'role:super-admin'])->prefix('super-admin')->name('super-admin.')->group(function () {
    Route::get('/', [SuperAdminDashboardController::class, 'index'])->name('dashboard');
    Route::get('/roles', [SuperAdminDashboardController::class, 'roles'])->name('roles');
    Route::post('/users/{user}/role', [SuperAdminDashboardController::class, 'assignRole'])->name('users.role');

    // Vue globale de toutes les demandes
    Route::get('/loans',        [SuperAdminLoanRequestController::class, 'index'])->name('loans.index');

    // Gestion complète (mêmes droits que l'admin) — /create AVANT /{loan}
    Route::get('/loans/create',                    [AdminLoanRequestController::class, 'create'])->name('loans.create');
    Route::post('/loans',                          [AdminLoanRequestController::class, 'store'])->name('loans.store');
    Route::get('/loans/{loan}',                    [SuperAdminLoanRequestController::class, 'show'])->name('loans.show');
    Route::get('/loans/{loan}/edit',               [AdminLoanRequestController::class, 'edit'])->name('loans.edit');
    Route::put('/loans/{loan}',                    [AdminLoanRequestController::class, 'update'])->name('loans.update');
    Route::delete('/loans/{loan}',                 [AdminLoanRequestController::class, 'destroy'])->name('loans.destroy');
    Route::get('/loans/{loan}/contract',           [AdminLoanRequestController::class, 'contract'])->name('loans.contract');
    Route::post('/loans/{loan}/contract',          [AdminLoanRequestController::class, 'updateContract'])->name('loans.contract.update');
    Route::post('/loans/{loan}/validate',          [AdminLoanRequestController::class, 'validateLoan'])->name('loans.validate');
    Route::post('/loans/{loan}/signed',            [AdminLoanRequestController::class, 'markSigned'])->name('loans.signed');
    Route::patch('/loans/{loan}/status',           [AdminLoanRequestController::class, 'updateStatus'])->name('loans.status');
});
