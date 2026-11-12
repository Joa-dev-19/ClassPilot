<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Filiere extends Model
{
    protected $fillable = [
        'nom',
    ];

    public function classes(): HasMany
    {
        return $this->hasMany(Classe::class);
    }

    public function devoirs(): HasMany
    {
        return $this->hasMany(Devoir::class);
    }
}
