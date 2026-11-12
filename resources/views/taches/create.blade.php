<x-app-layout>
    <div class="cp-page">
        <div class="cp-page-inner flex max-w-3xl flex-col gap-3">

            <h1 class="cp-heading text-3xl">
                Nouvelle tâche
            </h1>

            <p class="text-slate-600">
                Ajoute une activité à ton organisation scolaire.
            </p>

            <form action="{{ route('taches.store') }}" method="POST"
                  class="cp-card flex flex-col gap-6 p-5 sm:p-8">
                @csrf

                <div>
                    <label for="titre" class="mb-2 block font-semibold text-slate-800">
                        Titre de la tâche
                    </label>
                    <input id="titre" name="titre" type="text" required
                           maxlength="255"
                           value="{{ old('titre') }}"
                           aria-invalid="@error('titre') true @else false @enderror"
                           aria-describedby="@error('titre') titre-error @enderror"
                           class="w-full rounded-xl border-slate-300 bg-white focus:border-emerald-600 focus:ring-emerald-600"
                           placeholder="Ex. : Préparer l'exposé">
                    @error('titre')
                        <p id="titre-error" class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="description" class="mb-2 block font-semibold text-slate-800">
                        Description
                    </label>
                    <textarea id="description" name="description" rows="4"
                              aria-invalid="@error('description') true @else false @enderror"
                              aria-describedby="@error('description') description-error @enderror"
                              class="w-full rounded-xl border-slate-300 bg-white focus:border-emerald-600 focus:ring-emerald-600"
                              placeholder="Ajoute quelques détails...">{{ old('description') }}</textarea>
                    @error('description')
                        <p id="description-error" class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="date_limite" class="mb-2 block font-semibold text-slate-800">
                        Date limite
                    </label>
                    <input id="date_limite" name="date_limite" type="date"
                           value="{{ old('date_limite') }}"
                           aria-invalid="@error('date_limite') true @else false @enderror"
                           aria-describedby="@error('date_limite') date-limite-error @enderror"
                           class="w-full rounded-xl border-slate-300 bg-white focus:border-emerald-600 focus:ring-emerald-600">
                    @error('date_limite')
                        <p id="date-limite-error" class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid gap-6 sm:grid-cols-2">
                    <div>
                        <label for="priorite" class="mb-2 block font-semibold text-slate-800">
                            Priorité
                        </label>
                        <select id="priorite" name="priorite" required
                                aria-invalid="@error('priorite') true @else false @enderror"
                                aria-describedby="@error('priorite') priorite-error @enderror"
                                class="w-full rounded-xl border-slate-300 bg-white focus:border-emerald-600 focus:ring-emerald-600">
                            <option value="faible" @selected(old('priorite', 'moyenne') === 'faible')>Faible</option>
                            <option value="moyenne" @selected(old('priorite', 'moyenne') === 'moyenne')>Moyenne</option>
                            <option value="elevee" @selected(old('priorite') === 'elevee')>Élevée</option>
                        </select>
                        @error('priorite')
                            <p id="priorite-error" class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="statut" class="mb-2 block font-semibold text-slate-800">
                            Statut
                        </label>
                        <select id="statut" name="statut" required
                                aria-invalid="@error('statut') true @else false @enderror"
                                aria-describedby="@error('statut') statut-error @enderror"
                                class="w-full rounded-xl border-slate-300 bg-white focus:border-emerald-600 focus:ring-emerald-600">
                            <option value="a_faire" @selected(old('statut', 'a_faire') === 'a_faire')>À faire</option>
                            <option value="en_cours" @selected(old('statut') === 'en_cours')>En cours</option>
                            <option value="terminee" @selected(old('statut') === 'terminee')>Terminée</option>
                        </select>
                        @error('statut')
                            <p id="statut-error" class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="flex flex-wrap gap-3 pt-2">
                    <button type="submit"
                            class="cp-button-primary w-full sm:w-auto">
                        Enregistrer la tâche
                    </button>

                    <a href="{{ route('taches.index') }}"
                       class="cp-button-secondary w-full sm:w-auto">
                        Annuler
                    </a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>