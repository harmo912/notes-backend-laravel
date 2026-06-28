<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Classe;

class ClasseController extends Controller
{
    // Liste pour enseignant (filtrée) ou admin (toutes)
    public function index(Request $request)
    {
        $user = $request->user();

        if ($user->role === 'admin') {
            return response()->json(Classe::orderBy('nom')->get());
        }

        $classes = DB::table('enseignant_matiere_classe')
            ->join('classes', 'enseignant_matiere_classe.classe_id', '=', 'classes.id')
            ->where('enseignant_matiere_classe.enseignant_id', $user->id)
            ->select('classes.id', 'classes.nom', 'classes.annee_academique')
            ->distinct()
            ->get();

        return response()->json($classes);
    }

    public function store(Request $request)
    {
        $request->validate([
            'nom'              => 'required|string|max:255',
            'annee_academique' => 'required|string|max:20',
        ]);

        $classe = Classe::create([
            'nom'              => $request->nom,
            'annee_academique' => $request->annee_academique,
        ]);

        return response()->json($classe, 201);
    }

    public function show($id)
    {
        return response()->json(Classe::findOrFail($id));
    }

    public function update(Request $request, $id)
    {
        $classe = Classe::findOrFail($id);

        $request->validate([
            'nom'              => 'sometimes|string|max:255',
            'annee_academique' => 'sometimes|string|max:20',
        ]);

        if ($request->has('nom'))              $classe->nom = $request->nom;
        if ($request->has('annee_academique')) $classe->annee_academique = $request->annee_academique;

        $classe->save();
        return response()->json($classe);
    }

    public function destroy($id)
    {
        Classe::findOrFail($id)->delete();
        return response()->json(['message' => 'Classe supprimée.']);
    }

    public function getEtudiants($id)
    {
        $classe = Classe::findOrFail($id);
        $etudiants = $classe->etudiants()->select('users.id', 'users.name', 'users.email')->get();
        return response()->json($etudiants);
    }
}