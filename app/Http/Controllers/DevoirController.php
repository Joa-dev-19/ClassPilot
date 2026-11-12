<?php

namespace App\Http\Controllers;

use App\Models\Classe;
use App\Models\Devoir;
use App\Models\Filiere;
use Carbon\CarbonImmutable;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class DevoirController extends Controller
{
    public function index(Request $request): View
    {
        $devoirs = Devoir::query()
            ->whereBelongsTo($request->user(), 'enseignant')
            ->with(['classe.filiere', 'filiere'])
            ->orderByDesc('updated_at')
            ->paginate(15);

        return view('devoirs.index', ['devoirs' => $devoirs]);
    }

    public function create(): View
    {
        return view('devoirs.create', [
            'classes' => $this->availableClasses(),
            'filieres' => $this->availableFilieres(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'titre' => ['required', 'string', 'max:255'],
            'consignes' => ['nullable', 'string'],
            'date_limite' => ['required', 'date', 'after_or_equal:today'],
            'classe_id' => ['nullable', 'integer', 'exists:classes,id'],
            'filiere_id' => ['nullable', 'integer', 'exists:filieres,id'],
        ]);
        $this->ensureSingleAudience($validated);

        $devoir = $request->user()->devoirs()->create([
            ...$validated,
            'statut' => Devoir::STATUS_DRAFT,
        ]);

        return redirect()
            ->route('devoirs.show', $devoir)
            ->with('success', 'Le devoir a été enregistré en brouillon.');
    }

    public function show(Request $request, Devoir $devoir): View
    {
        $devoir = $this->findOwnedDevoir($request, $devoir);
        $devoir->load(['classe.filiere', 'filiere']);

        return view('devoirs.show', ['devoir' => $devoir]);
    }

    public function edit(Request $request, Devoir $devoir): View
    {
        $devoir = $this->findOwnedDevoir($request, $devoir);
        abort_if($devoir->statut === Devoir::STATUS_ARCHIVED, 404);
        $devoir->load('classe');

        return view('devoirs.edit', [
            'devoir' => $devoir,
            'classes' => $this->availableClasses(),
            'filieres' => $this->availableFilieres(),
        ]);
    }

    public function update(Request $request, Devoir $devoir): RedirectResponse
    {
        $devoir = $this->findOwnedDevoir($request, $devoir);
        abort_if($devoir->statut === Devoir::STATUS_ARCHIVED, 404);

        $validated = $request->validate([
            'titre' => ['required', 'string', 'max:255'],
            'consignes' => [
                $devoir->statut === Devoir::STATUS_PUBLISHED ? 'required' : 'nullable',
                'string',
            ],
            'date_limite' => ['required', 'date'],
            'classe_id' => ['nullable', 'integer', 'exists:classes,id'],
            'filiere_id' => ['nullable', 'integer', 'exists:filieres,id'],
        ]);
        $this->ensureSingleAudience($validated);

        $dateLimite = CarbonImmutable::parse($validated['date_limite'])->toDateString();
        $ancienneDateLimite = $devoir->date_limite->toDateString();

        if ($dateLimite < today()->toDateString() && $dateLimite !== $ancienneDateLimite) {
            throw ValidationException::withMessages([
                'date_limite' => 'La date limite doit être aujourd’hui ou ultérieure.',
            ]);
        }

        $devoir->update($validated);

        return redirect()
            ->route('devoirs.show', $devoir)
            ->with('success', 'Le devoir a été modifié.');
    }

    public function publish(Request $request, Devoir $devoir): RedirectResponse
    {
        $devoir = $this->findOwnedDevoir($request, $devoir);
        abort_unless($devoir->statut === Devoir::STATUS_DRAFT, 404);

        if (blank($devoir->consignes)) {
            throw ValidationException::withMessages([
                'consignes' => 'Les consignes sont obligatoires pour publier le devoir.',
            ]);
        }

        if ($devoir->date_limite->isBefore(today())) {
            throw ValidationException::withMessages([
                'date_limite' => 'La date limite doit être aujourd’hui ou ultérieure.',
            ]);
        }

        $devoir->statut = Devoir::STATUS_PUBLISHED;
        $devoir->save();

        return redirect()
            ->route('devoirs.show', $devoir)
            ->with(
                'success',
                $devoir->classe_id !== null
                    ? 'Le devoir a été publié pour la classe sélectionnée.'
                    : ($devoir->filiere_id !== null
                        ? 'Le devoir a été publié pour la filière sélectionnée.'
                        : 'Le devoir a été publié pour tous les étudiants.'),
            );
    }

    public function archive(Request $request, Devoir $devoir): RedirectResponse
    {
        $devoir = $this->findOwnedDevoir($request, $devoir);
        abort_if($devoir->statut === Devoir::STATUS_ARCHIVED, 404);

        $devoir->statut = Devoir::STATUS_ARCHIVED;
        $devoir->save();

        return redirect()
            ->route('devoirs.index')
            ->with('success', 'Le devoir a été archivé.');
    }

    public function studentIndex(Request $request): View
    {
        $devoirs = Devoir::query()
            ->where('statut', Devoir::STATUS_PUBLISHED)
            ->visibleToStudent($request->user())
            ->with(['classe.filiere', 'filiere'])
            ->orderBy('date_limite')
            ->paginate(15);

        return view('devoirs.etudiants.index', ['devoirs' => $devoirs]);
    }

    public function studentShow(Request $request, Devoir $devoir): View
    {
        $devoir = Devoir::query()
            ->where('statut', Devoir::STATUS_PUBLISHED)
            ->visibleToStudent($request->user())
            ->with(['enseignant', 'classe.filiere', 'filiere'])
            ->findOrFail($devoir->id);

        return view('devoirs.etudiants.show', ['devoir' => $devoir]);
    }

    private function findOwnedDevoir(Request $request, Devoir $devoir): Devoir
    {
        return Devoir::query()
            ->whereBelongsTo($request->user(), 'enseignant')
            ->with(['classe.filiere', 'filiere'])
            ->whereKey($devoir->getKey())
            ->firstOrFail();
    }

    /**
     * @return Collection<int, Classe>
     */
    private function availableClasses(): Collection
    {
        return Classe::query()
            ->with('filiere')
            ->orderBy('filiere_id')
            ->orderBy('nom')
            ->get();
    }

    /**
     * @return Collection<int, Filiere>
     */
    private function availableFilieres(): Collection
    {
        return Filiere::query()
            ->orderBy('nom')
            ->get();
    }

    /**
     * @param  array<string, mixed>  $validated
     */
    private function ensureSingleAudience(array $validated): void
    {
        if (
            filled($validated['classe_id'] ?? null)
            && filled($validated['filiere_id'] ?? null)
        ) {
            throw ValidationException::withMessages([
                'classe_id' => 'Choisis soit une filière, soit une classe pour le devoir.',
            ]);
        }
    }
}
