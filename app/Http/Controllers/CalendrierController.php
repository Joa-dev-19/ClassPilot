<?php

namespace App\Http\Controllers;

use App\Models\Devoir;
use App\Models\Rappel;
use App\Models\Tache;
use Carbon\CarbonImmutable;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class CalendrierController extends Controller
{
    public function index(Request $request): View
    {
        $validated = $request->validate([
            'mois' => ['nullable', 'date_format:Y-m'],
        ]);

        $month = CarbonImmutable::parse($validated['mois'] ?? now()->format('Y-m'))->startOfMonth();
        $start = $month->startOfMonth();
        $end = $month->endOfMonth();

        $taches = Tache::query()
            ->whereBelongsTo($request->user())
            ->whereBetween('date_limite', [$start->toDateString(), $end->toDateString()])
            ->with('rappels')
            ->orderBy('date_limite')
            ->get();

        $tachesParDate = $taches->groupBy(
            fn (Tache $tache): string => CarbonImmutable::parse($tache->date_limite)->toDateString()
        );

        $devoirsDuMois = Devoir::query()
            ->where('statut', Devoir::STATUS_PUBLISHED)
            ->whereDate('date_limite', '>=', $start->toDateString())
            ->whereDate('date_limite', '<=', $end->toDateString())
            ->orderBy('date_limite')
            ->get();

        $devoirsParDate = $devoirsDuMois->groupBy(
            fn (Devoir $devoir): string => $devoir->date_limite->toDateString()
        );

        $premierJour = $start->startOfWeek(CarbonImmutable::MONDAY);
        $dernierJour = $end->endOfWeek(CarbonImmutable::SUNDAY);
        $joursCalendrier = [];

        for ($jour = $premierJour; $jour <= $dernierJour; $jour = $jour->addDay()) {
            $joursCalendrier[] = $jour;
        }

        $echeancesProches = Tache::query()
            ->whereBelongsTo($request->user())
            ->where('statut', '!=', 'terminee')
            ->whereBetween('date_limite', [today()->toDateString(), today()->addDays(30)->toDateString()])
            ->orderBy('date_limite')
            ->get();

        $tachesEnRetard = Tache::query()
            ->whereBelongsTo($request->user())
            ->where('statut', '!=', 'terminee')
            ->whereDate('date_limite', '<', today())
            ->orderBy('date_limite')
            ->get();

        return view('calendrier.index', [
            'month' => $month,
            'joursCalendrier' => $joursCalendrier,
            'tachesParDate' => $tachesParDate,
            'devoirsParDate' => $devoirsParDate,
            'devoirsDuMois' => $devoirsDuMois,
            'taches' => $taches,
            'echeancesProches' => $echeancesProches,
            'tachesEnRetard' => $tachesEnRetard,
        ]);
    }

    public function storeReminder(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'tache_id' => [
                'required',
                'integer',
                Rule::exists('taches', 'id')->where('user_id', Auth::id()),
            ],
            'date_heure_rappel' => ['required', 'date', 'after:now'],
        ]);

        $tache = Tache::query()
            ->whereBelongsTo($request->user())
            ->findOrFail($validated['tache_id']);

        if ($tache->date_limite === null) {
            throw ValidationException::withMessages([
                'tache_id' => 'Un rappel nécessite une tâche avec une date limite.',
            ]);
        }

        $dateHeureRappel = CarbonImmutable::parse($validated['date_heure_rappel']);
        $dateLimite = CarbonImmutable::parse($tache->date_limite);

        if ($dateHeureRappel->greaterThan($dateLimite->endOfDay())) {
            throw ValidationException::withMessages([
                'date_heure_rappel' => 'Le rappel doit être programmé avant la date limite de la tâche.',
            ]);
        }

        if ($tache->rappels()->where('date_heure_rappel', $dateHeureRappel)->exists()) {
            throw ValidationException::withMessages([
                'date_heure_rappel' => 'Un rappel est déjà programmé à cette date et à cette heure.',
            ]);
        }

        $tache->rappels()->create([
            'date_heure_rappel' => $dateHeureRappel,
            'statut' => Rappel::STATUS_PENDING,
        ]);

        return redirect()
            ->route('calendrier.index', ['mois' => $dateLimite->format('Y-m')])
            ->with('success', 'Le rappel a été programmé.');
    }

    public function destroyReminder(Request $request, int $rappel): RedirectResponse
    {
        $rappel = Rappel::query()
            ->where('statut', Rappel::STATUS_PENDING)
            ->whereHas('tache', fn (Builder $query): Builder => $query->whereBelongsTo($request->user()))
            ->findOrFail($rappel);

        $mois = $rappel->tache->date_limite === null
            ? null
            : CarbonImmutable::parse($rappel->tache->date_limite)->format('Y-m');
        $rappel->delete();

        return redirect()
            ->route('calendrier.index', $mois === null ? [] : ['mois' => $mois])
            ->with('success', 'Le rappel a été annulé.');
    }
}
