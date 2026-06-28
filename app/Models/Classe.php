<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Classe extends Model
{
    protected $fillable = ['nom', 'annee_academique'];

    // Cette relation permet de récupérer tous les étudiants d'une classe
    public function etudiants(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'etudiant_classe', 'classe_id', 'etudiant_id')
                    ->where('role', 'etudiant');
    }
}