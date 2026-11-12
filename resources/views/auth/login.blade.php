<x-guest-layout>
    <div class="mb-6">
        <h1 class="cp-heading text-3xl">Connexion à ClassPilot</h1>
        <p class="mt-2 text-sm leading-6 text-slate-600">
            Connecte-toi pour retrouver tes tâches et tes échéances.
        </p>
    </div>

    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <div>
            <x-input-label for="email" value="Adresse e-mail" />
            <x-text-input id="email" class="mt-1 block w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="email" :aria-invalid="$errors->has('email') ? 'true' : 'false'" :aria-describedby="$errors->has('email') ? 'email-error' : null" />
            <x-input-error id="email-error" :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="password" value="Mot de passe" />

            <x-text-input id="password" class="mt-1 block w-full"
                            type="password"
                            name="password"
                            required autocomplete="current-password" :aria-invalid="$errors->has('password') ? 'true' : 'false'" :aria-describedby="$errors->has('password') ? 'password-error' : null" />

            <x-input-error id="password-error" :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="mt-4">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox" class="rounded border-slate-300 text-emerald-700 shadow-sm focus:ring-emerald-600" name="remember">
                <span class="ms-2 text-sm text-slate-600">Se souvenir de moi</span>
            </label>
        </div>

        <div class="mt-6 flex flex-col-reverse items-center justify-between gap-4 sm:flex-row">
            @if (Route::has('password.request'))
                <a class="rounded-md text-sm text-slate-600 underline decoration-transparent underline-offset-4 hover:text-slate-900 hover:decoration-current focus-visible:ring-2 focus-visible:ring-emerald-600" href="{{ route('password.request') }}">
                    Mot de passe oublié ?
                </a>
            @endif

            <x-primary-button>
                Se connecter
            </x-primary-button>
        </div>

        @if (Route::has('register'))
            <p class="mt-6 border-t border-slate-200 pt-5 text-center text-sm text-slate-600">
                Pas encore de compte ?
                <a class="font-semibold text-emerald-800 underline decoration-transparent underline-offset-4 hover:decoration-current" href="{{ route('register') }}">
                    Créer un compte étudiant
                </a>
            </p>
        @endif
    </form>
</x-guest-layout>
