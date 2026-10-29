<?php

namespace App\Console\Commands;

use App\Models\Rappel;
use App\Notifications\RappelTacheNotification;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

#[Signature('app:envoyer-rappels')]
#[Description('Envoie les rappels arrivés à échéance')]
class EnvoyerRappels extends Command
{
    public function handle(): int
    {
        Rappel::query()
            ->where('statut', Rappel::STATUS_PENDING)
            ->where('date_heure_rappel', '<=', now())
            ->whereHas('tache', fn (Builder $query): Builder => $query->where('statut', '!=', 'terminee'))
            ->with(['tache.user'])
            ->chunkById(100, function (Collection $rappels): void {
                foreach ($rappels as $rappel) {
                    $rappel->tache->user->notify(new RappelTacheNotification($rappel->tache));
                    $rappel->update(['statut' => Rappel::STATUS_SENT]);
                }
            });

        return self::SUCCESS;
    }
}
