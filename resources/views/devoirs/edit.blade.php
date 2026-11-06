<x-app-layout>
    <div class="min-h-screen bg-slate-50 px-5 py-8 sm:px-8">
        <main class="mx-auto max-w-3xl">
            <h1 class="text-2xl font-bold text-slate-900">Modifier le devoir</h1>
            <p class="mb-8 mt-2 text-slate-600">
                Les devoirs publiés restent modifiables par leur enseignant auteur.
            </p>

            @if ($errors->any())
                <div class="mb-6 rounded-xl bg-red-50 p-4 text-red-800" role="alert">
                    <ul class="list-inside list-disc space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @include('devoirs._form', ['devoir' => $devoir])
        </main>
    </div>
</x-app-layout>
