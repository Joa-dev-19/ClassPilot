<x-app-layout>
    <div class="min-h-screen bg-slate-50 px-5 py-8 sm:px-8">
        <main class="mx-auto max-w-3xl">
            <article class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                <h1 class="text-2xl font-bold text-slate-900">{{ $devoir->titre }}</h1>
                <p class="mt-2 text-sm text-slate-600">
                    Publié par {{ $devoir->enseignant->name }}
                    · Date limite : {{ $devoir->date_limite->format('d/m/Y') }}
                </p>

                <section class="mt-6" aria-labelledby="consignes-devoir">
                    <h2 id="consignes-devoir" class="font-semibold text-slate-900">Consignes</h2>
                    <p class="mt-2 whitespace-pre-line text-slate-700">{{ $devoir->consignes }}</p>
                </section>

                <a href="{{ route('devoirs.etudiants.index') }}"
                   class="mt-8 inline-flex rounded-xl border border-slate-200 px-5 py-3 font-semibold text-slate-700 hover:bg-slate-50 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-600 focus-visible:ring-offset-2">
                    Retour aux devoirs
                </a>
            </article>
        </main>
    </div>
</x-app-layout>
