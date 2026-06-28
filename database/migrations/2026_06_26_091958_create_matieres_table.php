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
    Schema::create('matieres', function (Blueprint $table) {
        $table->id();
        $table->string('nom'); // Ex: Développement Web
        $table->string('code')->unique(); // Ex: DEVWEB2
        $table->integer('coefficient_defaut')->default(1);
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('matieres');
    }
};
