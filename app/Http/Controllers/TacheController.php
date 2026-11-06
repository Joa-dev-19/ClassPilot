<?php

namespace App\Http\Controllers;

use App\Models\Tache;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TacheController extends Controller
{
    public function index(): View
    {
        $taches = Tache::where('user_id', Auth::id())
            ->orderBy('date_limite')
            ->get();

        return view('taches.index', ['taches' => $taches]);
    }

    public function create(): View
    {
        return view('taches.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $donnees = $this->validatedTaskData($request);

        $donnees['user_id'] = Auth::id();

        Tache::create($donnees);

        return redirect()
            ->route('taches.index')
            ->with('success', 'La tâche a été créée avec succès.');
    }

    public function edit(int $tache): View
    {
        return view('taches.edit', ['tache' => $this->findOwnedTask($tache)]);
    }

    public function update(Request $request, int $tache): RedirectResponse
    {
        $tache = $this->findOwnedTask($tache);
        $tache->update($this->validatedTaskData($request));

        return redirect()
            ->route('taches.index')
            ->with('success', 'La tâche a été mise à jour avec succès.');
    }

    public function destroy(int $tache): RedirectResponse
    {
        $this->findOwnedTask($tache)->delete();

        return redirect()
            ->route('taches.index')
            ->with('success', 'La tâche a été supprimée avec succès.');
    }

    /**
     * @return array{titre: string, description?: string|null, date_limite?: string|null, priorite: string, statut: string}
     */
    private function validatedTaskData(Request $request): array
    {
        return $request->validate([
            'titre' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'date_limite' => ['nullable', 'date'],
            'priorite' => ['required', 'in:faible,moyenne,elevee'],
            'statut' => ['required', 'in:a_faire,en_cours,terminee'],
        ]);
    }

    private function findOwnedTask(int $taskId): Tache
    {
        return Tache::query()
            ->where('user_id', Auth::id())
            ->findOrFail($taskId);
    }
}
