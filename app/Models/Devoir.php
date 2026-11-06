<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Devoir extends Model
{
    public const STATUS_DRAFT = 'brouillon';

    public const STATUS_PUBLISHED = 'publie';

    public const STATUS_ARCHIVED = 'archive';

    protected $fillable = [
        'enseignant_id',
        'titre',
        'consignes',
        'date_limite',
        'statut',
    ];

    protected function casts(): array
    {
        return [
            'date_limite' => 'date',
        ];
    }

    public function enseignant(): BelongsTo
    {
        return $this->belongsTo(User::class, 'enseignant_id');
    }
}
