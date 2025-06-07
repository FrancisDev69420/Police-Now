<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\UserController;
use App\Http\Controllers\API\OfficerController;
use App\Http\Controllers\API\ResidentController;
use App\Http\Controllers\API\EmergencyRequestController;
use App\Http\Controllers\API\CommunicationLogController;
use App\Http\Controllers\API\EvidenceFileController;
use App\Http\Controllers\API\SavedLocationController;
use App\Http\Controllers\API\AdminController;



/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Public Routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Protected Routes
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', [AuthController::class, 'user']);
    Route::post('/logout', [AuthController::class, 'logout']);
    
    // Users
    Route::apiResource('users', UserController::class);
    
    // Officers
    Route::apiResource('officers', OfficerController::class);
    Route::put('/officers/{officer}/duty-status', [OfficerController::class, 'updateDutyStatus']);
    
    // Residents
    Route::apiResource('residents', ResidentController::class);
    
    // Emergency Requests
    Route::apiResource('emergency-requests', EmergencyRequestController::class);
    Route::put('/emergency-requests/{emergencyRequest}/status', [EmergencyRequestController::class, 'updateStatus']);
    
    // Communication Logs
    Route::apiResource('communication-logs', CommunicationLogController::class);
    
    // Evidence Files
    Route::apiResource('evidence-files', EvidenceFileController::class);
    Route::post('/evidence-files/upload', [EvidenceFileController::class, 'upload']);
    
    // Saved Locations
    Route::apiResource('saved-locations', SavedLocationController::class);
    
    // Admin-only routes
    Route::prefix('admin')->middleware(['auth:sanctum', 'admin'])->group(function () {
        Route::post('/officers', [AdminController::class, 'createOfficer']);
        Route::get('/officers', [AdminController::class, 'getAllOfficers']);
        Route::put('/officers/{id}', [AdminController::class, 'updateOfficer']);
        Route::delete('/officers/{id}', [AdminController::class, 'deleteOfficer']);
        
        Route::get('/residents', [AdminController::class, 'getAllResidents']);
        Route::put('/residents/{id}', [AdminController::class, 'updateResident']);
        Route::delete('/residents/{id}', [AdminController::class, 'deleteResident']);
    });
});
