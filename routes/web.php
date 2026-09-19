<?php

use App\Http\Controllers\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Auth\EmailVerificationOtpController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('pages.home.index');
})->name('home');

Route::get('/pre-event-1', function () {
    return view('pages.pre-event-1.index');
})->name('pre-event-1.index');

Route::get('/pre-event-2', function () {
    return view('pages.pre-event-2.index');
})->name('pre-event-2.index');

Route::get('/main-event', function () {
    return view('pages.main-event.index');
})->name('main-event.index');

Route::middleware('auth')->group(function () {
    Route::get('/email/verify', fn () => response()->noContent())
        ->name('verification.notice');

    Route::post('/email/verify-otp', EmailVerificationOtpController::class)
        ->middleware('throttle:6,1')
        ->name('verification.verify-otp');

    Route::post('/email/verification-notification', EmailVerificationNotificationController::class)
        ->middleware('throttle:6,1')
        ->name('verification.send');
});

Route::middleware(['auth', 'verified'])->prefix('dashboard')->name('dashboard.')->group(function () {
    Route::get('/', function () {
        return view('dashboard.index');
    })->name('index');

    Route::get('/registration', function () {
        return view('dashboard.registration.index');
    })->name('registration.index');

    Route::get('/registration/{competition}', function (string $competition) {
        return view('dashboard.registration.show', ['competition' => $competition]);
    })->whereIn('competition', ['mcc', 'bcc', 'bpc'])->name('registration.show');

    Route::get('/submission', function () {
        return view('dashboard.submission.index');
    })->name('submission.index');

    Route::get('/submission/{competition}/{stage?}', function (string $competition, ?string $stage = null) {
        return view('dashboard.submission.show', ['competition' => $competition, 'stage' => $stage]);
    })->whereIn('competition', ['mcc', 'bcc', 'bpc'])
        ->where('stage', '[a-z0-9-]+')
        ->name('submission.show');

    Route::get('/summit-pass', function () {
        return view('dashboard.summit-pass.index');
    })->name('summit-pass.index');

    Route::get('/profile', function () {
        return view('dashboard.profile.index');
    })->name('profile.index');
});
