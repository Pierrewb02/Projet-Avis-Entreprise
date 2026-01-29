<?php

use Illuminate\Http\Request;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\CommentController;
use Illuminate\Support\Facades\Route;

// --- ROUTES PUBLIQUES ---
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login'])->name('login');

// --- ROUTES PROTÉGÉES (Nécessitent un Token) ---
Route::middleware('auth:sanctum')->group(function () {
    
    // Récupérer le profil utilisateur
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    // Gestion des avis
    Route::get('/reviews', [ReviewController::class, 'index']);
    Route::post('/reviews', [ReviewController::class, 'store']);
    Route::delete('/reviews/{id}', [ReviewController::class, 'destroy']);

    // Gestion des réponses (Commentaires)
    // ON LA MET ICI pour que $request->user() fonctionne dans le contrôleur
    Route::post('/reviews/{reviewId}/comments', [CommentController::class, 'store']);

    // Statistiques pour le Dashboard
    Route::get('/reviews/stats', [ReviewController::class, 'stats']); 

    // Déconnexion
    Route::post('/logout', [AuthController::class, 'logout']);
});