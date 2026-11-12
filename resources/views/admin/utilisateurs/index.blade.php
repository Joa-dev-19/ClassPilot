<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            Gestion des comptes
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto flex max-w-7xl flex-col gap-6 px-4 sm:px-6 lg:px-8">
            @if (session('success'))
                <p role="status" class="rounded-xl border border-green-200 bg-green-50 p-4 text-green-800">
                    {{ session('success') }}
                </p>
            @endif

            <section class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                <h3 class="text-lg font-semibold text-slate-900">Rechercher un compte</h3>
                <form action="{{ route('admin.utilisateurs.index') }}" method="GET"
                      class="mt-4 flex flex-col gap-4 sm:flex-row sm:items-end">
                    <div class="flex-1">
                        <label for="q" class="mb-2 block font-medium text-slate-700">
                            Nom ou adresse e-mail
                        </label>
                        <input id="q" name="q" type="search" maxlength="100" value="{{ $search }}"
                               aria-invalid="@error('q') true @else false @enderror"
                               aria-describedby="@error('q') recherche-error @enderror"
                               class="w-full rounded-xl border-slate-300 focus:border-blue-500 focus:ring-blue-500">
                        @error('q')
                            <p id="recherche-error" role="alert" class="mt-1 text-sm text-red-700">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="flex flex-wrap gap-3">
                        <button type="submit"
                                class="rounded-xl bg-blue-700 px-5 py-3 font-semibold text-white hover:bg-blue-800 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-600 focus-visible:ring-offset-2">
                            Rechercher
                        </button>
                        @if ($search !== '')
                            <a href="{{ route('admin.utilisateurs.index') }}"
                               class="rounded-xl border border-slate-300 px-5 py-3 font-semibold text-slate-700 hover:bg-slate-50 focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-600 focus-visible:ring-offset-2">
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
                    <p class="rounded-2xl border border-slate-200 bg-white p-6 text-slate-700">
                        Aucun compte ne correspond à cette recherche.
                    </p>
                @else
                    <div class="overflow-x-auto rounded-2xl border border-slate-200 bg-white shadow-sm">
                        <table class="min-w-full divide-y divide-slate-200">
                            <caption class="sr-only">Liste paginée des comptes utilisateurs</caption>
                            <thead class="bg-slate-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-600">Nom</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-600">Adresse e-mail</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-600">Rôle</th>
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
                                        <td class="whitespace-nowrap px-6 py-4 text-sm text-slate-700">{{ $user->role }}</td>
                                        <td class="whitespace-nowrap px-6 py-4 text-sm text-slate-700">
                                            {{ $user->statut === \App\Models\User::STATUS_ACTIVE ? 'Actif' : 'Désactivé' }}
                                        </td>
                                        <td class="whitespace-nowrap px-6 py-4 text-sm text-slate-700">{{ $user->created_at->format('d/m/Y') }}</td>
                                        <td class="whitespace-nowrap px-6 py-4 text-sm">
                                            <a href="{{ route('admin.utilisateurs.show', $user) }}"
                                               class="font-semibold text-blue-700 underline decoration-transparent underline-offset-4 hover:decoration-current focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-600 focus-visible:ring-offset-2">
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
