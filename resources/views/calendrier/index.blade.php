<x-app-layout>
    <div class="min-h-screen bg-slate-50 px-5 py-8 sm:px-8">
        <div class="mx-auto max-w-7xl">
            @if (session('success'))
                <div class="mb-6 rounded-xl bg-green-100 p-4 text-green-800" role="status">
                    {{ session('success') }}
                </div>
            @endif

            <div class="mb-8 flex flex-wrap items-center justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-bold text-slate-900">Calendrier et rappels</h1>
                    <p class="mt-2 text-slate-500">
                        Consulte tes échéances et programme des rappels par courriel.
                    </p>
                </div>

                <div class="flex flex-wrap gap-3">
                    <a href="{{ route('dashboard') }}"
                       class="rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-100">
                        Tableau de bord
                    </a>
                    <a href="{{ route('taches.index') }}"
                       class="rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-100">
                        Mes tâches
                    </a>
                </div>
            </div>

            @if ($errors->any())
                <div class="mb-6 rounded-xl bg-red-50 p-4 text-red-800" role="alert">
                    <ul class="list-inside list-disc space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <section class="mb-8 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6"
                     aria-labelledby="calendrier-mois">
                <div class="mb-5 flex flex-wrap items-center justify-between gap-3">
                    <a href="{{ route('calendrier.index', ['mois' => $month->subMonth()->format('Y-m')]) }}"
                       class="rounded-lg border border-slate-200 px-3 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                        Mois précédent
                    </a>
                    <h2 id="calendrier-mois" class="text-lg font-bold capitalize text-slate-900">
                        {{ $month->locale('fr')->translatedFormat('F Y') }}
                    </h2>
                    <a href="{{ route('calendrier.index', ['mois' => $month->addMonth()->format('Y-m')]) }}"
                       class="rounded-lg border border-slate-200 px-3 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50">
                        Mois suivant
                    </a>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full min-w-[42rem] table-fixed border-collapse text-left">
                        <caption class="sr-only">
                            Calendrier des tâches pour {{ $month->locale('fr')->translatedFormat('F Y') }}
                        </caption>
                        <thead>
                            <tr>
                                @foreach (['Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi', 'Dimanche'] as $jourSemaine)
                                    <th scope="col" class="border-b border-slate-200 px-2 py-3 text-sm font-semibold text-slate-700">
                                        {{ $jourSemaine }}
                                    </th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @foreach (array_chunk($joursCalendrier, 7) as $semaine)
                                <tr>
                                    @foreach ($semaine as $jour)
                                        @php($tachesDuJour = $tachesParDate->get($jour->toDateString(), collect()))
                                        <td class="h-28 border-b border-r border-slate-100 p-2 align-top {{ $jour->month !== $month->month ? 'bg-slate-50 text-slate-400' : 'text-slate-800' }}">
                                            <time datetime="{{ $jour->toDateString() }}"
                                                  class="text-sm font-semibold">
                                                {{ $jour->day }}
                                            </time>
                                            @foreach ($tachesDuJour->take(2) as $tache)
                                                <a href="#tache-{{ $tache->id }}"
                                                   class="mt-1 block truncate rounded-md px-2 py-1 text-xs {{ $tache->statut === 'terminee' ? 'bg-green-50 text-green-800' : ($jour->isBefore(today()) ? 'bg-red-50 text-red-800' : 'bg-blue-50 text-blue-800') }}">
                                                    {{ $tache->titre }}
                                                </a>
                                            @endforeach
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
                <p class="mt-4 text-sm text-slate-600">
                    Les tâches terminées sont indiquées comme terminées dans la liste. Les tâches échues et non terminées sont signalées comme en retard.
                </p>
            </section>

            <div class="mb-8 grid grid-cols-1 gap-6 lg:grid-cols-2">
                <section class="rounded-2xl border border-amber-200 bg-white p-5 shadow-sm sm:p-6"
                         aria-labelledby="echeances-proches">
                    <h2 id="echeances-proches" class="text-lg font-bold text-slate-900">
                        Échéances à venir
                    </h2>
                    <p class="mt-1 text-sm text-slate-500">Dans les 30 prochains jours</p>
                    @if ($echeancesProches->isEmpty())
                        <p class="mt-4 text-sm text-slate-600">Aucune échéance à venir.</p>
                    @else
                        <ul class="mt-4 space-y-3">
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

                <section class="rounded-2xl border border-red-200 bg-white p-5 shadow-sm sm:p-6"
                         aria-labelledby="taches-en-retard">
                    <h2 id="taches-en-retard" class="text-lg font-bold text-slate-900">
                        Tâches en retard
                    </h2>
                    <p class="mt-1 text-sm text-slate-500">Échéance passée, tâche non terminée</p>
                    @if ($tachesEnRetard->isEmpty())
                        <p class="mt-4 text-sm text-slate-600">Aucune tâche en retard.</p>
                    @else
                        <ul class="mt-4 space-y-3">
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

            <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6"
                     aria-labelledby="taches-du-mois">
                <h2 id="taches-du-mois" class="text-lg font-bold text-slate-900">
                    Tâches du mois
                </h2>

                @if ($taches->isEmpty())
                    <p class="mt-4 text-sm text-slate-600">
                        Aucune tâche avec une date limite ce mois-ci.
                    </p>
                @else
                    <ul class="mt-4 divide-y divide-slate-100">
                        @foreach ($taches as $tache)
                            <li id="tache-{{ $tache->id }}" class="py-5 first:pt-0 last:pb-0">
                                <div class="flex flex-wrap items-start justify-between gap-4">
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
                                              class="flex flex-wrap items-end gap-3">
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
                                                       min="{{ now()->format('Y-m-d\TH:i') }}"
                                                       max="{{ \Carbon\Carbon::parse($tache->date_limite)->endOfDay()->format('Y-m-d\TH:i') }}"
                                                       required
                                                       class="mt-1 block rounded-lg border-slate-300 text-sm shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                            </div>
                                            <button type="submit"
                                                    class="rounded-lg bg-blue-700 px-4 py-2.5 text-sm font-semibold text-white hover:bg-blue-800">
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
                                                    · {{ $rappel->statut === \App\Models\Rappel::STATUS_SENT ? 'Envoyé' : 'En attente' }}
                                                </span>
                                                @if ($rappel->statut === \App\Models\Rappel::STATUS_PENDING)
                                                    <form method="POST"
                                                          action="{{ route('calendrier.rappels.destroy', $rappel) }}">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit"
                                                                class="font-semibold text-red-700 underline hover:text-red-900">
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
