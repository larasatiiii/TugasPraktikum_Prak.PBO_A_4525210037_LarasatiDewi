<?php

use App\Http\Controllers\AnggotaController;
use App\Http\Controllers\BukuController;
use App\Http\Controllers\PeminjamanBukuController;
use Illuminate\Support\Facades\Route;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

Route::get('/buku/get', [BukuController::class, 'index']);
Route::post('/buku/create', [BukuController::class, 'store']);
Route::patch('/buku/update/{id}', [BukuController::class, 'update']);
Route::delete('/buku/delete/{id}', [BukuController::class, 'destroy']);

Route::get('/anggota/get', [AnggotaController::class, 'index']);
Route::post('/anggota/create', [AnggotaController::class, 'store']);
Route::patch('/anggota/update/{id}', [AnggotaController::class, 'update']);
Route::delete('/anggota/delete/{id}', [AnggotaController::class, 'destroy']);

Route::get('/pinjam-buku/get', [PeminjamanBukuController::class, 'index']);
Route::post('/pinjam-buku/create', [PeminjamanBukuController::class, 'store']);
Route::patch('/pinjam-buku/update/{id}', [PeminjamanBukuController::class, 'update']);
Route::delete('/pinjam-buku/delete/{id}', [PeminjamanBukuController::class, 'destroy']);
