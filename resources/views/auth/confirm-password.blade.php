<x-guest-layout>
    <div class="mb-6">
        <h1 class="cp-heading text-3xl">Confirme ton mot de passe</h1>
    </div>

    <div class="mb-4 text-sm leading-6 text-slate-600">
        Cette zone est protégée. Confirme ton mot de passe pour continuer.
    </div>

    <form method="POST" action="{{ route('password.confirm') }}">
        @csrf

        <div>
            <x-input-label for="password" value="Mot de passe" />

            <x-text-input id="password" class="mt-1 block w-full"
                            type="password"
                            name="password"
                            required autocomplete="current-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="flex justify-end mt-4">
            <x-primary-button>
                Confirmer
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
