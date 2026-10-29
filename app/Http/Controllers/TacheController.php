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
        $donnees = $request->validate([
            'titre' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'date_limite' => ['nullable', 'date'],
            'priorite' => ['required', 'in:faible,moyenne,elevee'],
            'statut' => ['required', 'in:a_faire,en_cours,terminee'],
        ]);

        $donnees['user_id'] = Auth::id();

        Tache::create($donnees);

        return redirect()
            ->route('taches.index')
            ->with('success', 'La tâche a été créée avec succès.');
    }
}
