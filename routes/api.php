<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PengaduanController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WhatsAppGroupController;
use App\Http\Controllers\PJUController;
use App\Http\Controllers\PanelController;

//
// Login Logout
Route::get('/getProfile', [AuthController::class, 'getProfile']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
// WhatsApp Group Routes

Route::post('/send-message', [WhatsAppGroupController::class, 'sendMessage']);

    // Count Routes
    Route::get('/pengaduan/count', [PengaduanController::class, 'count']);
    Route::get('/pjus/count', [PJUController::class, 'count']);
    Route::get('/pengaduan/monthlycount', [PengaduanController::class, 'monthlyCount']);

// Group all routes that require authentication with Sanctum
Route::middleware('auth:sanctum')->group(function () {

    // Pengaduan Routes
    Route::post('/pengaduan', [PengaduanController::class, 'create'])->middleware('role:dishub');  // To create a complaint
    Route::put('/pengaduan/{id}', [PengaduanController::class, 'update'])->middleware('role:admin');  // To update a complaint by ID
    Route::get('/pengaduan', [PengaduanController::class, 'view']); // To view all complaints
    Route::delete('/pengaduan/{id}', [PengaduanController::class, 'destroy'])->middleware('role:admin');  // To delete a complaint by ID
    Route::get('/pengaduan/{id}', [PengaduanController::class, 'exportToExcel'])->middleware('role:admin'); 
    // User Routes
    Route::get('/users', [UserController::class, 'view'])->middleware('role:superadmin'); 
    Route::get('/users/{id}', [UserController::class, 'show'])->middleware('role:superadmin'); 
    Route::post('/users', [UserController::class, 'create'])->middleware('role:superadmin'); 
    Route::put('/users/{id}', [UserController::class, 'update'])->middleware('role:superadmin'); 
    Route::delete('/users/{id}', [UserController::class, 'destroy'])->middleware('role:superadmin'); 


    // PJU Routes
    Route::get('/pjus', [PJUController::class, 'index']);
    Route::post('/pjus', [PJUController::class, 'store'])->middleware('role:admin'); 
    Route::put('/pjus/{id}', [PJUController::class, 'update'])->middleware('role:admin'); 
    Route::delete('/pjus/{id}', [PJUController::class, 'destroy'])->middleware('role:admin'); 
    Route::get('/pjus/no-tiang-baru', [PJUController::class, 'listNoTiangBaru']);



    // Panel Routes
    Route::get('/panels', [PanelController::class, 'index']);
    Route::get('/panels/no-app', [PanelController::class, 'listNoApp']);

    // Import Routes
    Route::post('/import-pengaduan', [PengaduanController::class, 'importExcel'])->middleware('role:admin'); 
    Route::post('/import-pju', [PJUController::class, 'importPju'])->middleware('role:admin'); 
});