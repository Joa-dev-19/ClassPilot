<x-app-layout>
    <div class="min-h-screen bg-slate-50 px-5 py-8 sm:px-8">
        <main class="mx-auto max-w-3xl">
            @if (session('success'))
                <div class="mb-6 rounded-xl bg-green-100 p-4 text-green-800" role="status">
                    {{ session('success') }}
                </div>
            @endif
            @if ($errors->any())
                <div class="mb-6 rounded-xl bg-red-50 p-4 text-red-800" role="alert">
                    <ul class="list-inside list-disc space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <article class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                <header class="flex flex-wrap items-start justify-between gap-4">
                    <div>
                        <h1 class="text-2xl font-bold text-slate-900">{{ $devoir->titre }}</h1>
                        <p class="mt-2 text-sm text-slate-600">
                            Date limite : {{ $devoir->date_limite->format('d/m/Y') }}
                        </p>
                    </div>
                    <span class="rounded-full bg-slate-100 px-3 py-1 text-sm font-semibold text-slate-700">
                        {{ match ($devoir->statut) {
                            \App\Models\Devoir::STATUS_DRAFT => 'Brouillon',
                            \App\Models\Devoir::STATUS_PUBLISHED => 'Publié',
                            default => 'Archivé',
                        } }}
                    </span>
                </header>

                <section class="mt-6" aria-labelledby="consignes-devoir">
                    <h2 id="consignes-devoir" class="font-semibold text-slate-900">Consignes</h2>
                    <p class="mt-2 whitespace-pre-line text-slate-700">{{ $devoir->consignes ?: 'Aucune consigne renseignée.' }}</p>
                </section>

                <div class="mt-8 flex flex-wrap gap-3">
                    <a href="{{ route('devoirs.index') }}"
                       class="rounded-xl border border-slate-200 px-5 py-3 font-semibold text-slate-700 hover:bg-slate-50 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-600 focus-visible:ring-offset-2">
                        Retour à mes devoirs
                    </a>
                    @if ($devoir->statut !== \App\Models\Devoir::STATUS_ARCHIVED)
                        <a href="{{ route('devoirs.edit', $devoir) }}"
                           class="rounded-xl bg-blue-700 px-5 py-3 font-semibold text-white hover:bg-blue-800 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-600 focus-visible:ring-offset-2">
                            Modifier
                        </a>
                    @endif
                    @if ($devoir->statut === \App\Models\Devoir::STATUS_DRAFT)
                        <form action="{{ route('devoirs.publish', $devoir) }}" method="POST">
                            @csrf
                            <button type="submit"
                                    class="rounded-xl bg-green-700 px-5 py-3 font-semibold text-white hover:bg-green-800 focus:outline-none focus-visible:ring-2 focus-visible:ring-green-700 focus-visible:ring-offset-2">
                                Publier
                            </button>
                        </form>
                    @endif
                    @if ($devoir->statut !== \App\Models\Devoir::STATUS_ARCHIVED)
                        <form action="{{ route('devoirs.archive', $devoir) }}" method="POST"
                              onsubmit="return confirm('Archiver ce devoir ?')">
                            @csrf
                            <button type="submit"
                                    class="rounded-xl border border-red-200 px-5 py-3 font-semibold text-red-700 hover:bg-red-50 focus:outline-none focus-visible:ring-2 focus-visible:ring-red-600 focus-visible:ring-offset-2">
                                Archiver
                            </button>
                        </form>
                    @endif
                </div>
            </article>
        </main>
    </div>
</x-app-layout>
