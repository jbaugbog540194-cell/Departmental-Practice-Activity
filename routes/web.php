<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\PlantController;
use App\Http\Controllers\PlantReportController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/plants');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/login', [AuthenticatedSessionController::class, 'store'])->name('login.store');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');

    Route::get('/reports', [PlantReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/export/excel', [PlantReportController::class, 'exportExcel'])->name('reports.export.excel');
    Route::get('/reports/export/pdf', [PlantReportController::class, 'exportPdf'])->name('reports.export.pdf');

    Route::resource('plants', PlantController::class);
});
