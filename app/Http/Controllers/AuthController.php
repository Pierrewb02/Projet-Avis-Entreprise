<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    
    // Connexion (Pour ton /login)
    // Connexion
public function login(Request $request) {
    $fields = $request->validate([
        'email' => 'required|email', // On force le format email
        'password' => 'required|string'
    ]);

    $user = User::where('email', $fields['email'])->first();

    // Debug : on vérifie si l'utilisateur existe ET si le mot de passe match
    if(!$user || !Hash::check($fields['password'], $user->password)) {
        return response()->json(['message' => 'Identifiants invalides'], 401);
    }

    $token = $user->createToken('myapptoken')->plainTextToken;

    return response()->json([
        'user' => [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'role' => $user->role
        ],
        'token' => $token
    ], 200);
}

public function register(Request $request) {
    try {
        $fields = $request->validate([
            'name' => 'required|string',
            'email' => 'required|string|unique:users,email',
            'password' => 'required|string|min:6',
            'role' => 'nullable|string' // On autorise le champ role
        ]);

        $user = User::create([
            'name' => $fields['name'],
            'email' => $fields['email'],
            'password' => bcrypt($fields['password']),
            // ICI : On prend le rôle envoyé par Vue, sinon on met 'user' par défaut
            'role' => $request->role ?? 'user' 
        ]);

        return response()->json([
            'message' => 'Utilisateur créé : ' . $user->email,
            'user' => $user
        ], 201);

    } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage()], 500);
    }
}

    public function logout(Request $request) {
        $request->user()->currentAccessToken()->delete();
        return ['message' => 'Déconnecté'];
    }
}

