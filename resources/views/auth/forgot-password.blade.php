<x-guest-layout>
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Réinitialiser le mot de passe</h1>
    </div>

    <div class="mb-4 text-sm text-gray-600">
        Indique l’adresse e-mail associée à ton compte. Si elle existe, nous t’enverrons un lien pour choisir un nouveau mot de passe.
    </div>

    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}">
        @csrf

        <div>
            <x-input-label for="email" value="Adresse e-mail" />
            <x-text-input id="email" class="mt-1 block w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="email" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="mt-6 flex items-center justify-between gap-4">
            <a class="text-sm text-gray-600 underline hover:text-gray-900" href="{{ route('login') }}">
                Retour à la connexion
            </a>
            <x-primary-button>
                Envoyer le lien
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
