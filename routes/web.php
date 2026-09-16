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
