<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\DoctorController;
use App\Http\Controllers\FacilityController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ScreeningController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AqiController;


/*
|--------------------------------------------------------------------------
| Web Routes - ISHAP (Intelligent Screening for Health Awareness & Prevention)
|--------------------------------------------------------------------------
*/

// 1. Landing Page (Edukasi ISPA, Status Udara AQI, dan Alur Cepat)
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/api/aqi', [AqiController::class, 'getAqiByCoords']);

// 2. Alur Skrining Mandiri ISPA
Route::get('/screening', [ScreeningController::class, 'index'])->name('screening.index');
Route::post('/screening', [ScreeningController::class, 'submit'])->name('screening.submit');
Route::post('/screening/extract-symptoms', [ScreeningController::class, 'extractSymptoms'])->middleware('throttle:15,1')->name('screening.extract-symptoms');
Route::get('/screening/{sessionId}/result', [ScreeningController::class, 'result'])->name('screening.result');

// 3. Pencarian Fasilitas Kesehatan Terdekat (OpenStreetMap Real-time)
Route::get('/facilities', [FacilityController::class, 'index'])->name('facilities.index');

// 4. Direktori Konsultasi Dokter Mitra Telemedika
Route::get('/doctors', [DoctorController::class, 'index'])->name('doctors.index');

// Admin Area - Manajemen Pengetahuan AI
Route::prefix('admin')->group(function () {
    Route::get('/', [AdminController::class,'showUploadForm'])->name('admin');
    Route::get('/upload-medical-data', [App\Http\Controllers\AdminController::class, 'showUploadForm'])->name('admin.upload.form');
    Route::post('/upload-medical-data', [App\Http\Controllers\AdminController::class, 'storeFromPdf'])->name('admin.upload.store');
});