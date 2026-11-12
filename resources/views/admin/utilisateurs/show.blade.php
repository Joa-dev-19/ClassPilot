<x-app-layout>
    <x-slot name="header">
        <h2 class="cp-heading text-xl">
            Compte utilisateur
        </h2>
    </x-slot>

    <div class="cp-page">
        <div class="cp-page-inner flex max-w-3xl flex-col gap-6">
            <a href="{{ route('admin.utilisateurs.index') }}"
               class="cp-button-secondary w-full text-sm sm:w-fit">
                Retour à la liste
            </a>

            @if (session('success'))
                <p role="status" class="rounded-2xl border border-emerald-200 bg-emerald-50 p-4 text-emerald-900">
                    {{ session('success') }}
                </p>
            @endif

            <section class="cp-card flex flex-col gap-6 p-5 sm:p-7">
                <div>
                    <h3 class="text-2xl font-semibold text-slate-900">{{ $user->name }}</h3>
                    <p class="mt-1 text-slate-600">{{ $user->email }}</p>
                </div>

                <dl class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <dt class="text-sm font-medium text-slate-600">Rôle</dt>
                        <dd class="mt-1 text-slate-900">{{ $user->role }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-slate-600">Statut du compte</dt>
                        <dd class="mt-1 text-slate-900">
                            {{ $user->isActive() ? 'Actif' : 'Désactivé' }}
                        </dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-slate-600">Adresse e-mail vérifiée</dt>
                        <dd class="mt-1 text-slate-900">
                            {{ $user->email_verified_at ? 'Oui' : 'Non' }}
                        </dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-slate-600">Date de création</dt>
                        <dd class="mt-1 text-slate-900">{{ $user->created_at->format('d/m/Y à H:i') }}</dd>
                    </div>
                    @if ($user->role === \App\Models\User::ROLE_STUDENT)
                        <div>
                            <dt class="text-sm font-medium text-slate-600">Classe / filière</dt>
                            <dd class="mt-1 text-slate-900">
                                @if ($user->classe)
                                    {{ $user->classe->nom }} · {{ $user->classe->filiere->nom }}
                                @else
                                    Non rattaché
                                @endif
                            </dd>
                        </div>
                    @endif
                </dl>

                @if ($user->role === \App\Models\User::ROLE_STUDENT)
                    <form action="{{ route('admin.utilisateurs.classe.update', $user) }}" method="POST"
                          class="flex flex-col gap-4 border-t border-slate-200 pt-6">
                        @csrf
                        @method('PATCH')
                        <div>
                            <label for="classe_id" class="mb-2 block font-medium text-slate-700">
                                Rattacher l’étudiant à une classe
                            </label>
                            <select id="classe_id" name="classe_id"
                                    aria-invalid="@error('classe_id') true @else false @enderror"
                                    aria-describedby="@error('classe_id') classe-error @enderror"
                                    class="w-full rounded-xl border-slate-300 bg-white focus:border-emerald-600 focus:ring-emerald-600">
                                <option value="">Aucune classe</option>
                                @foreach ($classes as $classe)
                                    <option value="{{ $classe->id }}"
                                        @selected((string) old('classe_id', $user->classe_id) === (string) $classe->id)>
                                        {{ $classe->nom }} · {{ $classe->filiere->nom }}
                                    </option>
                                @endforeach
                            </select>
                            @error('classe_id')
                                <p id="classe-error" role="alert" class="mt-1 text-sm text-red-700">{{ $message }}</p>
                            @enderror
                        </div>
                        <button type="submit"
                                class="cp-button-primary w-full sm:w-fit">
                            Enregistrer la classe
                        </button>
                    </form>
                @endif

                @error('statut')
                    <p role="alert" class="rounded-xl border border-red-200 bg-red-50 p-4 text-red-800">
                        {{ $message }}
                    </p>
                @enderror

                @if ($user->isActive())
                    <form action="{{ route('admin.utilisateurs.deactivate', $user) }}" method="POST"
                          class="border-t border-slate-200 pt-6">
                        @csrf
                        @method('PATCH')
                        <p class="mb-4 text-sm text-slate-600">
                            La désactivation bloquera le compte au prochain accès à une page protégée et invalidera sa session.
                        </p>
                        <button type="submit"
                                class="w-full rounded-xl bg-red-700 px-5 py-3 font-semibold text-white transition hover:bg-red-800 focus-visible:ring-2 focus-visible:ring-red-600 sm:w-fit">
                            Désactiver ce compte
                        </button>
                    </form>
                @else
                    <form action="{{ route('admin.utilisateurs.activate', $user) }}" method="POST"
                          class="border-t border-slate-200 pt-6">
                        @csrf
                        @method('PATCH')
                        <button type="submit"
                                class="cp-button-primary w-full sm:w-fit">
                            Réactiver ce compte
                        </button>
                    </form>
                @endif
            </section>
        </div>
    </div>
</x-app-layout>
