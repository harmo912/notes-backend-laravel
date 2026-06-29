<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Classe;
use App\Models\Matiere;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Vider les tables dans le bon ordre
        DB::statement('SET session_replication_role = replica;'); // désactive les FK pour PostgreSQL
        DB::table('etudiant_classe')->truncate();
        DB::table('enseignant_matiere_classe')->truncate();
        DB::table('notes')->truncate();
        DB::table('personal_access_tokens')->truncate();
        DB::table('users')->truncate();
        DB::table('classes')->truncate();
        DB::table('matieres')->truncate();
        DB::statement('SET session_replication_role = DEFAULT;');

        // 1. UTILISATEURS
        $admin = User::create([
            'name'     => 'Admin Système',
            'email'    => 'admin@test.com',
            'password' => Hash::make('password'),
            'role'     => 'admin',
        ]);

        $enseignant1 = User::create([
            'name'     => 'Dr. Koffi Mensah',
            'email'    => 'koffi@test.com',
            'password' => Hash::make('password'),
            'role'     => 'enseignant',
        ]);

        $enseignant2 = User::create([
            'name'     => 'Mme. Marie Silva',
            'email'    => 'marie@test.com',
            'password' => Hash::make('password'),
            'role'     => 'enseignant',
        ]);

        $etudiants = [];
        for ($i = 1; $i <= 5; $i++) {
            $etudiants[] = User::create([
                'name'     => "Étudiant Modèle $i",
                'email'    => "etudiant$i@test.com",
                'password' => Hash::make('password'),
                'role'     => 'etudiant',
            ]);
        }

        // 2. CLASSE ET MATIÈRES
        $classe = Classe::create([
            'nom'              => 'Licence 2 Informatique de Gestion',
            'annee_academique' => '2025-2026',
        ]);

        $matiere1 = Matiere::create([
            'nom'               => 'Développement Web Fullstack',
            'code'              => 'DEVWEB',
            'coefficient_defaut'=> 3,
        ]);

        $matiere2 = Matiere::create([
            'nom'               => 'Base de données',
            'code'              => 'BDD',
            'coefficient_defaut'=> 2,
        ]);

        $matiere3 = Matiere::create([
            'nom'               => 'Réseaux informatiques',
            'code'              => 'RESEAU',
            'coefficient_defaut'=> 2,
        ]);

        // 3. AFFECTATIONS enseignant → matière → classe
        foreach ([$matiere1, $matiere2, $matiere3] as $matiere) {
            DB::table('enseignant_matiere_classe')->insert([
                'enseignant_id' => $enseignant1->id,
                'matiere_id'    => $matiere->id,
                'classe_id'     => $classe->id,
                'annee'         => '2025-2026',
                'created_at'    => now(),
                'updated_at'    => now(),
            ]);
        }

        // 4. AFFECTATIONS étudiants → classe
        foreach ($etudiants as $etudiant) {
            DB::table('etudiant_classe')->insert([
                'etudiant_id' => $etudiant->id,
                'classe_id'   => $classe->id,
                'annee'       => '2025-2026',
                'created_at'  => now(),
                'updated_at'  => now(),
            ]);
        }
    }
}