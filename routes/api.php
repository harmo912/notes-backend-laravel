<?php

use Illuminate\Support\Facades\Artisan;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ClasseController;
use App\Http\Controllers\Api\MatiereController;
use App\Http\Controllers\Api\NoteController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\AffectationController;
use Illuminate\Support\Facades\Route;

// --- Routes Publiques ---
Route::post('/login', [AuthController::class, 'login']);

// --- Routes Sécurisées par Sanctum ---
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);
    Route::get('/login', function() {
    return response()->json(['message' => 'Utilisez POST pour vous connecter.'], 405);
})->name('login');

    // Classes & Matières
    Route::get('/classes', [ClasseController::class, 'index']);
    Route::get('/matieres', [MatiereController::class, 'index']);
    Route::apiResource('classes', ClasseController::class)->except(['index']);
    Route::apiResource('matieres', MatiereController::class)->except(['index']);
    Route::get('/classes/{id}/etudiants', [ClasseController::class, 'getEtudiants']);

    // Notes
    Route::get('/notes/moyennes', [NoteController::class, 'moyennes']);
    Route::apiResource('notes', NoteController::class);

    // Users (admin)
    Route::get('/users/stats', [UserController::class, 'stats']);
    Route::apiResource('users', UserController::class);

    // Affectations (admin)
    Route::get('/affectations', [AffectationController::class, 'index']);
    Route::post('/affectations', [AffectationController::class, 'store']);
    Route::delete('/affectations/{id}', [AffectationController::class, 'destroy']);
    Route::post('/affectations/etudiant', [AffectationController::class, 'affecterEtudiant']);
    // Route temporaire pour seeder — À SUPPRIMER APRÈS
    Route::get('/setup-db', function () {
    try {
        Artisan::call('migrate', ['--force' => true]);
        Artisan::call('db:seed', ['--force' => true]);
        return response()->json([
            'message' => 'Terminé !',
            'migrate' => Artisan::output(),
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString(),
        ], 500);
    }
});
});