<x-guest-layout>
    <div class="mb-6">
        <h1 class="cp-heading text-3xl">Créer un compte étudiant</h1>
        <p class="mt-2 text-sm leading-6 text-slate-600">
            Inscris-toi pour organiser tes tâches et suivre tes échéances.
        </p>
    </div>

    <form method="POST" action="{{ route('register') }}">
        @csrf

        <div>
            <x-input-label for="name" value="Nom complet" />
            <x-text-input id="name" class="mt-1 block w-full" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" :aria-invalid="$errors->has('name') ? 'true' : 'false'" :aria-describedby="$errors->has('name') ? 'name-error' : null" />
            <x-input-error id="name-error" :messages="$errors->get('name')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="email" value="Adresse e-mail" />
            <x-text-input id="email" class="mt-1 block w-full" type="email" name="email" :value="old('email')" required autocomplete="email" :aria-invalid="$errors->has('email') ? 'true' : 'false'" :aria-describedby="$errors->has('email') ? 'email-error' : null" />
            <x-input-error id="email-error" :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="password" value="Mot de passe" />

            <x-text-input id="password" class="mt-1 block w-full"
                            type="password"
                            name="password"
                            required autocomplete="new-password" :aria-invalid="$errors->has('password') ? 'true' : 'false'" :aria-describedby="$errors->has('password') ? 'password-error' : null" />

            <x-input-error id="password-error" :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="password_confirmation" value="Confirmer le mot de passe" />

            <x-text-input id="password_confirmation" class="mt-1 block w-full"
                            type="password"
                            name="password_confirmation" required autocomplete="new-password" :aria-invalid="$errors->has('password_confirmation') ? 'true' : 'false'" :aria-describedby="$errors->has('password_confirmation') ? 'password-confirmation-error' : null" />

            <x-input-error id="password-confirmation-error" :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="mt-6 flex flex-col-reverse items-center justify-between gap-4 sm:flex-row">
            <a class="rounded-md text-sm text-slate-600 underline decoration-transparent underline-offset-4 hover:text-slate-900 hover:decoration-current focus-visible:ring-2 focus-visible:ring-emerald-600" href="{{ route('login') }}">
                Déjà inscrit ? Se connecter
            </a>

            <x-primary-button>
                Créer mon compte
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
