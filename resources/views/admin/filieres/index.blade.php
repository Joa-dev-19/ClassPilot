<x-app-layout>
    <x-slot name="header">
        <h2 class="cp-heading text-xl">
            Gestion des classes et filières
        </h2>
    </x-slot>

    <div class="cp-page">
        <div class="cp-page-inner flex max-w-7xl flex-col gap-6">
            <div class="flex flex-wrap gap-3">
                <a href="{{ route('admin.utilisateurs.index') }}"
                   class="cp-button-secondary w-full sm:w-fit">
                    Gestion des comptes
                </a>
            </div>

            @if (session('success'))
                <p role="status" class="rounded-2xl border border-emerald-200 bg-emerald-50 p-4 text-emerald-900">
                    {{ session('success') }}
                </p>
            @endif

            @if ($errors->any())
                <div role="alert" class="rounded-2xl border border-red-200 bg-red-50 p-4 text-red-800">
                    <ul class="list-inside list-disc">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <section class="cp-card flex flex-col gap-2 p-5 sm:p-6">
                <h3 class="text-lg font-semibold text-slate-900">Créer une filière</h3>
                <form action="{{ route('admin.filieres.store') }}" method="POST"
                      class="mt-4 flex flex-col gap-4 sm:flex-row sm:items-end">
                    @csrf
                    <div class="flex-1">
                        <label for="nouvelle-filiere" class="mb-2 block font-medium text-slate-700">
                            Nom de la filière
                        </label>
                        <input id="nouvelle-filiere" name="nom" type="text" required maxlength="100"
                               class="w-full rounded-xl border-slate-300 bg-white focus:border-emerald-600 focus:ring-emerald-600">
                    </div>
                    <button type="submit"
                            class="cp-button-primary w-full sm:w-auto">
                        Ajouter la filière
                    </button>
                </form>
            </section>

            @if ($filieres->isEmpty())
                <p class="cp-card p-6 text-slate-700">
                    Aucune filière n’a été créée.
                </p>
            @else
                <section class="flex flex-col gap-6" aria-label="Filières et classes">
                    @foreach ($filieres as $filiere)
                        <article class="cp-card flex flex-col gap-5 p-5 sm:p-6">
                            <form action="{{ route('admin.filieres.update', $filiere) }}" method="POST"
                                  class="flex flex-col gap-4 border-b border-slate-200 pb-5 sm:flex-row sm:items-end">
                                @csrf
                                @method('PUT')
                                <div class="flex-1">
                                    <label for="filiere-{{ $filiere->id }}" class="mb-2 block font-semibold text-slate-800">
                                        Filière
                                    </label>
                                    <input id="filiere-{{ $filiere->id }}" name="nom" type="text" required maxlength="100"
                                           value="{{ $filiere->nom }}"
                                           class="w-full rounded-xl border-slate-300 bg-white focus:border-emerald-600 focus:ring-emerald-600">
                                </div>
                                <button type="submit"
                                        class="cp-button-secondary w-full sm:w-auto">
                                    Renommer
                                </button>
                            </form>

                            <div>
                                <h3 class="font-semibold text-slate-900">Classes de {{ $filiere->nom }}</h3>
                                @if ($filiere->classes->isEmpty())
                                    <p class="mt-3 text-sm text-slate-600">Aucune classe dans cette filière.</p>
                                @else
                                    <ul class="mt-3 flex flex-col gap-3">
                                        @foreach ($filiere->classes as $classe)
                                            <li class="rounded-xl bg-slate-50 p-4">
                                                <form action="{{ route('admin.classes.update', [$filiere, $classe]) }}"
                                                      method="POST"
                                                      class="flex flex-col gap-3 sm:flex-row sm:items-end">
                                                    @csrf
                                                    @method('PUT')
                                                    <div class="flex-1">
                                                        <label for="classe-{{ $classe->id }}" class="mb-2 block text-sm font-medium text-slate-700">
                                                            Nom de la classe
                                                        </label>
                                                        <input id="classe-{{ $classe->id }}" name="nom" type="text" required maxlength="100"
                                                               value="{{ $classe->nom }}"
                                                               class="w-full rounded-xl border-slate-300 bg-white focus:border-emerald-600 focus:ring-emerald-600">
                                                    </div>
                                                    <p class="text-sm text-slate-600">
                                                        {{ $classe->etudiants_count }} étudiant(s) · {{ $classe->devoirs_count }} devoir(s)
                                                    </p>
                                                    <button type="submit"
                                                            class="cp-button-secondary w-full sm:w-auto">
                                                        Renommer
                                                    </button>
                                                </form>
                                            </li>
                                        @endforeach
                                    </ul>
                                @endif
                            </div>

                            <form action="{{ route('admin.classes.store', $filiere) }}" method="POST"
                                  class="flex flex-col gap-4 border-t border-slate-200 pt-5 sm:flex-row sm:items-end">
                                @csrf
                                <div class="flex-1">
                                    <label for="nouvelle-classe-{{ $filiere->id }}" class="mb-2 block font-medium text-slate-700">
                                        Nouvelle classe
                                    </label>
                                    <input id="nouvelle-classe-{{ $filiere->id }}" name="nom" type="text" required maxlength="100"
                                           class="w-full rounded-xl border-slate-300 bg-white focus:border-emerald-600 focus:ring-emerald-600">
                                </div>
                                <button type="submit"
                                        class="cp-button-primary w-full sm:w-auto">
                                    Ajouter la classe
                                </button>
                            </form>
                        </article>
                    @endforeach
                </section>
            @endif
        </div>
    </div>
</x-app-layout>
