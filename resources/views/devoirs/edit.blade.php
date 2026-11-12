<x-app-layout>
    <div class="cp-page">
        <div class="cp-page-inner flex max-w-3xl flex-col gap-3">
            <h1 class="cp-heading text-3xl">Modifier le devoir</h1>
            <p class="mb-4 text-slate-600">
                Les devoirs publiés restent modifiables par leur enseignant auteur.
            </p>

            @if ($errors->any())
                <div class="rounded-2xl border border-red-200 bg-red-50 p-4 text-red-800" role="alert">
                    <ul class="list-inside list-disc space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @include('devoirs._form', ['devoir' => $devoir])
        </div>
    </div>
</x-app-layout>
