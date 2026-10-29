<?php

namespace Tests\Feature\Notifications;

use App\Models\Tache;
use App\Models\User;
use App\Notifications\RappelTacheNotification;
use Tests\TestCase;

class RappelTacheNotificationTest extends TestCase
{
    public function test_task_reminder_email_renders_task_details_and_action_link(): void
    {
        $user = User::factory()->make(['name' => 'Alice']);
        $tache = new Tache([
            'titre' => 'Réviser les mathématiques',
            'date_limite' => '2026-10-30',
        ]);

        $email = (new RappelTacheNotification($tache))
            ->toMail($user)
            ->render()
            ->toHtml();

        $this->assertStringContainsString('Bonjour Alice', $email);
        $this->assertStringContainsString('Réviser les mathématiques', $email);
        $this->assertStringContainsString('30/10/2026', $email);
        $this->assertStringContainsString('Ouvrir mes tâches', $email);
        $this->assertStringContainsString(route('taches.index'), $email);
    }

    public function test_task_title_is_escaped_in_the_reminder_email(): void
    {
        $user = User::factory()->make();
        $tache = new Tache([
            'titre' => '<script>alert(1)</script>',
            'date_limite' => '2026-10-30',
        ]);

        $email = (new RappelTacheNotification($tache))
            ->toMail($user)
            ->render()
            ->toHtml();

        $this->assertStringContainsString('&lt;script&gt;alert(1)&lt;/script&gt;', $email);
        $this->assertStringNotContainsString('<script>alert(1)</script>', $email);
    }
}
