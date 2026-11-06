<?php

namespace App\Console\Commands;

use App\Models\Rappel;
use App\Notifications\RappelTacheNotification;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Contracts\Notifications\Dispatcher;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

#[Signature('app:envoyer-rappels')]
#[Description('Envoie les rappels arrivés à échéance')]
class EnvoyerRappels extends Command
{
    private const MAX_ATTEMPTS = 3;

    public function handle(Dispatcher $notifications): int
    {
        $failedAttempts = 0;

        Rappel::query()
            ->where('statut', Rappel::STATUS_PENDING)
            ->where('date_heure_rappel', '<=', now())
            ->whereHas('tache', fn (Builder $query): Builder => $query->where('statut', '!=', 'terminee'))
            ->select('id')
            ->chunkById(100, function (Collection $rappels) use ($notifications, &$failedAttempts): void {
                foreach ($rappels as $rappel) {
                    if (! $this->sendReminder($rappel->id, $notifications)) {
                        $failedAttempts++;
                    }
                }
            });

        return $failedAttempts === 0 ? self::SUCCESS : self::FAILURE;
    }

    private function sendReminder(int $rappelId, Dispatcher $notifications): bool
    {
        return DB::transaction(function () use ($rappelId, $notifications): bool {
            $rappel = Rappel::query()
                ->with(['tache.user'])
                ->lockForUpdate()
                ->find($rappelId);

            if (
                $rappel === null
                || $rappel->statut !== Rappel::STATUS_PENDING
                || $rappel->date_heure_rappel->isFuture()
                || $rappel->tache->statut === 'terminee'
            ) {
                return true;
            }

            $tentative = $rappel->tentatives + 1;

            try {
                $notifications->send(
                    $rappel->tache->user,
                    new RappelTacheNotification($rappel->tache)
                );
            } catch (Throwable $exception) {
                $rappel->statut = $tentative >= self::MAX_ATTEMPTS
                    ? Rappel::STATUS_FAILED
                    : Rappel::STATUS_PENDING;
                $rappel->tentatives = $tentative;
                $rappel->save();

                Log::warning('Échec de l’envoi d’un rappel.', [
                    'rappel_id' => $rappel->id,
                    'tentative' => $tentative,
                    'exception' => $exception::class,
                ]);

                return false;
            }

            $rappel->statut = Rappel::STATUS_SENT;
            $rappel->tentatives = $tentative;
            $rappel->save();

            return true;
        });
    }
}
