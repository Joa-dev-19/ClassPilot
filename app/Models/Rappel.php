<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Rappel extends Model
{
    public const STATUS_PENDING = 'en_attente';

    public const STATUS_SENT = 'envoye';

    protected $fillable = [
        'tache_id',
        'date_heure_rappel',
        'statut',
    ];

    public function tache(): BelongsTo
    {
        return $this->belongsTo(Tache::class);
    }

    protected function casts(): array
    {
        return [
            'date_heure_rappel' => 'datetime',
        ];
    }
}
