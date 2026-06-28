<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Note extends Model
{
    protected $fillable = [
        'etudiant_id',
        'matiere_id',
        'enseignant_id',
        'valeur',
        'coefficient',
        'type',
        'date_evaluation',
    ];

    protected $casts = [
        'date_evaluation' => 'date',
        'valeur'          => 'float',
        'coefficient'     => 'float',
    ];

    public function etudiant()
    {
        return $this->belongsTo(User::class, 'etudiant_id');
    }

    public function enseignant()
    {
        return $this->belongsTo(User::class, 'enseignant_id');
    }

    public function matiere()
    {
        return $this->belongsTo(Matiere::class);
    }
}