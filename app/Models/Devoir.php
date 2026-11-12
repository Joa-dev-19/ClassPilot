<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
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
        'filiere_id',
        'classe_id',
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

    public function classe(): BelongsTo
    {
        return $this->belongsTo(Classe::class);
    }

    public function filiere(): BelongsTo
    {
        return $this->belongsTo(Filiere::class);
    }

    public function scopeVisibleToStudent(Builder $query, User $student): Builder
    {
        return $query->where(function (Builder $query) use ($student): void {
            $query->whereNull('classe_id')
                ->whereNull('filiere_id');

            if ($student->classe_id !== null) {
                $query->orWhere('classe_id', $student->classe_id)
                    ->orWhereIn(
                        'filiere_id',
                        Classe::query()
                            ->select('filiere_id')
                            ->whereKey($student->classe_id),
                    );
            }
        });
    }
}
