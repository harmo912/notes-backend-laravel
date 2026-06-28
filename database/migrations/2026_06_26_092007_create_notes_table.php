<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
{
    Schema::create('notes', function (Blueprint $table) {
        $table->id();
        
        // Clés étrangères liées à la table users (étudiant et enseignant)
        $table->foreignId('etudiant_id')->constrained('users')->onDelete('cascade');
        $table->foreignId('enseignant_id')->constrained('users')->onDelete('cascade');
        
        // Clé étrangère liée aux matières
        $table->foreignId('matiere_id')->constrained('matieres')->onDelete('cascade');
        
        // Infos de la note
        $table->decimal('valeur', 4, 2); // Permet des notes comme 15.50 (max 20)
        $table->integer('coefficient');
        $table->enum('type', ['devoir', 'examen']);
        $table->date('date_evaluation');
        
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notes');
    }
};
