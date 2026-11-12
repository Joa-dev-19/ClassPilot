<x-app-layout>
    <x-slot name="header">
        <h2 class="cp-heading text-xl">
            Mon profil
        </h2>
    </x-slot>

    <div class="cp-page">
        <div class="cp-page-inner flex flex-col gap-5">
            <section class="cp-card p-5 sm:p-8">
                <div class="max-w-xl">
                    <section>
                        <header>
                            <h2 class="text-lg font-bold text-slate-900">
                                Informations du profil
                            </h2>

                            <p class="mt-1 text-sm leading-6 text-slate-600">
                                Mets à jour ton nom et ton adresse e-mail.
                            </p>
                        </header>

                        <form method="POST" action="{{ route('profile.update') }}" class="mt-6 space-y-6">
                            @csrf
                            @method('PATCH')

                            <div>
                                <x-input-label for="name" value="Nom complet" />
                                <x-text-input id="name" name="name" type="text" class="mt-1 block w-full"
                                    :value="old('name', $user->name)" required autofocus autocomplete="name" />
                                <x-input-error class="mt-2" :messages="$errors->get('name')" />
                            </div>

                            <div>
                                <x-input-label for="email" value="Adresse e-mail" />
                                <x-text-input id="email" name="email" type="email" class="mt-1 block w-full"
                                    :value="old('email', $user->email)" required autocomplete="username" />
                                <x-input-error class="mt-2" :messages="$errors->get('email')" />
                            </div>

                            <div class="flex items-center gap-4">
                                <x-primary-button>Enregistrer</x-primary-button>

                                @if (session('status') === 'profile-updated')
                                    <p class="text-sm text-emerald-800">Modifications enregistrées.</p>
                                @endif
                            </div>
                        </form>
                    </section>
                </div>
            </section>

            <section class="cp-card p-5 sm:p-8">
                <div class="max-w-xl">
                    @include('profile.partials.update-password-form')
                </div>
            </section>

        </div>
    </div>
</x-app-layout>
