<x-mail::message>
# Bienvenue sur ClassPilot

Bonjour {{ $user->name }},

Merci d’avoir créé ton compte ClassPilot. Pour confirmer ton adresse e-mail et activer ton accès, clique sur le bouton ci-dessous.

<x-mail::button :url="$url">
Confirmer mon adresse e-mail
</x-mail::button>

Si le bouton ne fonctionne pas, copie et colle ce lien dans ton navigateur :

{{ $url }}

Si tu n’es pas à l’origine de cette inscription, tu peux ignorer ce message. Ton adresse e-mail ne sera pas confirmée sans validation.

À bientôt,<br>
L’équipe {{ config('app.name') }}
</x-mail::message>
