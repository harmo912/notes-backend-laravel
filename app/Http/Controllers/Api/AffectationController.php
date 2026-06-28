<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AffectationController extends Controller
{
    // Lister toutes les affectations enseignant→matière→classe
    public function index()
    {
        $affectations = DB::table('enseignant_matiere_classe')
            ->join('users',    'enseignant_matiere_classe.enseignant_id', '=', 'users.id')
            ->join('matieres', 'enseignant_matiere_classe.matiere_id',   '=', 'matieres.id')
            ->join('classes',  'enseignant_matiere_classe.classe_id',    '=', 'classes.id')
            ->select(
                'enseignant_matiere_classe.id',
                'users.id as enseignant_id',
                'users.name as enseignant_nom',
                'matieres.id as matiere_id',
                'matieres.nom as matiere_nom',
                'matieres.code as matiere_code',
                'classes.id as classe_id',
                'classes.nom as classe_nom',
                'enseignant_matiere_classe.annee'
            )
            ->orderBy('users.name')
            ->get();

        return response()->json($affectations);
    }

    // Créer une affectation
    public function store(Request $request)
    {
        $request->validate([
            'enseignant_id' => 'required|exists:users,id',
            'matiere_id'    => 'required|exists:matieres,id',
            'classe_id'     => 'required|exists:classes,id',
            'annee'         => 'required|string',
        ]);

        // Vérifier doublon
        $exists = DB::table('enseignant_matiere_classe')
            ->where('enseignant_id', $request->enseignant_id)
            ->where('matiere_id',    $request->matiere_id)
            ->where('classe_id',     $request->classe_id)
            ->exists();

        if ($exists) {
            return response()->json(['message' => 'Affectation déjà existante.'], 409);
        }

        $id = DB::table('enseignant_matiere_classe')->insertGetId([
            'enseignant_id' => $request->enseignant_id,
            'matiere_id'    => $request->matiere_id,
            'classe_id'     => $request->classe_id,
            'annee'         => $request->annee,
            'created_at'    => now(),
            'updated_at'    => now(),
        ]);

        return response()->json(['id' => $id, 'message' => 'Affectation créée.'], 201);
    }

    // Supprimer une affectation
    public function destroy($id)
    {
        DB::table('enseignant_matiere_classe')->where('id', $id)->delete();
        return response()->json(['message' => 'Affectation supprimée.']);
    }

    // Affecter étudiant → classe
    public function affecterEtudiant(Request $request)
    {
        $request->validate([
            'etudiant_id' => 'required|exists:users,id',
            'classe_id'   => 'required|exists:classes,id',
            'annee'       => 'required|string',
        ]);

        $exists = DB::table('etudiant_classe')
            ->where('etudiant_id', $request->etudiant_id)
            ->where('classe_id',   $request->classe_id)
            ->exists();

        if ($exists) {
            return response()->json(['message' => 'Étudiant déjà dans cette classe.'], 409);
        }

        DB::table('etudiant_classe')->insert([
            'etudiant_id' => $request->etudiant_id,
            'classe_id'   => $request->classe_id,
            'annee'       => $request->annee,
            'created_at'  => now(),
            'updated_at'  => now(),
        ]);

        return response()->json(['message' => 'Étudiant affecté à la classe.'], 201);
    }
}