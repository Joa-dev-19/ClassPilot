<x-app-layout>
    <x-slot name="header">
        <h2 class="cp-heading text-xl">
            Gestion des comptes
        </h2>
    </x-slot>

    <div class="cp-page">
        <div class="cp-page-inner flex max-w-7xl flex-col gap-6">
            <a href="{{ route('admin.filieres.index') }}"
               class="cp-button-secondary w-full sm:w-fit">
                Gérer les classes et filières
            </a>

            @if (session('success'))
                <p role="status" class="rounded-2xl border border-emerald-200 bg-emerald-50 p-4 text-emerald-900">
                    {{ session('success') }}
                </p>
            @endif

            <section class="cp-card flex flex-col gap-2 p-5 sm:p-6">
                <h3 class="text-lg font-semibold text-slate-900">Rechercher un compte</h3>
                <form action="{{ route('admin.utilisateurs.index') }}" method="GET"
                      class="flex flex-col gap-4 sm:flex-row sm:items-end">
                    <div class="flex-1">
                        <label for="q" class="mb-2 block font-medium text-slate-700">
                            Nom ou adresse e-mail
                        </label>
                        <input id="q" name="q" type="search" maxlength="100" value="{{ $search }}"
                               aria-invalid="@error('q') true @else false @enderror"
                               aria-describedby="@error('q') recherche-error @enderror"
                               class="w-full rounded-xl border-slate-300 bg-white focus:border-emerald-600 focus:ring-emerald-600">
                        @error('q')
                            <p id="recherche-error" role="alert" class="mt-1 text-sm text-red-700">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="flex flex-wrap gap-3">
                        <button type="submit"
                                class="cp-button-primary w-full sm:w-auto">
                            Rechercher
                        </button>
                        @if ($search !== '')
                            <a href="{{ route('admin.utilisateurs.index') }}"
                               class="cp-button-secondary w-full sm:w-auto">
                                Effacer
                            </a>
                        @endif
                    </div>
                </form>
            </section>

            <section class="flex flex-col gap-4">
                <div class="flex flex-wrap items-baseline justify-between gap-2">
                    <h3 class="text-lg font-semibold text-slate-900">Comptes utilisateurs</h3>
                    <p class="text-sm text-slate-600">
                        {{ $users->total() }} résultat{{ $users->total() > 1 ? 's' : '' }}
                    </p>
                </div>

                @if ($users->isEmpty())
                    <p class="cp-card p-6 text-slate-700">
                        Aucun compte ne correspond à cette recherche.
                    </p>
                @else
                    <ul class="flex flex-col gap-3 sm:hidden">
                        @foreach ($users as $user)
                            <li class="cp-card flex flex-col gap-4 p-4">
                                <div class="flex flex-col gap-1">
                                    <a href="{{ route('admin.utilisateurs.show', $user) }}"
                                       class="font-bold text-slate-900 underline decoration-transparent underline-offset-4 hover:decoration-current">
                                        {{ $user->name }}
                                    </a>
                                    <span class="break-all text-sm text-slate-600">{{ $user->email }}</span>
                                </div>
                                <dl class="grid grid-cols-2 gap-3 border-t border-slate-100 pt-3 text-sm">
                                    <div>
                                        <dt class="text-xs font-semibold uppercase tracking-wide text-slate-500">Rôle</dt>
                                        <dd class="mt-1 text-slate-800">{{ match ($user->role) {
                                            \App\Models\User::ROLE_STUDENT => 'Étudiant',
                                            \App\Models\User::ROLE_TEACHER => 'Enseignant',
                                            \App\Models\User::ROLE_ADMINISTRATOR => 'Administrateur',
                                            default => $user->role,
                                        } }}</dd>
                                    </div>
                                    <div>
                                        <dt class="text-xs font-semibold uppercase tracking-wide text-slate-500">Statut</dt>
                                        <dd class="mt-1 text-slate-800">{{ $user->isActive() ? 'Actif' : 'Désactivé' }}</dd>
                                    </div>
                                    <div class="col-span-2">
                                        <dt class="text-xs font-semibold uppercase tracking-wide text-slate-500">Classe / filière</dt>
                                        <dd class="mt-1 text-slate-800">
                                            {{ $user->classe ? $user->classe->nom.' · '.$user->classe->filiere->nom : 'Non rattaché' }}
                                        </dd>
                                    </div>
                                </dl>
                                <a href="{{ route('admin.utilisateurs.show', $user) }}" class="cp-button-secondary w-full text-sm">
                                    Consulter le compte
                                </a>
                            </li>
                        @endforeach
                    </ul>

                    <div class="hidden overflow-x-auto rounded-2xl border border-slate-200 bg-white shadow-card sm:block">
                        <table class="min-w-full divide-y divide-slate-200">
                            <caption class="sr-only">Liste paginée des comptes utilisateurs</caption>
                            <thead class="bg-slate-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-600">Nom</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-600">Adresse e-mail</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-600">Rôle</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-600">Classe / filière</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-600">Statut</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-600">Créé le</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-600">Action</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-200">
                                @foreach ($users as $user)
                                    <tr>
                                        <th scope="row" class="whitespace-nowrap px-6 py-4 text-left font-medium text-slate-900">
                                            {{ $user->name }}
                                        </th>
                                        <td class="whitespace-nowrap px-6 py-4 text-sm text-slate-700">{{ $user->email }}</td>
                                        <td class="whitespace-nowrap px-6 py-4 text-sm text-slate-700">{{ match ($user->role) {
                                            \App\Models\User::ROLE_STUDENT => 'Étudiant',
                                            \App\Models\User::ROLE_TEACHER => 'Enseignant',
                                            \App\Models\User::ROLE_ADMINISTRATOR => 'Administrateur',
                                            default => $user->role,
                                        } }}</td>
                                        <td class="whitespace-nowrap px-6 py-4 text-sm text-slate-700">
                                            @if ($user->classe)
                                                {{ $user->classe->nom }} · {{ $user->classe->filiere->nom }}
                                            @else
                                                <span class="text-slate-500">Non rattaché</span>
                                            @endif
                                        </td>
                                        <td class="whitespace-nowrap px-6 py-4 text-sm text-slate-700">
                                            {{ $user->statut === \App\Models\User::STATUS_ACTIVE ? 'Actif' : 'Désactivé' }}
                                        </td>
                                        <td class="whitespace-nowrap px-6 py-4 text-sm text-slate-700">{{ $user->created_at->format('d/m/Y') }}</td>
                                        <td class="whitespace-nowrap px-6 py-4 text-sm">
                                            <a href="{{ route('admin.utilisateurs.show', $user) }}"
                                               class="font-semibold text-emerald-800 underline decoration-transparent underline-offset-4 hover:decoration-current focus-visible:ring-2 focus-visible:ring-emerald-600">
                                                Consulter
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <nav aria-label="Pagination des comptes">
                        {{ $users->links() }}
                    </nav>
                @endif
            </section>
        </div>
    </div>
</x-app-layout>
