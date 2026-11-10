<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\KantingoController;
use App\Http\Controllers\Api\MenuController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\TableController;

/*
|--------------------------------------------------------------------------
| API Routes — Project Kantingo (Laravel 11 RESTful API)
|--------------------------------------------------------------------------
| Mendukung ≥ 20 endpoint aktif untuk katalog menu, meja, checkout,
| autentikasi kasir/admin, status pesanan, pembayaran, dan dashboard.
*/

// ==========================================
// 1. Endpoint Versi 1 (v1) Terstruktur
// ==========================================
Route::prefix('v1')->group(function () {

    // --- A. Autentikasi & Akun ---
    Route::prefix('auth')->group(function () {
        Route::post('/register', [AuthController::class, 'register']);
        Route::post('/login', [AuthController::class, 'login']);

        Route::middleware('auth:sanctum')->group(function () {
            Route::get('/profile', [AuthController::class, 'profile']);
            Route::post('/logout', [AuthController::class, 'logout']);
        });
    });

    // --- B. Kategori Menu (CRUD) ---
    Route::get('/categories', [CategoryController::class, 'index']);
    Route::post('/categories', [CategoryController::class, 'store']);
    Route::get('/categories/{id}', [CategoryController::class, 'show']);
    Route::put('/categories/{id}', [CategoryController::class, 'update']);
    Route::delete('/categories/{id}', [CategoryController::class, 'destroy']);

    // --- C. Menu Makanan & Minuman (CRUD + Stok) ---
    Route::get('/menus', [MenuController::class, 'index']);
    Route::post('/menus', [MenuController::class, 'store']);
    Route::get('/menus/{id}', [MenuController::class, 'show']);
    Route::put('/menus/{id}', [MenuController::class, 'update']);
    Route::delete('/menus/{id}', [MenuController::class, 'destroy']);
    Route::patch('/menus/{id}/stock', [MenuController::class, 'updateStock']);

    // --- D. Meja & Pemindaian QR Code (CRUD + Scan) ---
    Route::get('/tables', [TableController::class, 'index']);
    Route::post('/tables', [TableController::class, 'store']);
    Route::get('/tables/{id}', [TableController::class, 'show']);
    Route::put('/tables/{id}', [TableController::class, 'update']);
    Route::delete('/tables/{id}', [TableController::class, 'destroy']);
    Route::get('/tables/scan/{token}', [TableController::class, 'scanQr']);

    // --- E. Pemesanan & Transaksi (Orders) ---
    Route::get('/orders', [OrderController::class, 'index']);
    Route::post('/orders', [OrderController::class, 'store']);
    Route::get('/orders/{id}', [OrderController::class, 'show']);
    Route::patch('/orders/{id}/status', [OrderController::class, 'updateStatus']);
    Route::delete('/orders/{id}', [OrderController::class, 'destroy']);
    Route::post('/orders/{id}/pay', [OrderController::class, 'payOrder']);

    // --- F. Dashboard & Laporan ---
    Route::get('/dashboard/summary', [DashboardController::class, 'summary']);
});

// ==========================================
// 2. Endpoint Legacy / Kompatibilitas Frontend
// ==========================================
Route::get('/menus', [KantingoController::class, 'getMenus']);
Route::get('/tables', [KantingoController::class, 'getTables']);
Route::post('/orders', [KantingoController::class, 'storeOrder']);
