<?php

use App\Http\Controllers\AdherentController;
use App\Http\Controllers\AdherentSpaceController;
use App\Http\Controllers\AdhesionController;
use App\Http\Controllers\Auth\AdherentLoginController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\PasswordController;
use App\Http\Controllers\CertificatMedicalController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PaiementController;
use App\Http\Middleware\ValidateCsrfToken;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

Route::get('/robots.txt', function () {
    $baseUrl = rtrim((string) config('app.url'), '/');

    return response("User-agent: *\nDisallow: /dashboard\nDisallow: /adherents\nDisallow: /certificats\nDisallow: /adhesions\nDisallow: /password\nSitemap: {$baseUrl}/sitemap.xml\n", 200)
        ->header('Content-Type', 'text/plain; charset=UTF-8')
        ->header('Cache-Control', 'public, max-age=3600');
})->withoutMiddleware([
    EncryptCookies::class,
    AddQueuedCookiesToResponse::class,
    StartSession::class,
    ShareErrorsFromSession::class,
    ValidateCsrfToken::class,
    PreventRequestForgery::class,
]);

Route::get('/sitemap.xml', function () {
    $baseUrl = rtrim((string) config('app.url'), '/');
    $today = now()->toDateString();
    $urls = collect([
        '/',
        '/login',
        '/espace-adherent/login',
    ])->map(fn (string $path): string => sprintf(
        "    <url><loc>%s</loc><lastmod>%s</lastmod></url>",
        e($baseUrl.$path),
        $today,
    ))->implode("\n");

    return response("<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n<urlset xmlns=\"http://www.sitemaps.org/schemas/sitemap/0.9\">\n{$urls}\n</urlset>\n", 200)
        ->header('Content-Type', 'application/xml; charset=UTF-8')
        ->header('Cache-Control', 'public, max-age=3600');
})->withoutMiddleware([
    EncryptCookies::class,
    AddQueuedCookiesToResponse::class,
    StartSession::class,
    ShareErrorsFromSession::class,
    ValidateCsrfToken::class,
    PreventRequestForgery::class,
]);

Route::get('/', function () {
    if (Auth::guard('web')->check()) {
        return redirect()->route('dashboard');
    }

    if (Auth::guard('adherent')->check()) {
        return redirect()->route('adherent.dashboard');
    }

    return view('welcome');
})->name('home');

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'show'])->name('login');
    Route::post('/login', [LoginController::class, 'store']);
});

Route::prefix('espace-adherent')->name('adherent.')->group(function () {
    Route::middleware('guest:adherent')->group(function () {
        Route::get('/login', [AdherentLoginController::class, 'show'])->name('login');
        Route::post('/login', [AdherentLoginController::class, 'store'])->name('login.store');
    });

    Route::middleware('auth:adherent')->group(function () {
        Route::get('/', [AdherentSpaceController::class, 'dashboard'])->name('dashboard');
        Route::post('/logout', [AdherentLoginController::class, 'destroy'])->name('logout');
    });
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [LoginController::class, 'destroy'])->name('logout');

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/password/change', [PasswordController::class, 'show'])->name('password.show');
    Route::post('/password/update', [PasswordController::class, 'update'])->name('password.update');

    Route::resource('adherents', AdherentController::class);
    Route::patch('adherents/{adherent}/restore', [AdherentController::class, 'restore'])->name('adherents.restore');

    Route::get('certificats', [CertificatMedicalController::class, 'index'])->name('certificats.index');
    Route::get('certificats/export', [CertificatMedicalController::class, 'export'])->name('certificats.export');
    Route::get('certificats/{certificat}/download', [CertificatMedicalController::class, 'download'])->name('certificats.download');

    Route::get('adhesions', [AdhesionController::class, 'index'])->name('adhesions.index');
    Route::get('adhesions/export', [AdhesionController::class, 'export'])->name('adhesions.export');
    Route::get('adhesions/{adhesion}', [AdhesionController::class, 'show'])->name('adhesions.show');

    Route::get('adhesions/{adhesion}/paiements/create', [PaiementController::class, 'create'])->name('paiements.create');
    Route::post('adhesions/{adhesion}/paiements', [PaiementController::class, 'store'])->name('paiements.store');
});
