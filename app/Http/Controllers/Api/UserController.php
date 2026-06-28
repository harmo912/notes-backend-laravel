<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    // Liste tous les users (filtrable par rôle)
    public function index(Request $request)
    {
        $query = User::orderBy('name');
        if ($request->has('role')) {
            $query->where('role', $request->role);
        }
        return response()->json($query->get(['id', 'name', 'email', 'role', 'created_at']));
    }

    // Créer un user
    public function store(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'role'     => 'required|in:admin,enseignant,etudiant',
        ]);

        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'role'     => $request->role,
        ]);

        return response()->json($user, 201);
    }

    // Détail d'un user
    public function show($id)
    {
        return response()->json(User::findOrFail($id));
    }

    // Modifier un user
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'name'     => 'sometimes|string|max:255',
            'email'    => 'sometimes|email|unique:users,email,' . $id,
            'password' => 'sometimes|string|min:6',
            'role'     => 'sometimes|in:admin,enseignant,etudiant',
        ]);

        if ($request->has('name'))     $user->name  = $request->name;
        if ($request->has('email'))    $user->email = $request->email;
        if ($request->has('role'))     $user->role  = $request->role;
        if ($request->has('password')) $user->password = Hash::make($request->password);

        $user->save();
        return response()->json($user);
    }

    // Supprimer un user
    public function destroy($id)
    {
        User::findOrFail($id)->delete();
        return response()->json(['message' => 'Utilisateur supprimé.']);
    }

    // Stats pour le dashboard admin
    public function stats()
    {
        return response()->json([
            'nb_etudiants'   => User::where('role', 'etudiant')->count(),
            'nb_enseignants' => User::where('role', 'enseignant')->count(),
            'nb_admins'      => User::where('role', 'admin')->count(),
            'nb_total'       => User::count(),
        ]);
    }
}