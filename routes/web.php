<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

// Authentication routes
Route::get('/login', function () {
    return view('auth.login');
})->name('login');

Route::get('/register', function () {
    return view('auth.register');
})->name('register');

Route::post('/login', [App\Http\Controllers\AuthController::class, 'loginWeb'])->name('login.post');
Route::post('/register', [App\Http\Controllers\AuthController::class, 'registerWeb'])->name('register.post');

Route::post('/logout', function () {
    auth()->logout();
    return redirect('/');
})->name('logout');

// Password reset routes
Route::get('/password/reset', function () {
    return view('auth.passwords.email');
})->name('password.request');

Route::post('/password/email', [App\Http\Controllers\AuthController::class, 'sendResetLinkEmail'])->name('password.email');

Route::get('/password/reset/{token}', function ($token) {
    return view('auth.passwords.reset', ['token' => $token]);
})->name('password.reset');

Route::post('/password/reset', [App\Http\Controllers\AuthController::class, 'reset'])->name('password.update');

// OAuth routes (web)
Route::get('/auth/google', [App\Http\Controllers\OAuthController::class, 'redirectToGoogle'])->name('auth.google');
Route::get('/auth/google/callback', [App\Http\Controllers\OAuthController::class, 'handleGoogleCallback'])->name('auth.google.callback');
Route::get('/auth/google/url', [App\Http\Controllers\OAuthController::class, 'getGoogleAuthUrl'])->name('auth.google.url');

// AI routes (web)
Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/ai/suggestions', [App\Http\Controllers\AppointmentController::class, 'getAISuggestions'])->name('ai.suggestions');
    Route::get('/ai/analysis/{dentistId}', [App\Http\Controllers\AppointmentController::class, 'getAIAnalysis'])->name('ai.analysis');
    Route::get('/ai/predictions/{dentistId}', [App\Http\Controllers\AppointmentController::class, 'getAIPredictions'])->name('ai.predictions');
});

// Dashboard routes
Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
    
    Route::get('/profile', function () {
        return view('profile');
    })->name('profile');
});

// Patient routes
Route::middleware(['auth:sanctum', 'role:patient'])->group(function () {
    Route::get('/patient/appointments', function () {
        return view('patient.appointments');
    })->name('patient.appointments');
    
    Route::get('/patient/medical-history', function () {
        return view('patient.medical-history');
    })->name('patient.medical-history');
    
    Route::get('/patient/schedule', function () {
        return view('patient.schedule');
    })->name('patient.schedule');
});

// Dentist routes
Route::middleware(['auth:sanctum', 'role:dentist'])->group(function () {
    Route::get('/dentist/appointments', function () {
        return view('dentist.appointments');
    })->name('dentist.appointments');
    
    Route::get('/dentist/patients', function () {
        return view('dentist.patients');
    })->name('dentist.patients');
    
    Route::get('/dentist/schedule', function () {
        return view('dentist.schedule');
    })->name('dentist.schedule');
    
    Route::get('/dentist/statistics', function () {
        return view('dentist.statistics');
    })->name('dentist.statistics');
});

// Admin routes
Route::middleware(['auth:sanctum', 'role:admin'])->group(function () {
    Route::get('/admin/dashboard', function () {
        return view('admin.dashboard');
    })->name('admin.dashboard');
    
    Route::get('/admin/patients', function () {
        return view('admin.patients');
    })->name('admin.patients');
    
    Route::get('/admin/dentists', function () {
        return view('admin.dentists');
    })->name('admin.dentists');
    
    Route::get('/admin/appointments', function () {
        return view('admin.appointments');
    })->name('admin.appointments');
    
    Route::get('/admin/procedures', function () {
        return view('admin.procedures');
    })->name('admin.procedures');
    
    Route::get('/admin/specializations', function () {
        return view('admin.specializations');
    })->name('admin.specializations');
    
    Route::get('/admin/reports', function () {
        return view('admin.reports');
    })->name('admin.reports');
});

// Patient specific routes
Route::middleware(['auth:sanctum', 'role:patient'])->group(function () {
    Route::get('/patient/appointments', function () {
        return view('patient.appointments');
    })->name('patient.appointments');
    
    Route::get('/patient/medical-history', function () {
        return view('patient.medical-history');
    })->name('patient.medical-history');
    
    Route::get('/patient/schedule', function () {
        return view('patient.schedule');
    })->name('patient.schedule');
});

// Dentist specific routes
Route::middleware(['auth:sanctum', 'role:dentist'])->group(function () {
    Route::get('/dentist/appointments', function () {
        return view('dentist.appointments');
    })->name('dentist.appointments');
    
    Route::get('/dentist/patients', function () {
        return view('dentist.patients');
    })->name('dentist.patients');
    
    Route::get('/dentist/schedule', function () {
        return view('dentist.schedule');
    })->name('dentist.schedule');
    
    Route::get('/dentist/statistics', function () {
        return view('dentist.statistics');
    })->name('dentist.statistics');
});
