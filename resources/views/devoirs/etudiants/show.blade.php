<x-app-layout>
    <div class="cp-page">
        <div class="cp-page-inner max-w-3xl">
            <article class="cp-card flex flex-col gap-5 p-5 sm:p-8">
                <h1 class="cp-heading text-3xl">{{ $devoir->titre }}</h1>
                <p class="mt-2 text-sm text-slate-600">
                    Publié par {{ $devoir->enseignant->name }}
                    · Date limite : {{ $devoir->date_limite->format('d/m/Y') }}
                </p>
                @if ($devoir->classe)
                    <p class="mt-1 text-sm text-slate-600">
                        Classe : {{ $devoir->classe->nom }} · {{ $devoir->classe->filiere->nom }}
                    </p>
                @elseif ($devoir->filiere)
                    <p class="mt-1 text-sm text-slate-600">
                        Filière : {{ $devoir->filiere->nom }}
                    </p>
                @endif

                <section class="flex flex-col gap-2 border-t border-slate-100 pt-5" aria-labelledby="consignes-devoir">
                    <h2 id="consignes-devoir" class="font-semibold text-slate-900">Consignes</h2>
                    <p class="mt-2 whitespace-pre-line text-slate-700">{{ $devoir->consignes }}</p>
                </section>

                <a href="{{ route('devoirs.etudiants.index') }}"
                   class="cp-button-secondary mt-3 w-full sm:w-fit">
                    Retour aux devoirs
                </a>
            </article>
        </div>
    </div>
</x-app-layout>
