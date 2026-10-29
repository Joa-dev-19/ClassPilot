<x-app-layout>
    <div class="min-h-screen bg-slate-50 px-5 py-8 sm:px-8">
        <div class="mx-auto max-w-3xl">
            <div class="mb-8">
                <a href="{{ route('taches.index') }}"
                   class="text-sm font-semibold text-blue-700 hover:text-blue-800">
                    ← Retour à mes tâches
                </a>
                <h1 class="mt-4 text-2xl font-bold text-slate-900">Nouvelle tâche</h1>
                <p class="mt-2 text-slate-500">
                    Ajoute une tâche pour organiser ton travail scolaire.
                </p>
            </div>

            <form method="POST" action="{{ route('taches.store') }}"
                  class="space-y-6 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm sm:p-8">
                @csrf

                <div>
                    <label for="titre" class="block text-sm font-semibold text-slate-700">
                        Titre
                    </label>
                    <input id="titre" name="titre" type="text" value="{{ old('titre') }}"
                           required maxlength="255" autofocus
                           aria-invalid="@error('titre') true @else false @enderror"
                           aria-describedby="@error('titre') titre-error @enderror"
                           class="mt-2 block w-full rounded-xl border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    @error('titre')
                        <p id="titre-error" class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="description" class="block text-sm font-semibold text-slate-700">
                        Description <span class="font-normal text-slate-500">(facultatif)</span>
                    </label>
                    <textarea id="description" name="description" rows="4"
                              aria-invalid="@error('description') true @else false @enderror"
                              aria-describedby="@error('description') description-error @enderror"
                              class="mt-2 block w-full rounded-xl border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ old('description') }}</textarea>
                    @error('description')
                        <p id="description-error" class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                    <div>
                        <label for="date_limite" class="block text-sm font-semibold text-slate-700">
                            Date limite <span class="font-normal text-slate-500">(facultatif)</span>
                        </label>
                        <input id="date_limite" name="date_limite" type="date"
                               value="{{ old('date_limite') }}"
                               aria-invalid="@error('date_limite') true @else false @enderror"
                               aria-describedby="@error('date_limite') date-limite-error @enderror"
                               class="mt-2 block w-full rounded-xl border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        @error('date_limite')
                            <p id="date-limite-error" class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="priorite" class="block text-sm font-semibold text-slate-700">
                            Priorité
                        </label>
                        <select id="priorite" name="priorite" required
                                aria-invalid="@error('priorite') true @else false @enderror"
                                aria-describedby="@error('priorite') priorite-error @enderror"
                                class="mt-2 block w-full rounded-xl border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="faible" @selected(old('priorite', 'moyenne') === 'faible')>Faible</option>
                            <option value="moyenne" @selected(old('priorite', 'moyenne') === 'moyenne')>Moyenne</option>
                            <option value="elevee" @selected(old('priorite') === 'elevee')>Élevée</option>
                        </select>
                        @error('priorite')
                            <p id="priorite-error" class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="statut" class="block text-sm font-semibold text-slate-700">
                            Statut
                        </label>
                        <select id="statut" name="statut" required
                                aria-invalid="@error('statut') true @else false @enderror"
                                aria-describedby="@error('statut') statut-error @enderror"
                                class="mt-2 block w-full rounded-xl border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="a_faire" @selected(old('statut', 'a_faire') === 'a_faire')>À faire</option>
                            <option value="en_cours" @selected(old('statut') === 'en_cours')>En cours</option>
                            <option value="terminee" @selected(old('statut') === 'terminee')>Terminée</option>
                        </select>
                        @error('statut')
                            <p id="statut-error" class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="flex flex-wrap justify-end gap-3 border-t border-slate-100 pt-5">
                    <a href="{{ route('taches.index') }}"
                       class="rounded-xl border border-slate-200 px-5 py-3 text-sm font-semibold text-slate-700 hover:bg-slate-50 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-600 focus-visible:ring-offset-2">
                        Annuler
                    </a>
                    <button type="submit"
                            class="rounded-xl bg-blue-700 px-5 py-3 text-sm font-semibold text-white hover:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                        Enregistrer la tâche
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
