<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Classe;
use App\Models\Filiere;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ClasseController extends Controller
{
    public function store(Request $request, Filiere $filiere): RedirectResponse
    {
        $validated = $request->validate([
            'nom' => [
                'required',
                'string',
                'max:100',
                Rule::unique('classes', 'nom')->where('filiere_id', $filiere->id),
            ],
        ]);

        $filiere->classes()->create($validated);

        return redirect()
            ->route('admin.filieres.index')
            ->with('success', 'La classe a été créée.');
    }

    public function update(
        Request $request,
        Filiere $filiere,
        Classe $classe,
    ): RedirectResponse {
        abort_unless($classe->filiere_id === $filiere->id, 404);

        $validated = $request->validate([
            'nom' => [
                'required',
                'string',
                'max:100',
                Rule::unique('classes', 'nom')
                    ->where('filiere_id', $filiere->id)
                    ->ignore($classe),
            ],
        ]);

        $classe->update($validated);

        return redirect()
            ->route('admin.filieres.index')
            ->with('success', 'La classe a été modifiée.');
    }
}
