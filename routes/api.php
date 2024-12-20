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
Route::get('/pjus/by-panel/{panel_id}', [PJUController::class, 'getPJUByPanel']);
Route::post('/pengaduan/filter', [PengaduanController::class, 'filterPengaduan']);  // To create a complaint
    
Route::get('/pengaduan/monthlycount', [PengaduanController::class, 'monthlyCount_pengaduan']);

// Group all routes that require authentication with Sanctum
Route::middleware('auth:sanctum')->group(function () {

    // Pengaduan Routes
    Route::post('/pengaduan', [PengaduanController::class, 'create_pengaduan']);  // To create a complaint
    Route::get('/pengaduan/export_excel', [PengaduanController::class, 'exportExcel']); 
    Route::get('/export-template', [PengaduanController::class, 'exportTemplate']);
    Route::get('/pengaduan/exportWord',[PengaduanController::class, 'exportToWord']);
    Route::post('/import-pengaduan', [PengaduanController::class, 'import_pengaduan']); 
    Route::get('/panel/{panel_id}/validate', [PengaduanController::class, 'validatePanel']);
    Route::put('/pengaduan/{id_pengaduan}', [PengaduanController::class, 'update_pengaduan']);  // To update a complaint by ID
    Route::get('/pengaduan', [PengaduanController::class, 'get_pengaduan']); // To view all complaints
    Route::delete('/pengaduan/{id_pengaduan}', [PengaduanController::class, 'delete_pengaduan']);  // To delete a complaint by ID
    Route::get('/pengaduan/count', [PengaduanController::class, 'count_pengaduan']);
    Route::get('/pengaduan/{id_pengaduan}', [PengaduanController::class, 'get_detail_pengaduan']); 

    // User Routes
    Route::get('/users', [UserController::class, 'view']); 
    Route::post('/users', [UserController::class, 'create'])->middleware('role:superadmin'); 
    Route::put('/users/{id}', [UserController::class, 'update'])->middleware('role:superadmin'); 
    Route::delete('/users/{id}', [UserController::class, 'destroy'])->middleware('role:superadmin'); 
    Route::get('/users/{id}', [UserController::class, 'show']); 

    // PJU Routes
    Route::get('/pjus', [PJUController::class, 'index']);
    Route::get('/pjus/count', [PJUController::class, 'countPJU']);
    Route::post('/pjus', [PJUController::class, 'store'])->middleware('role:admin'); 
    Route::get('/pjus/by-panel/{panel_id}', [PJUController::class, 'getPJUByPanel']);
    Route::get('/pjus/no-tiang-baru', [PJUController::class, 'listNoTiangBaru']);
    Route::delete('/pjus/{id}', [PJUController::class, 'destroy'])->middleware('role:admin'); 
    Route::put('/pjus/{id}', [PJUController::class, 'update'])->middleware('role:admin'); 

    // Panel Routes
    Route::get('/panels', [PanelController::class, 'index']);
    Route::get('/panels/no-app', [PanelController::class, 'listNoApp']);

    // Import Routes
    Route::post('/import-pju', [PJUController::class, 'importPju'])->middleware('role:admin'); 
});