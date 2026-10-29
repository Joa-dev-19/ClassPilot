<x-guest-layout>
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Vérifie ton adresse e-mail</h1>
    </div>

    <div class="mb-4 text-sm text-gray-600">
        Ton compte est créé. Consulte ta boîte de réception et clique sur le lien de vérification avant d’accéder à ton espace ClassPilot. Si tu ne reçois pas le message, tu peux en demander un nouveau.
    </div>

    @if (session('status') == 'verification-link-sent')
        <div class="mb-4 font-medium text-sm text-green-600">
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

            <button type="submit" class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                Se déconnecter
            </button>
        </form>
    </div>
</x-guest-layout>
