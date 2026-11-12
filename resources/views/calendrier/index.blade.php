<x-app-layout>
    <div class="cp-page">
        <div class="cp-page-inner flex flex-col gap-7">
            @if (session('success'))
                <div class="rounded-2xl border border-emerald-200 bg-emerald-50 p-4 text-emerald-900" role="status">
                    {{ session('success') }}
                </div>
            @endif

            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h1 class="cp-heading text-3xl">Calendrier et rappels</h1>
                    <p class="mt-2 text-sm text-slate-600 sm:text-base">
                        Consulte tes échéances et programme des rappels par courriel.
                    </p>
                </div>

                <div class="flex flex-wrap gap-3">
                    <a href="{{ route('dashboard') }}"
                       class="cp-button-secondary w-full text-sm sm:w-auto">
                        Tableau de bord
                    </a>
                    <a href="{{ route('taches.index') }}"
                       class="cp-button-secondary w-full text-sm sm:w-auto">
                        Mes tâches
                    </a>
                </div>
            </div>

            @if ($errors->any())
                <div class="rounded-2xl border border-red-200 bg-red-50 p-4 text-red-800" role="alert">
                    <ul class="list-inside list-disc space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <section class="cp-card flex flex-col gap-4 p-4 sm:p-6"
                     aria-labelledby="calendrier-mois">
                <div class="flex flex-wrap items-center justify-between gap-2 sm:gap-3">
                    <a href="{{ route('calendrier.index', ['mois' => $month->subMonth()->format('Y-m')]) }}"
                       aria-label="Mois précédent"
                       class="cp-button-secondary px-2 py-2 text-xs sm:px-3 sm:text-sm">
                        <span aria-hidden="true">←</span><span class="hidden sm:inline">Mois précédent</span>
                    </a>
                    <h2 id="calendrier-mois" class="text-center text-base font-bold capitalize text-slate-900 sm:text-lg">
                        {{ $month->locale('fr')->translatedFormat('F Y') }}
                    </h2>
                    <a href="{{ route('calendrier.index', ['mois' => $month->addMonth()->format('Y-m')]) }}"
                       aria-label="Mois suivant"
                       class="cp-button-secondary px-2 py-2 text-xs sm:px-3 sm:text-sm">
                        <span class="hidden sm:inline">Mois suivant</span><span aria-hidden="true">→</span>
                    </a>
                </div>

                <div>
                    <table class="w-full table-fixed border-collapse text-left">
                        <caption class="sr-only">
                            Calendrier des tâches pour {{ $month->locale('fr')->translatedFormat('F Y') }}
                        </caption>
                        <thead>
                            <tr>
                                @foreach (['Lundi' => 'Lu', 'Mardi' => 'Ma', 'Mercredi' => 'Me', 'Jeudi' => 'Je', 'Vendredi' => 'Ve', 'Samedi' => 'Sa', 'Dimanche' => 'Di'] as $jourSemaine => $jourCourt)
                                    <th scope="col" class="border-b border-slate-200 px-1 py-2 text-center text-xs font-semibold text-slate-700 sm:px-2 sm:py-3 sm:text-sm">
                                        <span class="sm:hidden">{{ $jourCourt }}</span>
                                        <span class="hidden sm:inline">{{ $jourSemaine }}</span>
                                    </th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @foreach (array_chunk($joursCalendrier, 7) as $semaine)
                                <tr>
                                    @foreach ($semaine as $jour)
                                        @php($tachesDuJour = $tachesParDate->get($jour->toDateString(), collect()))
                                        @php($devoirsDuJour = $devoirsParDate->get($jour->toDateString(), collect()))
                                        <td class="h-20 overflow-hidden border-b border-r border-slate-100 p-1 align-top sm:h-28 sm:p-2 {{ $jour->month !== $month->month ? 'bg-slate-50 text-slate-400' : 'text-slate-800' }}">
                                            <time datetime="{{ $jour->toDateString() }}"
                                                  class="text-xs font-semibold sm:text-sm">
                                                {{ $jour->day }}
                                            </time>
                                            @foreach ($tachesDuJour->take(2) as $tache)
                                                <a href="#tache-{{ $tache->id }}"
                                                   title="Tâche : {{ $tache->titre }}"
                                                   class="mt-1 block truncate rounded-md px-1 py-0.5 text-[10px] {{ $tache->statut === 'terminee' ? 'bg-emerald-50 text-emerald-800' : ($jour->isBefore(today()) ? 'bg-red-50 text-red-800' : 'bg-blue-50 text-blue-800') }} sm:px-2 sm:py-1 sm:text-xs">
                                                    {{ $tache->titre }}
                                                </a>
                                            @endforeach
                                            @foreach ($devoirsDuJour->take(2) as $devoir)
                                                <a href="{{ route('devoirs.etudiants.show', $devoir) }}"
                                                   class="mt-1 block truncate rounded-md bg-sand-100 px-1 py-0.5 text-[10px] text-sand-900 focus-visible:ring-2 focus-visible:ring-sand-700 sm:px-2 sm:py-1 sm:text-xs"
                                                   title="Devoir : {{ $devoir->titre }}">
                                                    Devoir : {{ $devoir->titre }}
                                                </a>
                                            @endforeach
                                            @if ($devoirsDuJour->count() > 2)
                                                <p class="mt-1 text-xs text-slate-600">
                                                    {{ $devoirsDuJour->count() - 2 }} autre{{ $devoirsDuJour->count() > 3 ? 's' : '' }} devoir{{ $devoirsDuJour->count() > 3 ? 's' : '' }}
                                                </p>
                                            @endif
                                            @if ($tachesDuJour->count() > 2)
                                                <p class="mt-1 text-xs text-slate-600">
                                                    {{ $tachesDuJour->count() - 2 }} autre{{ $tachesDuJour->count() > 3 ? 's' : '' }}
                                                </p>
                                            @endif
                                        </td>
                                    @endforeach
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <p class="text-xs leading-5 text-slate-600 sm:text-sm">
                    Les tâches terminées sont indiquées comme terminées dans la liste. Les tâches échues et non terminées sont signalées comme en retard.
                </p>
            </section>

            <div class="grid grid-cols-1 gap-5 lg:grid-cols-2">
                <section class="cp-card flex flex-col gap-2 p-5 sm:p-6"
                         aria-labelledby="echeances-proches">
                    <h2 id="echeances-proches" class="text-lg font-bold text-slate-900">
                        Échéances à venir
                    </h2>
                    <p class="text-sm text-slate-500">Dans les 30 prochains jours</p>
                    @if ($echeancesProches->isEmpty())
                        <p class="text-sm text-slate-600">Aucune échéance à venir.</p>
                    @else
                        <ul class="flex flex-col gap-3">
                            @foreach ($echeancesProches as $tache)
                                <li class="flex flex-wrap items-center justify-between gap-2 rounded-xl border border-slate-100 p-3">
                                    <span class="font-medium text-slate-800">{{ $tache->titre }}</span>
                                    <span class="text-sm text-slate-600">
                                        {{ \Carbon\Carbon::parse($tache->date_limite)->format('d/m/Y') }} · {{ str_replace('_', ' ', $tache->statut) }}
                                    </span>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </section>

                <section class="cp-card flex flex-col gap-2 p-5 sm:p-6"
                         aria-labelledby="taches-en-retard">
                    <h2 id="taches-en-retard" class="text-lg font-bold text-slate-900">
                        Tâches en retard
                    </h2>
                    <p class="text-sm text-slate-500">Échéance passée, tâche non terminée</p>
                    @if ($tachesEnRetard->isEmpty())
                        <p class="text-sm text-slate-600">Aucune tâche en retard.</p>
                    @else
                        <ul class="flex flex-col gap-3">
                            @foreach ($tachesEnRetard as $tache)
                                <li class="rounded-xl border border-red-100 p-3">
                                    <p class="font-medium text-slate-800">{{ $tache->titre }}</p>
                                    <p class="mt-1 text-sm text-red-800">
                                        En retard depuis le {{ \Carbon\Carbon::parse($tache->date_limite)->format('d/m/Y') }}
                                    </p>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </section>
            </div>

            <section class="cp-card flex flex-col gap-3 p-5 sm:p-6"
                     aria-labelledby="devoirs-du-mois">
                <h2 id="devoirs-du-mois" class="text-lg font-bold text-slate-900">
                    Devoirs publiés ce mois-ci
                </h2>
                @if ($devoirsDuMois->isEmpty())
                    <p class="text-sm text-slate-600">Aucun devoir publié avec une échéance ce mois-ci.</p>
                @else
                    <ul class="flex flex-col gap-3">
                        @foreach ($devoirsDuMois as $devoir)
                            <li class="flex flex-wrap items-center justify-between gap-3 rounded-xl border border-slate-100 p-3">
                                <a href="{{ route('devoirs.etudiants.show', $devoir) }}"
                                   class="font-semibold text-emerald-800 underline decoration-transparent underline-offset-4 hover:decoration-current focus-visible:ring-2 focus-visible:ring-emerald-600">
                                    {{ $devoir->titre }}
                                </a>
                                <time datetime="{{ $devoir->date_limite->toDateString() }}"
                                      class="text-sm text-slate-600">
                                    {{ $devoir->date_limite->format('d/m/Y') }}
                                </time>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </section>

            <section class="cp-card flex flex-col gap-3 p-5 sm:p-6"
                     aria-labelledby="taches-du-mois">
                <h2 id="taches-du-mois" class="text-lg font-bold text-slate-900">
                    Tâches du mois
                </h2>

                @if ($taches->isEmpty())
                    <p class="text-sm text-slate-600">
                        Aucune tâche avec une date limite ce mois-ci.
                    </p>
                @else
                    <ul class="divide-y divide-slate-100">
                        @foreach ($taches as $tache)
                            <li id="tache-{{ $tache->id }}" class="py-5 first:pt-0 last:pb-0">
                                <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                                    <div>
                                        <h3 class="font-semibold text-slate-900">{{ $tache->titre }}</h3>
                                        <p class="mt-1 text-sm text-slate-600">
                                            Échéance : {{ \Carbon\Carbon::parse($tache->date_limite)->format('d/m/Y') }}
                                            · Statut :
                                            {{ match ($tache->statut) {
                                                'a_faire' => 'À faire',
                                                'en_cours' => 'En cours',
                                                'terminee' => 'Terminée',
                                                default => 'Non défini',
                                            } }}
                                        </p>
                                    </div>
                                    @if ($tache->statut !== 'terminee')
                                        <form method="POST" action="{{ route('calendrier.rappels.store') }}"
                                              class="flex w-full flex-col gap-3 sm:w-auto sm:flex-row sm:items-end">
                                            @csrf
                                            <input type="hidden" name="tache_id" value="{{ $tache->id }}">
                                            <div>
                                                <label for="rappel-{{ $tache->id }}"
                                                       class="block text-sm font-semibold text-slate-700">
                                                    Date et heure du rappel
                                                </label>
                                                <input id="rappel-{{ $tache->id }}"
                                                       name="date_heure_rappel"
                                                       type="datetime-local"
                                                       min="{{ now()->addMinute()->format('Y-m-d\TH:i') }}"
                                                       max="{{ \Carbon\Carbon::parse($tache->date_limite)->endOfDay()->format('Y-m-d\TH:i') }}"
                                                       required
                                                       class="mt-1 block w-full rounded-xl border-slate-300 bg-white text-sm shadow-sm focus:border-emerald-600 focus:ring-emerald-600 sm:w-auto">
                                            </div>
                                            <button type="submit"
                                                    class="cp-button-primary w-full text-sm sm:w-auto">
                                                Programmer
                                            </button>
                                        </form>
                                    @endif
                                </div>

                                @if ($tache->rappels->isNotEmpty())
                                    <ul class="mt-3 space-y-2">
                                        @foreach ($tache->rappels as $rappel)
                                            <li class="flex flex-wrap items-center gap-3 text-sm text-slate-600">
                                                <span>
                                                    Rappel du {{ $rappel->date_heure_rappel->format('d/m/Y à H:i') }}
                                                    · {{ match ($rappel->statut) {
                                                        \App\Models\Rappel::STATUS_SENT => 'Envoyé',
                                                        \App\Models\Rappel::STATUS_FAILED => 'Échec après '.$rappel->tentatives.' tentative'.($rappel->tentatives === 1 ? '' : 's'),
                                                        default => 'En attente',
                                                    } }}
                                                </span>
                                                @if ($rappel->statut === \App\Models\Rappel::STATUS_PENDING)
                                                    <form method="POST"
                                                          action="{{ route('calendrier.rappels.destroy', $rappel) }}">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit"
                                                                class="font-semibold text-red-700 underline hover:text-red-900 focus:outline-none focus-visible:ring-2 focus-visible:ring-red-600 focus-visible:ring-offset-2">
                                                            Annuler le rappel
                                                        </button>
                                                    </form>
                                                @endif
                                            </li>
                                        @endforeach
                                    </ul>
                                @endif
                            </li>
                        @endforeach
                    </ul>
                @endif
            </section>
        </div>
    </div>
</x-app-layout>
