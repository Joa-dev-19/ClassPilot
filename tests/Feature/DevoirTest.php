<?php

namespace Tests\Feature;

use App\Models\Devoir;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DevoirTest extends TestCase
{
    use RefreshDatabase;

    public function test_a_teacher_can_open_the_draft_creation_form(): void
    {
        $teacher = User::factory()->create(['role' => User::ROLE_TEACHER]);

        $this->actingAs($teacher)
            ->get(route('devoirs.create'))
            ->assertOk()
            ->assertSee('Créer un devoir')
            ->assertSee('name="titre"', false)
            ->assertSee('name="consignes"', false)
            ->assertSee('name="date_limite"', false);
    }

    public function test_a_teacher_can_create_a_draft_owned_by_the_authenticated_teacher(): void
    {
        $this->travelTo('2026-10-30 10:00:00');
        $teacher = User::factory()->create(['role' => User::ROLE_TEACHER]);
        $anotherTeacher = User::factory()->create(['role' => User::ROLE_TEACHER]);

        $response = $this->actingAs($teacher)->post(route('devoirs.store'), [
            'enseignant_id' => $anotherTeacher->id,
            'titre' => 'Exercices sur les fractions',
            'consignes' => 'Résoudre les exercices 1 à 5.',
            'date_limite' => '2026-11-06',
            'statut' => Devoir::STATUS_PUBLISHED,
        ]);
        $devoir = Devoir::query()->sole();

        $response->assertRedirectToRoute('devoirs.show', $devoir)
            ->assertSessionHas('success', 'Le devoir a été enregistré en brouillon.');
        $this->assertDatabaseHas('devoirs', [
            'id' => $devoir->id,
            'enseignant_id' => $teacher->id,
            'titre' => 'Exercices sur les fractions',
            'statut' => Devoir::STATUS_DRAFT,
        ]);
    }

    public function test_invalid_or_past_devoir_data_is_rejected_without_persistence(): void
    {
        $this->travelTo('2026-10-30 10:00:00');
        $teacher = User::factory()->create(['role' => User::ROLE_TEACHER]);

        $this->actingAs($teacher)
            ->from(route('devoirs.create'))
            ->post(route('devoirs.store'), [
                'titre' => '',
                'date_limite' => '2026-10-29',
            ])
            ->assertRedirect(route('devoirs.create'))
            ->assertSessionHasErrors(['titre', 'date_limite']);

        $this->assertDatabaseCount('devoirs', 0);
    }

    public function test_a_draft_without_instructions_cannot_be_published(): void
    {
        $this->travelTo('2026-10-30 10:00:00');
        $teacher = User::factory()->create(['role' => User::ROLE_TEACHER]);
        $devoir = $teacher->devoirs()->create([
            'titre' => 'Devoir sans consignes',
            'date_limite' => '2026-11-06',
            'statut' => Devoir::STATUS_DRAFT,
        ]);

        $this->actingAs($teacher)
            ->from(route('devoirs.show', $devoir))
            ->post(route('devoirs.publish', $devoir))
            ->assertRedirect(route('devoirs.show', $devoir))
            ->assertSessionHasErrors('consignes');

        $this->assertDatabaseHas('devoirs', [
            'id' => $devoir->id,
            'statut' => Devoir::STATUS_DRAFT,
        ]);
    }

    public function test_a_teacher_can_publish_a_draft_for_all_students(): void
    {
        $this->travelTo('2026-10-30 10:00:00');
        $teacher = User::factory()->create(['role' => User::ROLE_TEACHER]);
        $student = User::factory()->create();
        $devoir = $teacher->devoirs()->create([
            'titre' => 'Lire le chapitre 4',
            'consignes' => 'Lire et résumer le chapitre.',
            'date_limite' => '2026-11-06',
            'statut' => Devoir::STATUS_DRAFT,
        ]);

        $this->actingAs($teacher)
            ->post(route('devoirs.publish', $devoir))
            ->assertRedirectToRoute('devoirs.show', $devoir)
            ->assertSessionHas('success', 'Le devoir a été publié pour tous les étudiants.');

        $this->assertDatabaseHas('devoirs', [
            'id' => $devoir->id,
            'statut' => Devoir::STATUS_PUBLISHED,
        ]);
        $this->actingAs($student)
            ->get(route('devoirs.etudiants.show', $devoir))
            ->assertOk()
            ->assertSee('Lire le chapitre 4');
    }

    public function test_a_teacher_cannot_publish_a_draft_with_a_past_deadline(): void
    {
        $this->travelTo('2026-10-30 10:00:00');
        $teacher = User::factory()->create(['role' => User::ROLE_TEACHER]);
        $devoir = $teacher->devoirs()->create([
            'titre' => 'Devoir arrivé à échéance',
            'consignes' => 'Terminer le travail.',
            'date_limite' => '2026-10-29',
            'statut' => Devoir::STATUS_DRAFT,
        ]);

        $this->actingAs($teacher)
            ->from(route('devoirs.show', $devoir))
            ->post(route('devoirs.publish', $devoir))
            ->assertRedirect(route('devoirs.show', $devoir))
            ->assertSessionHasErrors('date_limite');

        $this->assertDatabaseHas('devoirs', [
            'id' => $devoir->id,
            'statut' => Devoir::STATUS_DRAFT,
        ]);
    }

    public function test_a_teacher_can_edit_a_published_devoir_without_changing_its_status(): void
    {
        $this->travelTo('2026-10-30 10:00:00');
        $teacher = User::factory()->create(['role' => User::ROLE_TEACHER]);
        $devoir = $teacher->devoirs()->create([
            'titre' => 'Ancien titre',
            'consignes' => 'Anciennes consignes.',
            'date_limite' => '2026-11-06',
            'statut' => Devoir::STATUS_PUBLISHED,
        ]);

        $this->actingAs($teacher)
            ->put(route('devoirs.update', $devoir), [
                'titre' => 'Titre corrigé',
                'consignes' => 'Consignes corrigées.',
                'date_limite' => '2026-11-08',
                'enseignant_id' => 999,
                'statut' => Devoir::STATUS_ARCHIVED,
            ])
            ->assertRedirectToRoute('devoirs.show', $devoir)
            ->assertSessionHas('success', 'Le devoir a été modifié.');

        $this->assertDatabaseHas('devoirs', [
            'id' => $devoir->id,
            'enseignant_id' => $teacher->id,
            'titre' => 'Titre corrigé',
            'consignes' => 'Consignes corrigées.',
            'date_limite' => '2026-11-08 00:00:00',
            'statut' => Devoir::STATUS_PUBLISHED,
        ]);
    }

    public function test_a_published_devoir_cannot_be_assigned_a_new_past_deadline(): void
    {
        $this->travelTo('2026-11-10 10:00:00');
        $teacher = User::factory()->create(['role' => User::ROLE_TEACHER]);
        $devoir = $teacher->devoirs()->create([
            'titre' => 'Devoir publié',
            'consignes' => 'Consignes.',
            'date_limite' => '2026-11-06',
            'statut' => Devoir::STATUS_PUBLISHED,
        ]);

        $this->actingAs($teacher)
            ->from(route('devoirs.edit', $devoir))
            ->put(route('devoirs.update', $devoir), [
                'titre' => 'Devoir publié',
                'consignes' => 'Consignes.',
                'date_limite' => '2026-11-05',
            ])
            ->assertRedirect(route('devoirs.edit', $devoir))
            ->assertSessionHasErrors('date_limite');

        $this->assertDatabaseHas('devoirs', [
            'id' => $devoir->id,
            'date_limite' => '2026-11-06 00:00:00',
        ]);
    }

    public function test_a_published_devoir_can_keep_its_past_deadline_while_being_edited(): void
    {
        $this->travelTo('2026-11-10 10:00:00');
        $teacher = User::factory()->create(['role' => User::ROLE_TEACHER]);
        $devoir = $teacher->devoirs()->create([
            'titre' => 'Devoir arrivé à échéance',
            'consignes' => 'Consignes initiales.',
            'date_limite' => '2026-11-06',
            'statut' => Devoir::STATUS_PUBLISHED,
        ]);

        $this->actingAs($teacher)
            ->put(route('devoirs.update', $devoir), [
                'titre' => 'Devoir corrigé',
                'consignes' => 'Consignes corrigées.',
                'date_limite' => '2026-11-06',
            ])
            ->assertRedirectToRoute('devoirs.show', $devoir);

        $this->assertDatabaseHas('devoirs', [
            'id' => $devoir->id,
            'titre' => 'Devoir corrigé',
            'date_limite' => '2026-11-06 00:00:00',
            'statut' => Devoir::STATUS_PUBLISHED,
        ]);
    }

    public function test_a_teacher_cannot_remove_instructions_from_a_published_devoir(): void
    {
        $teacher = User::factory()->create(['role' => User::ROLE_TEACHER]);
        $devoir = $teacher->devoirs()->create([
            'titre' => 'Devoir publié',
            'consignes' => 'Consignes conservées.',
            'date_limite' => '2026-11-06',
            'statut' => Devoir::STATUS_PUBLISHED,
        ]);

        $this->actingAs($teacher)
            ->from(route('devoirs.edit', $devoir))
            ->put(route('devoirs.update', $devoir), [
                'titre' => 'Devoir publié',
                'consignes' => '',
                'date_limite' => '2026-11-06',
            ])
            ->assertRedirect(route('devoirs.edit', $devoir))
            ->assertSessionHasErrors('consignes');

        $this->assertDatabaseHas('devoirs', [
            'id' => $devoir->id,
            'consignes' => 'Consignes conservées.',
        ]);
    }

    public function test_a_teacher_can_archive_a_devoir_without_deleting_it(): void
    {
        $teacher = User::factory()->create(['role' => User::ROLE_TEACHER]);
        $student = User::factory()->create();
        $devoir = $teacher->devoirs()->create([
            'titre' => 'Devoir à archiver',
            'consignes' => 'Consignes conservées.',
            'date_limite' => '2026-11-06',
            'statut' => Devoir::STATUS_PUBLISHED,
        ]);

        $this->actingAs($teacher)
            ->post(route('devoirs.archive', $devoir))
            ->assertRedirectToRoute('devoirs.index')
            ->assertSessionHas('success', 'Le devoir a été archivé.');

        $this->assertModelExists($devoir->refresh());
        $this->assertDatabaseHas('devoirs', [
            'id' => $devoir->id,
            'statut' => Devoir::STATUS_ARCHIVED,
        ]);
        $this->actingAs($teacher)
            ->get(route('devoirs.edit', $devoir))
            ->assertNotFound();
        $this->post(route('devoirs.publish', $devoir))
            ->assertNotFound();
        $this->actingAs($student)
            ->get(route('devoirs.etudiants.show', $devoir))
            ->assertNotFound();
        $this->actingAs($teacher)
            ->get(route('devoirs.show', $devoir))
            ->assertOk()
            ->assertSee('Archivé');
    }

    public function test_published_devoir_deadlines_are_displayed_in_the_student_calendar(): void
    {
        $this->travelTo('2026-10-30 10:00:00');
        $student = User::factory()->create();
        $teacher = User::factory()->create(['role' => User::ROLE_TEACHER]);
        $teacher->devoirs()->create([
            'titre' => 'Devoir visible au calendrier',
            'consignes' => 'Consignes.',
            'date_limite' => '2026-10-31',
            'statut' => Devoir::STATUS_PUBLISHED,
        ]);
        $teacher->devoirs()->create([
            'titre' => 'Brouillon invisible au calendrier',
            'consignes' => 'Consignes.',
            'date_limite' => '2026-10-31',
            'statut' => Devoir::STATUS_DRAFT,
        ]);

        $this->actingAs($student)
            ->get(route('calendrier.index', ['mois' => '2026-10']))
            ->assertOk()
            ->assertSee('Devoir visible au calendrier')
            ->assertDontSee('Brouillon invisible au calendrier');
    }
}
