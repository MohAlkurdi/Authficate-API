<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\IntegrationController;
use App\Http\Controllers\UserController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

// Authentication Routes
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);
Route::get('/certificates/{certificateId}', [IntegrationController::class, 'searchCertificate']);
Route::get('/user/certificates/{memberId}', [IntegrationController::class, 'getUserCertificatesByMemberId']);


Route::middleware('auth:sanctum')->group(function () {
    Route::post('/get-certificates', [IntegrationController::class, 'getCertificates']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get("/user", [AuthController::class, "user"]);
});
