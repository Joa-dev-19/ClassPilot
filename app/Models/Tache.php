<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tache extends Model
{
    protected $fillable = [
        'user_id',
        'titre',
        'description',
        'date_limite',
        'priorite',
        'statut',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function rappels(): HasMany
    {
        return $this->hasMany(Rappel::class);
    }
}
