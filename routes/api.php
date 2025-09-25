<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\VideoController;

Route::post('auth/register', [AuthController::class, 'register']);
Route::post('auth/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function(){
    Route::post('reports', [ReportController::class, 'store']);
    Route::get('reports/{id}/status', [ReportController::class, 'status']);
    Route::get('reports/{id}/download', [ReportController::class, 'download']);

    Route::post('videos/upload', [VideoController::class, 'upload']);
});
