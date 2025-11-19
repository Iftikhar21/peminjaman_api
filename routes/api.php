<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoriesController;
use App\Http\Controllers\ClassBorrowerController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\MajorController;
use App\Http\Controllers\PeminjamanController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\StatusBorrowerController;
use App\Http\Controllers\UserDetailController;
use Illuminate\Support\Facades\Route;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout']);
Route::middleware('auth:sanctum')->group(function () {

    Route::middleware('auth:sanctum')->prefix('peminjaman')->group(function () {
        Route::get('/me', [PeminjamanController::class, 'myPeminjaman']); // history user
        Route::get('/active', [PeminjamanController::class, 'activePeminjaman']); // semua peminjaman aktif
        Route::post('/create', [PeminjamanController::class, 'store']); // buat peminjaman baru
        Route::post('/{id}/return', [PeminjamanController::class, 'return']); // kembalikan produk
        Route::post('/check-pin', [PeminjamanController::class, 'checkPin']); // cek PIN override
    });


    Route::middleware('role:user')->group(function () {
        Route::prefix('/me')->group(function () {
            Route::get('/', [UserDetailController::class, 'show']);
            Route::put('/update', [UserDetailController::class, 'update']);
        });
    });

    Route::middleware('role:admin')->group(function () {

        Route::prefix('/admin')->group(function () {
            Route::get('/', [AdminController::class, 'show']);
            Route::put('/update', [AdminController::class, 'update']);
        });
        
        Route::prefix('/user')->group(function () {
            Route::get('/', [AuthController::class, 'index']);
            Route::get('/{id}', [AuthController::class, 'show']);
            Route::post('/create-user', [AuthController::class, 'create']);
            Route::put('/{id}/update', [AuthController::class, 'update']);
            Route::delete('/{id}/delete', [AuthController::class, 'destroy']);
        });
        
        
        Route::prefix('/role')->group(function () {
            Route::get('/', [RoleController::class, 'index']);
            Route::get('/{id}', [RoleController::class, 'show']);
            Route::post('/create', [RoleController::class, 'store']);
            Route::put('/{id}/update', [RoleController::class, 'update']);
            Route::delete('/{id}/delete', [RoleController::class, 'destroy']);
        });

        Route::prefix('/categories')->group(function () {
            Route::get('/', [CategoriesController::class, 'index']);
            Route::get('/{id}', [CategoriesController::class, 'show']);
            Route::post('/create', [CategoriesController::class, 'store']);
            Route::put('/{id}/update', [CategoriesController::class, 'update']);
            Route::delete('/{id}/delete', [CategoriesController::class, 'destroy']);
        });

        Route::prefix('/class')->group(function () {
            Route::get('/', [ClassBorrowerController::class, 'index']);
            Route::get('/{id}', [ClassBorrowerController::class, 'show']);
            Route::post('/create', [ClassBorrowerController::class, 'store']);
            Route::put('/{id}/update', [ClassBorrowerController::class, 'update']);
            Route::delete('/{id}/delete', [ClassBorrowerController::class, 'destroy']);
        });

        Route::prefix('/location')->group(function () {
            Route::get('/', [LocationController::class, 'index']);
            Route::get('/{id}', [LocationController::class, 'show']);
            Route::post('/create', [LocationController::class, 'store']);
            Route::put('/{id}/update', [LocationController::class, 'update']);
            Route::delete('/{id}/delete', [LocationController::class, 'destroy']);
        });

        Route::prefix('/major')->group(function () {
            Route::get('/', [MajorController::class, 'index']);
            Route::get('/{id}', [MajorController::class, 'show']);
            Route::post('/create', [MajorController::class, 'store']);
            Route::put('/{id}/update', [MajorController::class, 'update']);
            Route::delete('/{id}/delete', [MajorController::class, 'destroy']);
        });

        Route::prefix('/status')->group(function () {
            Route::get('/', [StatusBorrowerController::class, 'index']);
            Route::get('/{id}', [StatusBorrowerController::class, 'show']);
            Route::post('/create', [StatusBorrowerController::class, 'store']);
            Route::put('/{id}/update', [StatusBorrowerController::class, 'update']);
            Route::delete('/{id}/delete', [StatusBorrowerController::class, 'destroy']);
        });

        Route::prefix('/product')->group(function () {
            Route::get('/', [ProductController::class, 'index']);
            Route::get('/{id}', [ProductController::class, 'show']);
            Route::post('/create', [ProductController::class, 'store']);
            Route::put('/{id}/update', [ProductController::class, 'update']);
            Route::delete('/{id}/delete', [ProductController::class, 'destroy']);
        });
    });
});

