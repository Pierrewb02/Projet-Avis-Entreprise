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
// --- LOGIN ---
public function login(Request $request) {
    $fields = $request->validate([
        'email' => 'required|email',
        'password' => 'required|string'
    ]);

    $user = User::where('email', $fields['email'])->first();

    if(!$user || !Hash::check($fields['password'], $user->password)) {
        return response()->json(['message' => 'Identifiants invalides'], 401);
    }

    $token = $user->createToken('myapptoken')->plainTextToken;

    return response()->json([
        'user' => [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'role' => $user->role,
            'company_name' => $user->company_name // On renvoie aussi le nom de l'entreprise
        ],
        'token' => $token
    ], 200);
}

// --- REGISTER ---
public function register(Request $request) {
    try {
        $fields = $request->validate([
            'name' => 'required|string',
            'email' => 'required|string|unique:users,email',
            'password' => 'required|string|min:6',
            'role' => 'required|string', // Obligatoire : user ou entreprise
            'company_name' => 'nullable|string' // Optionnel sauf si rôle = entreprise
        ]);

        $user = User::create([
            'name' => $fields['name'],
            'email' => $fields['email'],
            'password' => bcrypt($fields['password']),
            'role' => $fields['role'],
            'company_name' => ($fields['role'] === 'entreprise') ? $fields['company_name'] : null
        ]);

        return response()->json([
            'message' => 'Utilisateur créé',
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

