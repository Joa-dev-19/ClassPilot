@php($isEditing = $devoir !== null)

<form action="{{ $isEditing ? route('devoirs.update', $devoir) : route('devoirs.store') }}" method="POST"
      class="cp-card flex flex-col gap-6 p-5 sm:p-8">
    @csrf
    @if ($isEditing)
        @method('PUT')
    @endif

    <div>
        <label for="titre" class="mb-2 block font-semibold text-slate-800">Titre du devoir</label>
        <input id="titre" name="titre" type="text" required maxlength="255"
               value="{{ old('titre', $devoir?->titre) }}"
               aria-invalid="@error('titre') true @else false @enderror"
               aria-describedby="@error('titre') titre-error @enderror"
               class="w-full rounded-xl border-slate-300 bg-white focus:border-emerald-600 focus:ring-emerald-600">
        @error('titre')
            <p id="titre-error" class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="consignes" class="mb-2 block font-semibold text-slate-800">Consignes</label>
        <textarea id="consignes" name="consignes" rows="6"
                  aria-invalid="@error('consignes') true @else false @enderror"
                  aria-describedby="@error('consignes') consignes-error @enderror"
                  class="w-full rounded-xl border-slate-300 bg-white focus:border-emerald-600 focus:ring-emerald-600">{{ old('consignes', $devoir?->consignes) }}</textarea>
        @error('consignes')
            <p id="consignes-error" class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
        <p class="mt-1 text-sm text-slate-500">Les consignes seront obligatoires au moment de publier.</p>
    </div>

    <div>
        <label for="date_limite" class="mb-2 block font-semibold text-slate-800">Date limite</label>
        <input id="date_limite" name="date_limite" type="date" required
               min="{{ $devoir?->date_limite && $devoir->date_limite->isBefore(today()) ? $devoir->date_limite->toDateString() : today()->toDateString() }}"
               value="{{ old('date_limite', $devoir?->date_limite?->toDateString()) }}"
               aria-invalid="@error('date_limite') true @else false @enderror"
               aria-describedby="@error('date_limite') date-limite-error @enderror"
               class="w-full rounded-xl border-slate-300 bg-white focus:border-emerald-600 focus:ring-emerald-600">
        @error('date_limite')
            <p id="date-limite-error" class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="filiere_id" class="mb-2 block font-semibold text-slate-800">Filière destinataire (facultatif)</label>
        <select id="filiere_id" name="filiere_id"
                aria-invalid="@error('filiere_id') true @else false @enderror"
                aria-describedby="@error('filiere_id') filiere-error @enderror"
                class="w-full rounded-xl border-slate-300 bg-white focus:border-emerald-600 focus:ring-emerald-600">
            <option value="">Toutes les filières</option>
            @foreach ($filieres as $filiere)
                <option value="{{ $filiere->id }}"
                    @selected((string) old('filiere_id', $devoir?->filiere_id) === (string) $filiere->id)>
                    {{ $filiere->nom }}
                </option>
            @endforeach
        </select>
        @error('filiere_id')
            <p id="filiere-error" class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="classe_id" class="mb-2 block font-semibold text-slate-800">Classe destinataire (facultatif)</label>
        <select id="classe_id" name="classe_id"
                aria-invalid="@error('classe_id') true @else false @enderror"
                aria-describedby="@error('classe_id') classe-error @enderror"
                class="w-full rounded-xl border-slate-300 bg-white focus:border-emerald-600 focus:ring-emerald-600">
            <option value="">Tous les étudiants</option>
            @foreach ($classes as $classe)
                <option value="{{ $classe->id }}"
                    @selected((string) old('classe_id', $devoir?->classe_id) === (string) $classe->id)>
                    {{ $classe->nom }} · {{ $classe->filiere->nom }}
                </option>
            @endforeach
        </select>
        @error('classe_id')
            <p id="classe-error" class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
        <p class="mt-1 text-sm text-slate-500">
            Choisis au plus une filière ou une classe. Si aucune n’est choisie, le devoir sera visible par tous les étudiants.
        </p>
    </div>

    <div class="flex flex-wrap gap-3">
        <button type="submit"
                class="cp-button-primary w-full sm:w-auto">
            {{ $isEditing ? 'Enregistrer les modifications' : 'Enregistrer en brouillon' }}
        </button>
        <a href="{{ $isEditing ? route('devoirs.show', $devoir) : route('devoirs.index') }}"
           class="cp-button-secondary w-full sm:w-auto">
            Annuler
        </a>
    </div>
</form>
