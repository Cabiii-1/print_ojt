<?php

use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PrintController;
use App\Http\Controllers\PdfController;


Route::get('/', function () {
    return view('welcome');
});


Route::get('/sheet-data/{sheet?}', [UserController::class, 'index']);
Route::get('/get-sheets', [UserController::class, 'getSheets'])->name('getSheets');
Route::get('/get-sheet-data', [UserController::class, 'getSheetData'])->name('getSheetData');


Route::post('/print-preview', [PrintController::class, 'printPreview'])->name('print.preview');




Route::get('/print-pdf', [PdfController::class, 'generatePdf']);
