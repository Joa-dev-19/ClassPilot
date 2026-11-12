<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Filiere;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class FiliereController extends Controller
{
    public function index(): View
    {
        $filieres = Filiere::query()
            ->with(['classes' => function (HasMany $query): void {
                $query->withCount(['etudiants', 'devoirs'])->orderBy('nom');
            }])
            ->orderBy('nom')
            ->get();

        return view('admin.filieres.index', ['filieres' => $filieres]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nom' => ['required', 'string', 'max:100', Rule::unique('filieres', 'nom')],
        ]);

        Filiere::query()->create($validated);

        return redirect()
            ->route('admin.filieres.index')
            ->with('success', 'La filière a été créée.');
    }

    public function update(Request $request, Filiere $filiere): RedirectResponse
    {
        $validated = $request->validate([
            'nom' => [
                'required',
                'string',
                'max:100',
                Rule::unique('filieres', 'nom')->ignore($filiere),
            ],
        ]);

        $filiere->update($validated);

        return redirect()
            ->route('admin.filieres.index')
            ->with('success', 'La filière a été modifiée.');
    }
}
