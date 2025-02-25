<?php

use App\Http\Controllers\USerController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PrintController;


Route::get('/', function () {
    return view('welcome');
});


Route::get('/sheet-data', [UserController::class, 'index']);



Route::post('/print-preview', [PrintController::class, 'printPreview'])->name('print.preview');
