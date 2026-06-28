<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    // Connexion et génération du Token Sanctum
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        // Vérification de l'utilisateur et du mot de passe
        if (! $user || ! Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'Identifiants incorrects.'
            ], 401);
        }

        // Création du token en incluant le rôle dans les capacités (abilities)
        $token = $user->createToken('auth_token', [$user->role])->plainTextToken;

        return response()->json([
            'message' => 'Connexion réussie',
            'token' => $token, // Modifié : de 'access_token' vers 'token'
            'token_type' => 'Bearer',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
            ]
        ]);
    }

    // Déconnexion (Suppression du token actuel)
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Déconnexion réussie.'
        ]);
    }

    // Récupérer l'utilisateur connecté
    public function me(Request $request)
    {
        return response()->json($request->user());
    }
}