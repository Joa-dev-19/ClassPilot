<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class UtilisateurController extends Controller
{
    private const PER_PAGE = 15;

    /**
     * @var list<string>
     */
    private const SAFE_COLUMNS = [
        'id',
        'name',
        'email',
        'role',
        'statut',
        'email_verified_at',
        'created_at',
    ];

    public function index(Request $request): View
    {
        $validated = $request->validate([
            'q' => ['nullable', 'string', 'max:100'],
        ]);
        $search = trim($validated['q'] ?? '');

        $usersQuery = User::query()
            ->select(['id', 'name', 'email', 'role', 'statut', 'created_at'])
            ->orderBy('name')
            ->orderBy('id');

        if ($search !== '') {
            $usersQuery->where(function (Builder $query) use ($search): void {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        return view('admin.utilisateurs.index', [
            'users' => $usersQuery->paginate(self::PER_PAGE)->withQueryString(),
            'search' => $search,
        ]);
    }

    public function show(User $user): View
    {
        $user = User::query()
            ->select(self::SAFE_COLUMNS)
            ->findOrFail($user->getKey());

        return view('admin.utilisateurs.show', ['user' => $user]);
    }

    public function activate(User $user): RedirectResponse
    {
        $wasActivated = DB::transaction(function () use ($user): bool {
            $user = User::query()
                ->whereKey($user->getKey())
                ->lockForUpdate()
                ->firstOrFail();

            if ($user->isActive()) {
                return false;
            }

            $user->statut = User::STATUS_ACTIVE;
            $user->save();

            return true;
        });

        return redirect()
            ->route('admin.utilisateurs.show', $user)
            ->with(
                'success',
                $wasActivated ? 'Le compte a été activé.' : 'Le compte est déjà actif.',
            );
    }

    public function deactivate(User $user): RedirectResponse
    {
        $wasDeactivated = DB::transaction(function () use ($user): bool {
            $activeAdministrators = User::query()
                ->where('role', User::ROLE_ADMINISTRATOR)
                ->where('statut', User::STATUS_ACTIVE)
                ->orderBy('id')
                ->lockForUpdate()
                ->get(['id']);

            $user = User::query()
                ->whereKey($user->getKey())
                ->lockForUpdate()
                ->firstOrFail();

            if (! $user->isActive()) {
                return false;
            }

            if (
                $user->role === User::ROLE_ADMINISTRATOR
                && $activeAdministrators->count() <= 1
            ) {
                throw ValidationException::withMessages([
                    'statut' => 'Impossible de désactiver le dernier compte administrateur actif.',
                ]);
            }

            $user->statut = User::STATUS_DISABLED;
            $user->save();

            return true;
        });

        return redirect()
            ->route('admin.utilisateurs.show', $user)
            ->with(
                'success',
                $wasDeactivated ? 'Le compte a été désactivé.' : 'Le compte est déjà désactivé.',
            );
    }
}
