<?php

use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DentistController;
use App\Http\Controllers\OAuthController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\ProcedureController;
use App\Http\Controllers\SpecializationController;
use Illuminate\Support\Facades\Route;

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

// Public routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// OAuth routes
Route::get('/auth/google', [OAuthController::class, 'redirectToGoogle']);
Route::get('/auth/google/callback', [OAuthController::class, 'handleGoogleCallback']);
Route::get('/auth/google/url', [OAuthController::class, 'getGoogleAuthUrl']);

// Public data routes
Route::get('/specializations', [SpecializationController::class, 'all']);
Route::get('/procedures', [ProcedureController::class, 'all']);
Route::get('/procedures/categories', [ProcedureController::class, 'categories']);
Route::get('/procedures/category/{category}', [ProcedureController::class, 'byCategory']);

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    // Auth routes
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);
    Route::put('/profile', [AuthController::class, 'updateProfile']);

    // OAuth protected routes
    Route::post('/auth/google/revoke', [OAuthController::class, 'revokeGoogleToken']);

    // Appointments
    Route::get('/appointments', [AppointmentController::class, 'index']);
    Route::get('/appointments/available-slots', [AppointmentController::class, 'getAvailableSlots']);
    Route::get('/appointments/ai-suggestions', [AppointmentController::class, 'getAISuggestions']);
    Route::get('/appointments/ai-analysis/{dentistId}', [AppointmentController::class, 'getAIAnalysis']);
    Route::get('/appointments/ai-predictions/{dentistId}', [AppointmentController::class, 'getAIPredictions']);
    Route::post('/appointments', [AppointmentController::class, 'store']);
    Route::put('/appointments/{id}/status', [AppointmentController::class, 'updateStatus']);
    Route::post('/appointments/{id}/cancel', [AppointmentController::class, 'cancel']);

    // Patients
    Route::get('/patients', [PatientController::class, 'index'])->middleware('role:admin');
    Route::get('/patients/profile', [PatientController::class, 'show']);
    Route::get('/patients/{id}', [PatientController::class, 'show'])->middleware('role:admin|dentist');
    Route::put('/patients/profile', [PatientController::class, 'update']);
    Route::put('/patients/{id}', [PatientController::class, 'update'])->middleware('role:admin');
    Route::get('/patients/profile/medical-history', [PatientController::class, 'medicalHistory']);
    Route::get('/patients/{id}/medical-history', [PatientController::class, 'medicalHistory'])->middleware('role:admin|dentist');
    Route::get('/patients/profile/appointments', [PatientController::class, 'appointments']);
    Route::get('/patients/{id}/appointments', [PatientController::class, 'appointments'])->middleware('role:admin|dentist');
    Route::get('/patients/profile/statistics', [PatientController::class, 'statistics']);
    Route::get('/patients/{id}/statistics', [PatientController::class, 'statistics'])->middleware('role:admin|dentist');
    Route::post('/patients/{id}/medical-history', [PatientController::class, 'createMedicalHistory'])->middleware('role:dentist');

    // Dentists
    Route::get('/dentists', [DentistController::class, 'index']);
    Route::get('/dentists/profile', [DentistController::class, 'show'])->middleware('role:dentist');
    Route::get('/dentists/{id}', [DentistController::class, 'show']);
    Route::put('/dentists/profile', [DentistController::class, 'update'])->middleware('role:dentist');
    Route::put('/dentists/{id}', [DentistController::class, 'update'])->middleware('role:admin');
    Route::get('/dentists/profile/appointments', [DentistController::class, 'appointments'])->middleware('role:dentist');
    Route::get('/dentists/{id}/appointments', [DentistController::class, 'appointments'])->middleware('role:admin');
    Route::get('/dentists/profile/patients', [DentistController::class, 'patients'])->middleware('role:dentist');
    Route::get('/dentists/{id}/patients', [DentistController::class, 'patients'])->middleware('role:admin');
    Route::get('/dentists/profile/schedule', [DentistController::class, 'schedule'])->middleware('role:dentist');
    Route::get('/dentists/{id}/schedule', [DentistController::class, 'schedule'])->middleware('role:admin');
    Route::put('/dentists/profile/schedule', [DentistController::class, 'updateSchedule'])->middleware('role:dentist');
    Route::put('/dentists/{id}/schedule', [DentistController::class, 'updateSchedule'])->middleware('role:admin');
    Route::get('/dentists/profile/statistics', [DentistController::class, 'statistics'])->middleware('role:dentist');
    Route::get('/dentists/{id}/statistics', [DentistController::class, 'statistics'])->middleware('role:admin');

    // Dentist Dashboard specific routes
    Route::get('/dentist/appointments/today', [DentistController::class, 'getTodayAppointments'])->middleware('role:dentist');
    Route::get('/dentist/patients/recent', [DentistController::class, 'getRecentPatients'])->middleware('role:dentist');
    Route::get('/dentist/charts/appointments-status', [DentistController::class, 'getAppointmentsStatusChart'])->middleware('role:dentist');
    Route::get('/dentist/charts/monthly-revenue', [DentistController::class, 'getMonthlyRevenueChart'])->middleware('role:dentist');

    // Specializations (admin only)
    Route::apiResource('specializations', SpecializationController::class)->middleware('role:admin');

    // Procedures (admin only)
    Route::apiResource('procedures', ProcedureController::class)->middleware('role:admin');
    Route::get('/procedures', [ProcedureController::class, 'index']);
    Route::get('/procedures/{id}', [ProcedureController::class, 'show']);
});

// Health check
Route::get('/health', function () {
    return response()->json([
        'status' => 'ok',
        'timestamp' => now()->toISOString(),
        'service' => 'Odonto360 API',
        'version' => '1.0.0',
    ]);
});
