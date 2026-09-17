<?php

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

Route::get('/dashboard', function () {
    return view('dashboard.index');
})->name('dashboard.index');

Route::get('/dashboard/registration', function () {
    return view('dashboard.registration.index');
})->name('dashboard.registration.index');

Route::get('/dashboard/registration/{competition}', function (string $competition) {
    return view('dashboard.registration.show', ['competition' => $competition]);
})->whereIn('competition', ['mcc', 'bcc', 'bpc'])->name('dashboard.registration.show');

Route::get('/dashboard/submission', function () {
    return view('dashboard.submission.index');
})->name('dashboard.submission.index');

Route::get('/dashboard/submission/{competition}/{stage?}', function (string $competition, ?string $stage = null) {
    return view('dashboard.submission.show', ['competition' => $competition, 'stage' => $stage]);
})->whereIn('competition', ['mcc', 'bcc', 'bpc'])
    ->where('stage', '[a-z0-9-]+')
    ->name('dashboard.submission.show');

Route::get('/dashboard/summit-pass', function () {
    return view('dashboard.summit-pass.index');
})->name('dashboard.summit-pass.index');

Route::get('/dashboard/profile', function () {
    return view('dashboard.profile.index');
})->name('dashboard.profile.index');
