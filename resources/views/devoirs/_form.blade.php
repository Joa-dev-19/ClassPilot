@php($isEditing = $devoir !== null)

<form action="{{ $isEditing ? route('devoirs.update', $devoir) : route('devoirs.store') }}" method="POST"
      class="flex flex-col gap-6 rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
    @csrf
    @if ($isEditing)
        @method('PUT')
    @endif

    <div>
        <label for="titre" class="mb-2 block font-medium text-slate-700">Titre du devoir</label>
        <input id="titre" name="titre" type="text" required maxlength="255"
               value="{{ old('titre', $devoir?->titre) }}"
               aria-invalid="@error('titre') true @else false @enderror"
               aria-describedby="@error('titre') titre-error @enderror"
               class="w-full rounded-xl border-slate-300 focus:border-blue-500 focus:ring-blue-500">
        @error('titre')
            <p id="titre-error" class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="consignes" class="mb-2 block font-medium text-slate-700">Consignes</label>
        <textarea id="consignes" name="consignes" rows="6"
                  aria-invalid="@error('consignes') true @else false @enderror"
                  aria-describedby="@error('consignes') consignes-error @enderror"
                  class="w-full rounded-xl border-slate-300 focus:border-blue-500 focus:ring-blue-500">{{ old('consignes', $devoir?->consignes) }}</textarea>
        @error('consignes')
            <p id="consignes-error" class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
        <p class="mt-1 text-sm text-slate-500">Les consignes seront obligatoires au moment de publier.</p>
    </div>

    <div>
        <label for="date_limite" class="mb-2 block font-medium text-slate-700">Date limite</label>
        <input id="date_limite" name="date_limite" type="date" required
               min="{{ $devoir?->date_limite && $devoir->date_limite->isBefore(today()) ? $devoir->date_limite->toDateString() : today()->toDateString() }}"
               value="{{ old('date_limite', $devoir?->date_limite?->toDateString()) }}"
               aria-invalid="@error('date_limite') true @else false @enderror"
               aria-describedby="@error('date_limite') date-limite-error @enderror"
               class="w-full rounded-xl border-slate-300 focus:border-blue-500 focus:ring-blue-500">
        @error('date_limite')
            <p id="date-limite-error" class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div class="flex flex-wrap gap-3">
        <button type="submit"
                class="rounded-xl bg-blue-700 px-5 py-3 font-semibold text-white hover:bg-blue-800 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-600 focus-visible:ring-offset-2">
            {{ $isEditing ? 'Enregistrer les modifications' : 'Enregistrer en brouillon' }}
        </button>
        <a href="{{ $isEditing ? route('devoirs.show', $devoir) : route('devoirs.index') }}"
           class="rounded-xl border border-slate-200 px-5 py-3 font-semibold text-slate-700 hover:bg-slate-50 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-600 focus-visible:ring-offset-2">
            Annuler
        </a>
    </div>
</form>
