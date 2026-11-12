<x-app-layout>
    <div class="cp-page">
        <div class="cp-page-inner flex max-w-3xl flex-col gap-5">
            @if (session('success'))
                <div class="rounded-2xl border border-emerald-200 bg-emerald-50 p-4 text-emerald-900" role="status">
                    {{ session('success') }}
                </div>
            @endif
            @if ($errors->any())
                <div class="rounded-2xl border border-red-200 bg-red-50 p-4 text-red-800" role="alert">
                    <ul class="list-inside list-disc space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <article class="cp-card flex flex-col gap-6 p-5 sm:p-8">
                <header class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                    <div>
                        <h1 class="cp-heading text-3xl">{{ $devoir->titre }}</h1>
                        <p class="mt-2 text-sm text-slate-600">
                            Date limite : {{ $devoir->date_limite->format('d/m/Y') }}
                        </p>
                        <p class="mt-1 text-sm text-slate-600">
                            Destinataires :
                            {{ $devoir->classe
                                ? $devoir->classe->nom.' · '.$devoir->classe->filiere->nom
                                : ($devoir->filiere?->nom ?? 'Tous les étudiants') }}
                        </p>
                    </div>
                    <span class="w-fit rounded-full bg-slate-100 px-3 py-1 text-sm font-semibold text-slate-700">
                        {{ match ($devoir->statut) {
                            \App\Models\Devoir::STATUS_DRAFT => 'Brouillon',
                            \App\Models\Devoir::STATUS_PUBLISHED => 'Publié',
                            default => 'Archivé',
                        } }}
                    </span>
                </header>

                <section class="flex flex-col gap-2 border-t border-slate-100 pt-5" aria-labelledby="consignes-devoir">
                    <h2 id="consignes-devoir" class="font-semibold text-slate-900">Consignes</h2>
                    <p class="mt-2 whitespace-pre-line text-slate-700">{{ $devoir->consignes ?: 'Aucune consigne renseignée.' }}</p>
                </section>

                <div class="flex flex-col gap-3 border-t border-slate-100 pt-5 sm:flex-row sm:flex-wrap">
                    <a href="{{ route('devoirs.index') }}"
                       class="cp-button-secondary w-full sm:w-auto">
                        Retour à mes devoirs
                    </a>
                    @if ($devoir->statut !== \App\Models\Devoir::STATUS_ARCHIVED)
                        <a href="{{ route('devoirs.edit', $devoir) }}"
                           class="cp-button-primary w-full sm:w-auto">
                            Modifier
                        </a>
                    @endif
                    @if ($devoir->statut === \App\Models\Devoir::STATUS_DRAFT)
                        <form action="{{ route('devoirs.publish', $devoir) }}" method="POST">
                            @csrf
                            <button type="submit"
                                    class="cp-button-primary w-full sm:w-auto">
                                Publier
                            </button>
                        </form>
                    @endif
                    @if ($devoir->statut !== \App\Models\Devoir::STATUS_ARCHIVED)
                        <form action="{{ route('devoirs.archive', $devoir) }}" method="POST"
                              onsubmit="return confirm('Archiver ce devoir ?')">
                            @csrf
                            <button type="submit"
                                    class="w-full rounded-xl border border-red-200 bg-white px-5 py-3 font-semibold text-red-700 transition hover:bg-red-50 focus-visible:ring-2 focus-visible:ring-red-600 sm:w-auto">
                                Archiver
                            </button>
                        </form>
                    @endif
                </div>
            </article>
        </div>
    </div>
</x-app-layout>
