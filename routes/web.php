<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ClusteringController;
use App\Http\Controllers\GrafikController;
use App\Http\Controllers\InsightController;
use App\Http\Controllers\RekomendasiController;

Route::get('/', [DashboardController::class, 'landing'])->name('home');
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
Route::get('/clustering', [ClusteringController::class, 'index'])->name('clustering');
Route::get('/clustering/data', [ClusteringController::class, 'getData'])->name('clustering.data');
Route::get('/grafik', [GrafikController::class, 'index'])->name('grafik');
Route::get('/grafik/data', [ClusteringController::class, 'getGrafikData'])->name('grafik.data');
Route::get('/insight', [InsightController::class, 'index'])->name('insight');
Route::get('/rekomendasi', function () {
    return view('rekomendasi');
})->name('rekomendasi');

Route::get('/rekomendasi/data', [RekomendasiController::class, 'rekomendasi'])
    ->name('rekomendasi.data');