<x-mail::message>
# Bonjour {{ $recipientName }},

Un petit rappel : la date limite de cette tâche approche.

<x-mail::panel>
**{{ $taskTitle }}**

**Échéance :** {{ $deadline }}
</x-mail::panel>

<x-mail::button :url="$tasksUrl">
Ouvrir mes tâches
</x-mail::button>

Si tu as déjà terminé cette tâche, pense à mettre son statut à jour.

À bientôt,<br>
L’équipe {{ config('app.name') }}
</x-mail::message>
