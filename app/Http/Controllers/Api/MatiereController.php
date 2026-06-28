<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Matiere;

class MatiereController extends Controller
{
    // Liste pour enseignant (filtrée) ou admin (toutes)
    public function index(Request $request)
    {
        $user = $request->user();

        if ($user->role === 'admin') {
            $matieres = Matiere::orderBy('nom')->get();
            return response()->json($matieres);
        }

        // Enseignant : seulement ses matières affectées
        $matieres = DB::table('enseignant_matiere_classe')
            ->join('matieres', 'enseignant_matiere_classe.matiere_id', '=', 'matieres.id')
            ->where('enseignant_matiere_classe.enseignant_id', $user->id)
            ->select('matieres.id', 'matieres.nom', 'matieres.code', 'matieres.coefficient_defaut')
            ->distinct()
            ->get();

        return response()->json($matieres);
    }

    public function store(Request $request)
    {
        $request->validate([
            'nom'               => 'required|string|max:255',
            'code'              => 'required|string|max:20|unique:matieres,code',
            'coefficient_defaut'=> 'sometimes|numeric|min:1',
        ]);

        $matiere = Matiere::create([
            'nom'                => $request->nom,
            'code'               => strtoupper($request->code),
            'coefficient_defaut' => $request->coefficient_defaut ?? 1,
        ]);

        return response()->json($matiere, 201);
    }

    public function show($id)
    {
        return response()->json(Matiere::findOrFail($id));
    }

    public function update(Request $request, $id)
    {
        $matiere = Matiere::findOrFail($id);

        $request->validate([
            'nom'               => 'sometimes|string|max:255',
            'code'              => 'sometimes|string|max:20|unique:matieres,code,' . $id,
            'coefficient_defaut'=> 'sometimes|numeric|min:1',
        ]);

        if ($request->has('nom'))                $matiere->nom = $request->nom;
        if ($request->has('code'))               $matiere->code = strtoupper($request->code);
        if ($request->has('coefficient_defaut')) $matiere->coefficient_defaut = $request->coefficient_defaut;

        $matiere->save();
        return response()->json($matiere);
    }

    public function destroy($id)
    {
        Matiere::findOrFail($id)->delete();
        return response()->json(['message' => 'Matière supprimée.']);
    }
}