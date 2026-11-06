<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable implements MustVerifyEmail
{
    public const ROLE_STUDENT = 'etudiant';

    public const ROLE_TEACHER = 'enseignant';

    public const ROLE_ADMINISTRATOR = 'administrateur';

    public const STATUS_ACTIVE = 'actif';

    public const STATUS_DISABLED = 'desactive';

    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    public function isActive(): bool
    {
        return $this->statut === self::STATUS_ACTIVE;
    }

    public function devoirs(): HasMany
    {
        return $this->hasMany(Devoir::class, 'enseignant_id');
    }

    /**
     * Les attributs convertis automatiquement.
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}
