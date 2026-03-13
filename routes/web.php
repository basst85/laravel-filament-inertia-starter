<?php

use App\Http\Controllers\PageController;
use Illuminate\Support\Facades\Route;

Route::get('/', [PageController::class, 'home'])->name('home');
Route::get('/contact', [PageController::class, 'contact'])->name('contact');

Route::get('/{slug}', [PageController::class, 'show'])
    ->where('slug', '^(?!admin$|livewire$|contact$).+')
    ->name('pages.show');
