<?php

namespace Tests\Feature;

use App\Models\Rappel;
use App\Models\Tache;
use App\Models\User;
use App\Notifications\RappelTacheNotification;
use Illuminate\Contracts\Notifications\Dispatcher;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Mockery;
use RuntimeException;
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
            'user_id' => $student->id,
            'titre' => 'Tâche sans date limite',
            'statut' => 'a_faire',
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
            ->assertDontSee('Tâche sans date limite')
            ->assertDontSee('Tâche privée');
    }

    public function test_a_guest_is_redirected_to_login_from_the_calendar(): void
    {
        $this->get(route('calendrier.index'))
            ->assertRedirect(route('login'));
    }

    public function test_reminder_times_use_the_paris_timezone(): void
    {
        $this->assertSame('Europe/Paris', config('app.timezone'));
    }

    public function test_a_non_student_cannot_access_the_calendar(): void
    {
        $teacher = User::factory()->create(['role' => User::ROLE_TEACHER]);

        $this->actingAs($teacher)
            ->get(route('calendrier.index'))
            ->assertForbidden();
    }

    public function test_a_calendar_month_must_use_the_expected_format(): void
    {
        $student = User::factory()->create();

        $this->actingAs($student)
            ->from(route('calendrier.index'))
            ->get(route('calendrier.index', ['mois' => '2026-13']))
            ->assertRedirect(route('calendrier.index'))
            ->assertSessionHasErrors('mois');
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
            'tentatives' => 0,
        ]);
    }

    public function test_reminder_form_minimum_is_after_the_current_minute(): void
    {
        $this->travelTo('2026-10-23 09:00:30');
        $student = User::factory()->create();
        Tache::create([
            'user_id' => $student->id,
            'titre' => 'Tâche à rappeler',
            'date_limite' => '2026-10-25',
            'statut' => 'a_faire',
        ]);

        $this->actingAs($student)
            ->get(route('calendrier.index', ['mois' => '2026-10']))
            ->assertOk()
            ->assertSee('min="2026-10-23T09:01"', false);
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

    public function test_a_student_cannot_schedule_a_reminder_for_a_task_without_a_deadline(): void
    {
        $student = User::factory()->create();
        $tache = Tache::create([
            'user_id' => $student->id,
            'titre' => 'Tâche sans échéance',
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

    public function test_a_student_cannot_cancel_another_students_reminder(): void
    {
        $student = User::factory()->create();
        $anotherStudent = User::factory()->create();
        $tache = Tache::create([
            'user_id' => $anotherStudent->id,
            'titre' => 'Tâche privée',
            'date_limite' => now()->addDay()->toDateString(),
            'statut' => 'a_faire',
        ]);
        $rappel = $tache->rappels()->create([
            'date_heure_rappel' => now()->addHour(),
            'statut' => Rappel::STATUS_PENDING,
        ]);

        $this->actingAs($student)
            ->delete(route('calendrier.rappels.destroy', $rappel))
            ->assertNotFound();

        $this->assertDatabaseHas('rappels', [
            'id' => $rappel->id,
            'tache_id' => $tache->id,
            'statut' => Rappel::STATUS_PENDING,
        ]);
    }

    public function test_a_student_can_cancel_their_pending_reminder(): void
    {
        $student = User::factory()->create();
        $dateLimite = now()->addDay();
        $tache = Tache::create([
            'user_id' => $student->id,
            'titre' => 'Tâche avec un rappel',
            'date_limite' => $dateLimite->toDateString(),
            'statut' => 'a_faire',
        ]);
        $rappel = $tache->rappels()->create([
            'date_heure_rappel' => now()->addHour(),
            'statut' => Rappel::STATUS_PENDING,
        ]);

        $this->actingAs($student)
            ->delete(route('calendrier.rappels.destroy', $rappel))
            ->assertRedirectToRoute('calendrier.index', ['mois' => $dateLimite->format('Y-m')])
            ->assertSessionHas('success', 'Le rappel a été annulé.');

        $this->assertDatabaseMissing('rappels', ['id' => $rappel->id]);
    }

    public function test_a_student_cannot_cancel_a_sent_reminder(): void
    {
        $student = User::factory()->create();
        $tache = Tache::create([
            'user_id' => $student->id,
            'titre' => 'Tâche avec un rappel envoyé',
            'date_limite' => now()->addDay()->toDateString(),
            'statut' => 'a_faire',
        ]);
        $rappel = $tache->rappels()->create([
            'date_heure_rappel' => now()->subMinute(),
            'statut' => Rappel::STATUS_SENT,
        ]);

        $this->actingAs($student)
            ->delete(route('calendrier.rappels.destroy', $rappel))
            ->assertNotFound();

        $this->assertDatabaseHas('rappels', [
            'id' => $rappel->id,
            'statut' => Rappel::STATUS_SENT,
        ]);
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

    public function test_future_reminders_are_not_sent_or_counted_as_attempts(): void
    {
        Notification::fake();
        $student = User::factory()->create();
        $tache = Tache::create([
            'user_id' => $student->id,
            'titre' => 'Réviser le cours',
            'date_limite' => now()->addDay()->toDateString(),
            'statut' => 'en_cours',
        ]);
        $rappel = $tache->rappels()->create([
            'date_heure_rappel' => now()->addMinutes(5),
            'statut' => Rappel::STATUS_PENDING,
        ]);

        $this->artisan('app:envoyer-rappels')->assertExitCode(0);

        Notification::assertNothingSent();
        $this->assertDatabaseHas('rappels', [
            'id' => $rappel->id,
            'statut' => Rappel::STATUS_PENDING,
            'tentatives' => 0,
        ]);
    }

    public function test_failed_reminders_are_retried_three_times_then_marked_failed(): void
    {
        $this->travelTo(now()->startOfMinute());
        $student = User::factory()->create();
        $tache = Tache::create([
            'user_id' => $student->id,
            'titre' => 'Envoyer le devoir',
            'date_limite' => now()->addDay()->toDateString(),
            'statut' => 'a_faire',
        ]);
        $rappel = $tache->rappels()->create([
            'date_heure_rappel' => now()->subMinute(),
            'statut' => Rappel::STATUS_PENDING,
        ]);
        $dispatcher = Mockery::mock(Dispatcher::class);
        $dispatcher->shouldReceive('send')
            ->times(3)
            ->andThrow(new RuntimeException('SMTP password must not be logged'));
        $this->app->instance(Dispatcher::class, $dispatcher);
        Log::shouldReceive('warning')
            ->times(3)
            ->with('Échec de l’envoi d’un rappel.', Mockery::on(
                fn (array $context): bool => $context['rappel_id'] === $rappel->id
                    && isset($context['tentative'], $context['exception'])
                    && ! isset($context['message'])
            ));

        $this->artisan('app:envoyer-rappels')->assertExitCode(1);
        $this->assertDatabaseHas('rappels', [
            'id' => $rappel->id,
            'statut' => Rappel::STATUS_PENDING,
            'tentatives' => 1,
        ]);

        $this->artisan('app:envoyer-rappels')->assertExitCode(1);
        $this->artisan('app:envoyer-rappels')->assertExitCode(1);
        $this->assertDatabaseHas('rappels', [
            'id' => $rappel->id,
            'statut' => Rappel::STATUS_FAILED,
            'tentatives' => 3,
        ]);

        $this->artisan('app:envoyer-rappels')->assertExitCode(0);
        $this->assertDatabaseHas('rappels', [
            'id' => $rappel->id,
            'statut' => Rappel::STATUS_FAILED,
            'tentatives' => 3,
        ]);
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
            'tentatives' => 0,
        ]);
    }

    public function test_calendar_displays_a_failed_reminder_status(): void
    {
        $student = User::factory()->create();
        $tache = Tache::create([
            'user_id' => $student->id,
            'titre' => 'Tâche avec un rappel en échec',
            'date_limite' => '2026-10-30',
            'statut' => 'a_faire',
        ]);
        $rappel = $tache->rappels()->create([
            'date_heure_rappel' => '2026-10-29 09:00:00',
            'statut' => Rappel::STATUS_FAILED,
        ]);
        $rappel->tentatives = 3;
        $rappel->save();

        $this->actingAs($student)
            ->get(route('calendrier.index', ['mois' => '2026-10']))
            ->assertOk()
            ->assertSee('Échec après 3 tentatives');
    }
}
