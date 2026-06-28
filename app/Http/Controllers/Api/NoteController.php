<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Note;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class NoteController extends Controller
{
    /**
     * Enregistrer les notes en masse (enseignant)
     */
    public function store(Request $request)
    {
        $request->validate([
            'matiere_id'           => 'required|exists:matieres,id',
            'classe_id'            => 'required|exists:classes,id',
            'type'                 => 'required|in:devoir,examen',
            'coefficient'          => 'required|numeric|min:1',
            'date_evaluation'      => 'required|date',
            'notes'                => 'required|array',
            'notes.*.etudiant_id'  => 'required|exists:users,id',
            'notes.*.valeur'       => 'required|numeric|min:0|max:20',
        ]);

        $enseignantId = Auth::id();

        foreach ($request->notes as $noteData) {
            Note::create([
                'etudiant_id'     => $noteData['etudiant_id'],
                'matiere_id'      => $request->matiere_id,
                'enseignant_id'   => $enseignantId,
                'valeur'          => $noteData['valeur'],
                'coefficient'     => $request->coefficient,
                'type'            => $request->type,
                'date_evaluation' => $request->date_evaluation,
            ]);
        }

        return response()->json([
            'message' => 'Toutes les notes ont été enregistrées avec succès !'
        ], 201);
    }

    /**
     * Consultation des notes filtrée par rôle
     * - admin    : toutes les notes
     * - enseignant : notes de ses matières/classes uniquement
     * - etudiant : ses propres notes uniquement
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        $query = Note::with(['etudiant:id,name,email', 'matiere:id,nom,code', 'enseignant:id,name'])
            ->orderBy('date_evaluation', 'desc');

        if ($user->role === 'etudiant') {
            $query->where('etudiant_id', $user->id);

        } elseif ($user->role === 'enseignant') {
            $query->where('enseignant_id', $user->id);

        }
        // admin : pas de filtre → toutes les notes

        $notes = $query->get();

        return response()->json($notes);
    }

    /**
     * Calcul des moyennes par matière pour un étudiant
     * Route : GET /api/notes/moyennes
     * - étudiant : ses propres moyennes
     * - enseignant : moyennes de ses classes
     * - admin : moyennes globales (filtre par etudiant_id optionnel)
     */
    public function moyennes(Request $request)
    {
        $user = Auth::user();

        if ($user->role === 'etudiant') {
            $etudiantId = $user->id;
        } else {
            // admin ou enseignant peuvent passer ?etudiant_id=X
            $etudiantId = $request->query('etudiant_id');
        }

        $query = DB::table('notes')
            ->join('matieres', 'notes.matiere_id', '=', 'matieres.id')
            ->select(
                'matieres.id as matiere_id',
                'matieres.nom as matiere_nom',
                'matieres.code as matiere_code',
                DB::raw('ROUND(SUM(notes.valeur * notes.coefficient) / SUM(notes.coefficient), 2) as moyenne'),
                DB::raw('COUNT(notes.id) as nb_notes')
            )
            ->groupBy('matieres.id', 'matieres.nom', 'matieres.code');

        if ($etudiantId) {
            $query->where('notes.etudiant_id', $etudiantId);
        }

        if ($user->role === 'enseignant') {
            $query->where('notes.enseignant_id', $user->id);
        }

        $moyennes = $query->get();

        // Moyenne générale pondérée
        $moyenneGenerale = null;
        if ($moyennes->isNotEmpty()) {
            $totalPoids  = 0;
            $totalValeur = 0;
            foreach ($moyennes as $m) {
                $totalValeur += $m->moyenne * $m->nb_notes;
                $totalPoids  += $m->nb_notes;
            }
            $moyenneGenerale = $totalPoids > 0
                ? round($totalValeur / $totalPoids, 2)
                : null;
        }

        return response()->json([
            'moyennes_par_matiere' => $moyennes,
            'moyenne_generale'     => $moyenneGenerale,
        ]);
    }

    /**
     * Détail d'une note (admin / enseignant concerné / étudiant concerné)
     */
    public function show($id)
    {
        $user = Auth::user();
        $note = Note::with(['etudiant:id,name,email', 'matiere:id,nom,code'])->findOrFail($id);

        if ($user->role === 'etudiant' && $note->etudiant_id !== $user->id) {
            return response()->json(['message' => 'Accès refusé.'], 403);
        }

        if ($user->role === 'enseignant' && $note->enseignant_id !== $user->id) {
            return response()->json(['message' => 'Accès refusé.'], 403);
        }

        return response()->json($note);
    }
}