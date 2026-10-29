<?php

namespace Tests\Feature;

use App\Models\Rappel;
use App\Models\Tache;
use App\Models\User;
use App\Notifications\RappelTacheNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class CalendrierTest extends TestCase
{
    use RefreshDatabase;

    public function test_a_student_can_view_their_calendar_and_overdue_tasks(): void
    {
        $this->travelTo(now()->setDate(2026, 10, 23)->startOfDay());
        $student = User::factory()->create();
        $anotherStudent = User::factory()->create();

        Tache::create([
            'user_id' => $student->id,
            'titre' => 'Échéance en retard',
            'date_limite' => '2026-10-20',
            'statut' => 'a_faire',
        ]);
        Tache::create([
            'user_id' => $student->id,
            'titre' => 'Échéance à venir',
            'date_limite' => '2026-10-25',
            'statut' => 'en_cours',
        ]);
        Tache::create([
            'user_id' => $student->id,
            'titre' => 'Tâche terminée',
            'date_limite' => '2026-10-18',
            'statut' => 'terminee',
        ]);
        Tache::create([
            'user_id' => $anotherStudent->id,
            'titre' => 'Tâche privée',
            'date_limite' => '2026-10-21',
            'statut' => 'a_faire',
        ]);

        $response = $this->actingAs($student)->get(route('calendrier.index', ['mois' => '2026-10']));

        $response->assertOk()
            ->assertSee('Calendrier et rappels')
            ->assertSee('Échéance en retard')
            ->assertSee('Échéance à venir')
            ->assertSee('Tâche terminée')
            ->assertSee('En retard depuis le 20/10/2026')
            ->assertDontSee('Tâche privée');
    }

    public function test_a_guest_is_redirected_to_login_from_the_calendar(): void
    {
        $this->get(route('calendrier.index'))
            ->assertRedirect(route('login'));
    }

    public function test_a_non_student_cannot_access_the_calendar(): void
    {
        $teacher = User::factory()->create(['role' => User::ROLE_TEACHER]);

        $this->actingAs($teacher)
            ->get(route('calendrier.index'))
            ->assertForbidden();
    }

    public function test_a_student_can_schedule_one_reminder_for_their_task(): void
    {
        $this->travelTo(now()->setDate(2026, 10, 23)->startOfDay());
        $student = User::factory()->create();
        $tache = Tache::create([
            'user_id' => $student->id,
            'titre' => 'Réviser les fractions',
            'date_limite' => '2026-10-25',
            'statut' => 'a_faire',
        ]);

        $response = $this->actingAs($student)->post(route('calendrier.rappels.store'), [
            'tache_id' => $tache->id,
            'date_heure_rappel' => '2026-10-24T09:00',
        ]);

        $response->assertRedirectToRoute('calendrier.index', ['mois' => '2026-10'])
            ->assertSessionHas('success', 'Le rappel a été programmé.');
        $this->assertDatabaseHas('rappels', [
            'tache_id' => $tache->id,
            'date_heure_rappel' => '2026-10-24 09:00:00',
            'statut' => Rappel::STATUS_PENDING,
        ]);
    }

    public function test_a_student_cannot_schedule_a_reminder_for_another_users_task(): void
    {
        $student = User::factory()->create();
        $anotherStudent = User::factory()->create();
        $tache = Tache::create([
            'user_id' => $anotherStudent->id,
            'titre' => 'Tâche privée',
            'date_limite' => '2026-10-25',
            'statut' => 'a_faire',
        ]);

        $this->actingAs($student)
            ->from(route('calendrier.index'))
            ->post(route('calendrier.rappels.store'), [
                'tache_id' => $tache->id,
                'date_heure_rappel' => now()->addHour()->format('Y-m-d\TH:i'),
            ])
            ->assertRedirect(route('calendrier.index'))
            ->assertSessionHasErrors('tache_id');

        $this->assertDatabaseCount('rappels', 0);
    }

    public function test_a_reminder_cannot_be_scheduled_after_the_task_deadline_or_twice_at_the_same_time(): void
    {
        $this->travelTo(now()->setDate(2026, 10, 23)->startOfDay());
        $student = User::factory()->create();
        $tache = Tache::create([
            'user_id' => $student->id,
            'titre' => 'Préparer un exposé',
            'date_limite' => '2026-10-25',
            'statut' => 'a_faire',
        ]);

        $this->actingAs($student)->from(route('calendrier.index'))->post(route('calendrier.rappels.store'), [
            'tache_id' => $tache->id,
            'date_heure_rappel' => '2026-10-26T09:00',
        ])->assertRedirect(route('calendrier.index'))
            ->assertSessionHasErrors('date_heure_rappel');

        $this->actingAs($student)->post(route('calendrier.rappels.store'), [
            'tache_id' => $tache->id,
            'date_heure_rappel' => '2026-10-24T09:00',
        ])->assertRedirectToRoute('calendrier.index', ['mois' => '2026-10']);

        $this->from(route('calendrier.index'))->post(route('calendrier.rappels.store'), [
            'tache_id' => $tache->id,
            'date_heure_rappel' => '2026-10-24T09:00',
        ])->assertRedirect(route('calendrier.index'))
            ->assertSessionHasErrors('date_heure_rappel');

        $this->assertDatabaseCount('rappels', 1);
    }

    public function test_due_reminders_are_emailed_once_and_marked_as_sent(): void
    {
        Notification::fake();
        $student = User::factory()->create();
        $tache = Tache::create([
            'user_id' => $student->id,
            'titre' => 'Rendre le devoir',
            'date_limite' => now()->addDay()->toDateString(),
            'statut' => 'en_cours',
        ]);
        $rappel = $tache->rappels()->create([
            'date_heure_rappel' => now()->subMinute(),
            'statut' => Rappel::STATUS_PENDING,
        ]);

        $this->artisan('app:envoyer-rappels')->assertExitCode(0);

        Notification::assertSentTo($student, RappelTacheNotification::class);
        $this->assertDatabaseHas('rappels', [
            'id' => $rappel->id,
            'statut' => Rappel::STATUS_SENT,
        ]);

        $this->artisan('app:envoyer-rappels')->assertExitCode(0);

        Notification::assertSentToTimes($student, RappelTacheNotification::class, 1);
    }

    public function test_due_reminders_for_completed_tasks_are_not_sent(): void
    {
        Notification::fake();
        $student = User::factory()->create();
        $tache = Tache::create([
            'user_id' => $student->id,
            'titre' => 'Tâche déjà terminée',
            'date_limite' => now()->addDay()->toDateString(),
            'statut' => 'terminee',
        ]);
        $rappel = $tache->rappels()->create([
            'date_heure_rappel' => now()->subMinute(),
            'statut' => Rappel::STATUS_PENDING,
        ]);

        $this->artisan('app:envoyer-rappels')->assertExitCode(0);

        Notification::assertNothingSent();
        $this->assertDatabaseHas('rappels', [
            'id' => $rappel->id,
            'statut' => Rappel::STATUS_PENDING,
        ]);
    }
}
