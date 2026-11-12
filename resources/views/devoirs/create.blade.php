<x-app-layout>
    <div class="cp-page">
        <div class="cp-page-inner flex max-w-3xl flex-col gap-3">
            <h1 class="cp-heading text-3xl">Créer un devoir</h1>
            <p class="mb-4 text-slate-600">
                Le devoir sera enregistré en brouillon. Tu pourras le publier après avoir renseigné les consignes.
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

            @include('devoirs._form', ['devoir' => null])
        </div>
    </div>
</x-app-layout>
