<x-guest-layout>
    <div class="mb-6">
        <h1 class="cp-heading text-3xl">Vérifie ton adresse e-mail</h1>
    </div>

    <div class="mb-4 text-sm leading-6 text-slate-600">
        Ton compte est créé. Consulte ta boîte de réception et clique sur le lien de vérification avant d’accéder à ton espace ClassPilot. Si tu ne reçois pas le message, tu peux en demander un nouveau.
    </div>

    @if (session('status') == 'verification-link-sent')
        <div class="mb-4 rounded-xl border border-emerald-200 bg-emerald-50 p-3 text-sm font-medium text-emerald-800" role="status">
            Un nouveau lien de vérification vient d’être envoyé à ton adresse e-mail.
        </div>
    @endif

    <div class="mt-4 flex items-center justify-between">
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf

            <div>
                <x-primary-button>
                    Renvoyer le lien de vérification
                </x-primary-button>
            </div>
        </form>

        <form method="POST" action="{{ route('logout') }}">
            @csrf

            <button type="submit" class="rounded-md text-sm text-slate-600 underline decoration-transparent underline-offset-4 hover:text-slate-900 hover:decoration-current focus-visible:ring-2 focus-visible:ring-emerald-600">
                Se déconnecter
            </button>
        </form>
    </div>
</x-guest-layout>
