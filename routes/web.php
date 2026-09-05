<?php

use App\Http\Controllers\DoctorController;
use App\Http\Controllers\FacilityController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ScreeningController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AqiController;


/*
|--------------------------------------------------------------------------
| Web Routes - ISHAP Skrining Mandiri ISPA (Laravel MVC)
|--------------------------------------------------------------------------
*/

// 1. Landing Page (Edukasi ISPA, Status Udara AQI, dan Alur Cepat)
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/api/aqi', [AqiController::class, 'getAqiByCoords']);

// 2. Alur Skrining Mandiri ISPA
Route::get('/screening', [ScreeningController::class, 'index'])->name('screening.index');
Route::post('/screening', [ScreeningController::class, 'submit'])->name('screening.submit');
Route::get('/screening/{sessionId}/result', [ScreeningController::class, 'result'])->name('screening.result');

// 3. Pencarian Fasilitas Kesehatan Terdekat (OpenStreetMap Real-time)
Route::get('/facilities', [FacilityController::class, 'index'])->name('facilities.index');

// 4. Direktori Konsultasi Dokter Mitra Telemedika
Route::get('/doctors', [DoctorController::class, 'index'])->name('doctors.index');
