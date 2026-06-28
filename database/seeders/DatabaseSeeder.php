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
        // 1. CRÉATION DES UTILISATEURS (Calqué sur tes vrais IDs de BDD)
        $admin = User::create([
            'id' => 1,
            'name' => 'Admin Système',
            'email' => 'admin@test.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        $enseignant = User::create([
            'id' => 2,
            'name' => 'Dr. Koffi Mensah',
            'email' => 'koffi@test.com',
            'password' => Hash::make('password'),
            'role' => 'enseignant',
        ]);

        User::create([
    'name' => 'Dr. Koffi Mensah',
    'email' => 'koffi@test.com',
    'password' => Hash::make('password'),
    'role' => 'enseignant',
    'created_at' => now(), // Insère la seconde exacte actuelle
    'updated_at' => now(),
]);
        // Création des 5 étudiants
        $etudiantIds = [];
        for ($i = 1; $i <= 5; $i++) {
            $student = User::create([
                'id' => $i + 3, // IDs de 4 à 8
                'name' => "Étudiant Modèle $i",
                'email' => "etudiant$i@test.com",
                'password' => Hash::make('password'),
                'role' => 'etudiant',
            ]);
            $etudiantIds[] = $student->id;
        }

        // 2. CRÉATION DE LA CLASSE ET DE LA MATIÈRE
        $classe = Classe::create([
            'id' => 1,
            'nom' => 'Licence 2 Informatique de Gestion',
            'annee_academique' => '2025-2026',
        ]);

        $matiere = Matiere::create([
            'id' => 1,
            'nom' => 'Développement Web Fullstack',
            'code' => 'DEVWEB',
            'coefficient_defaut' => 3,
        ]);

        // 3. REMPLISSAGE DES TABLES PIVOTS (Ce qui corrige tes bugs !)
        
        // Liaison Enseignant <-> Matière <-> Classe
        DB::table('enseignant_matiere_classe')->insert([
            'enseignant_id' => $enseignant->id,
            'matiere_id' => $matiere->id,
            'classe_id' => $classe->id,
            'annee' => '2025-2026',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Liaison Étudiants <-> Classe (La table qui était vide)
        foreach ($etudiantIds as $id) {
            DB::table('etudiant_classe')->insert([
                'etudiant_id' => $id,
                'classe_id' => $classe->id,
                'annee' => '2025-2026',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}