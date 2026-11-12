<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Classe extends Model
{
    protected $fillable = [
        'filiere_id',
        'nom',
    ];

    public function filiere(): BelongsTo
    {
        return $this->belongsTo(Filiere::class);
    }

    public function etudiants(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function devoirs(): HasMany
    {
        return $this->hasMany(Devoir::class);
    }
}
