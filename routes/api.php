<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PengaduanController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WhatsAppGroupController;
use App\Http\Controllers\PJUController;
use App\Http\Controllers\PanelController;


// Login Logout
Route::get('/getProfile', [AuthController::class, 'getProfile']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');

// Pengaduan
Route::post('/pengaduan', [PengaduanController::class, 'create']); // Untuk membuat pengaduan
Route::put('/pengaduan/{id}', [PengaduanController::class, 'update']); // Untuk memperbarui pengaduan berdasarkan ID
Route::get('/pengaduan/count', [PengaduanController::class, 'count']);
Route::get('/pengaduan/monthlycount', [PengaduanController::class, 'monthlyCount']);
Route::get('/pengaduan', [PengaduanController::class, 'view']); // Untuk melihat semua pengaduan
Route::delete('/pengaduan/{id}', [PengaduanController::class, 'destroy']); // Untuk menghapus pengaduan berdasarkan ID


// User
Route::get('/users', [UserController::class, 'view']);
Route::get('/users/{id}', [UserController::class, 'show']);
Route::post('/users', [UserController::class, 'create']);
Route::put('/users/{id}', [UserController::class, 'update']);
Route::delete('/users/{id}', [UserController::class, 'destroy']);

// grup wa
Route::post('/send-message', [WhatsAppGroupController::class, 'sendMessage']);

// PJU
Route::get('/pjus', [PJUController::class, 'index']);
Route::get('/pjus/search/{key}', [PJUController::class, 'search']);
Route::post('/pjus', [PJUController::class, 'store']);
Route::put('/pjus/{id}', [PJUController::class, 'update']);
Route::delete('/pjus/{id}', [PJUController::class, 'destroy']);

// count
Route::get('/pengaduan/count', [PengaduanController::class, 'count']);
Route::get('/pjus/count', [PJUController::class, 'count']);

// Panel
Route::get('/panels', [PanelController::class, 'index']);