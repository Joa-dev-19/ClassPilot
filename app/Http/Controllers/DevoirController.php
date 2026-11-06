<?php

namespace App\Http\Controllers;

use App\Models\Devoir;
use Carbon\CarbonImmutable;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class DevoirController extends Controller
{
    public function index(Request $request): View
    {
        $devoirs = Devoir::query()
            ->whereBelongsTo($request->user(), 'enseignant')
            ->orderByDesc('updated_at')
            ->paginate(15);

        return view('devoirs.index', ['devoirs' => $devoirs]);
    }

    public function create(): View
    {
        return view('devoirs.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'titre' => ['required', 'string', 'max:255'],
            'consignes' => ['nullable', 'string'],
            'date_limite' => ['required', 'date', 'after_or_equal:today'],
        ]);

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

        return view('devoirs.show', ['devoir' => $devoir]);
    }

    public function edit(Request $request, Devoir $devoir): View
    {
        $devoir = $this->findOwnedDevoir($request, $devoir);
        abort_if($devoir->statut === Devoir::STATUS_ARCHIVED, 404);

        return view('devoirs.edit', ['devoir' => $devoir]);
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
        ]);

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
            ->with('success', 'Le devoir a été publié pour tous les étudiants.');
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

    public function studentIndex(): View
    {
        $devoirs = Devoir::query()
            ->where('statut', Devoir::STATUS_PUBLISHED)
            ->orderBy('date_limite')
            ->paginate(15);

        return view('devoirs.etudiants.index', ['devoirs' => $devoirs]);
    }

    public function studentShow(Devoir $devoir): View
    {
        $devoir = Devoir::query()
            ->where('statut', Devoir::STATUS_PUBLISHED)
            ->findOrFail($devoir->id);

        return view('devoirs.etudiants.show', ['devoir' => $devoir]);
    }

    private function findOwnedDevoir(Request $request, Devoir $devoir): Devoir
    {
        return Devoir::query()
            ->whereBelongsTo($request->user(), 'enseignant')
            ->whereKey($devoir->getKey())
            ->firstOrFail();
    }
}
