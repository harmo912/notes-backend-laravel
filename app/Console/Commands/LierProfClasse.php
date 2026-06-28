<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Classe;
use App\Models\Matiere;

class LierProfClasse extends Command
{
    protected $signature = 'app:lier-prof';
    protected $description = 'Associe automatiquement le Dr. Koffi Mensah à sa classe et sa matière';

    public function handle()
    {
        // 1. Récupérer le professeur (ID 2 ou via son email)
        $enseignant = User::where('email', 'like', '%koffi%')->first() ?? User::find(2);
        
        // 2. Récupérer la classe de Licence 2 (ID 1)
        $classe = Classe::find(1);
        
        // 3. Récupérer la première matière de ta table (ID 1)
        $matiere = Matiere::find(1);

        if (!$enseignant || !$classe || !$matiere) {
            $this->error('Erreur : Assure-toi que le prof (ID 2), la classe (ID 1) et la matière (ID 1) existent bien.');
            return Command::FAILURE;
        }

        // 4. Vérifier si la liaison n'existe pas déjà pour éviter les doublons
        $existe = DB::table('enseignant_matiere_classe')
            ->where('enseignant_id', $enseignant->id)
            ->where('classe_id', $classe->id)
            ->where('matiere_id', $matiere->id)
            ->exists();

        if ($existe) {
            $this->info('La liaison existe déjà dans la base de données !');
            return Command::SUCCESS;
        }

        // 5. Insérer la liaison
        DB::table('enseignant_matiere_classe')->insert([
            'enseignant_id' => $enseignant->id,
            'matiere_id'    => $matiere->id,
            'classe_id'     => $classe->id,
            'annee'         => '2025-2026',
            'created_at'    => now(),
            'updated_at'    => now(),
        ]);

        $this->info('Succès ! Le Dr. Koffi Mensah a été lié à sa classe et sa matière automatiquement.');
        return Command::SUCCESS;
    }
}