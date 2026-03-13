<?php

use App\Http\Controllers\Api\ContactFormController;
use Illuminate\Support\Facades\Route;

Route::post('/contact', [ContactFormController::class, 'store'])->name('api.contact.store');
