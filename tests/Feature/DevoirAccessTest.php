<?php

namespace Tests\Feature;

use App\Models\Devoir;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DevoirAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_a_guest_is_redirected_to_login_from_teacher_and_student_devoir_pages(): void
    {
        $this->get(route('devoirs.index'))
            ->assertRedirect(route('login'));
        $this->get(route('devoirs.etudiants.index'))
            ->assertRedirect(route('login'));
    }

    public function test_students_and_administrators_cannot_access_teacher_actions(): void
    {
        $student = User::factory()->create();
        $administrator = User::factory()->create(['role' => User::ROLE_ADMINISTRATOR]);

        $this->actingAs($student)
            ->get(route('devoirs.index'))
            ->assertForbidden();
        $this->actingAs($administrator)
            ->get(route('devoirs.create'))
            ->assertForbidden();
        $this->post(route('devoirs.store'), [
            'titre' => 'Devoir interdit',
            'consignes' => 'Aucune.',
            'date_limite' => '2026-11-06',
        ])->assertForbidden();

        $this->assertDatabaseCount('devoirs', 0);
    }

    public function test_a_teacher_cannot_view_or_change_another_teachers_devoir(): void
    {
        $teacher = User::factory()->create(['role' => User::ROLE_TEACHER]);
        $anotherTeacher = User::factory()->create(['role' => User::ROLE_TEACHER]);
        $devoir = $teacher->devoirs()->create([
            'titre' => 'Devoir privé',
            'consignes' => 'Consignes du propriétaire.',
            'date_limite' => '2026-11-06',
            'statut' => Devoir::STATUS_DRAFT,
        ]);

        $this->actingAs($anotherTeacher)
            ->get(route('devoirs.show', $devoir))
            ->assertNotFound();
        $this->get(route('devoirs.edit', $devoir))
            ->assertNotFound();
        $this->put(route('devoirs.update', $devoir), [
            'titre' => 'Titre modifié sans droit',
            'consignes' => 'Consignes modifiées.',
            'date_limite' => '2026-11-07',
        ])->assertNotFound();
        $this->post(route('devoirs.publish', $devoir))->assertNotFound();
        $this->post(route('devoirs.archive', $devoir))->assertNotFound();

        $this->assertDatabaseHas('devoirs', [
            'id' => $devoir->id,
            'enseignant_id' => $teacher->id,
            'titre' => 'Devoir privé',
            'statut' => Devoir::STATUS_DRAFT,
        ]);
    }

    public function test_a_teacher_only_sees_their_own_devoirs_and_can_see_archived_ones(): void
    {
        $teacher = User::factory()->create(['role' => User::ROLE_TEACHER]);
        $anotherTeacher = User::factory()->create(['role' => User::ROLE_TEACHER]);
        $teacher->devoirs()->create([
            'titre' => 'Mon devoir archivé',
            'consignes' => 'Anciennes consignes.',
            'date_limite' => '2026-11-06',
            'statut' => Devoir::STATUS_ARCHIVED,
        ]);
        $anotherTeacher->devoirs()->create([
            'titre' => 'Devoir d’un autre enseignant',
            'consignes' => 'Texte privé.',
            'date_limite' => '2026-11-07',
            'statut' => Devoir::STATUS_PUBLISHED,
        ]);

        $this->actingAs($teacher)
            ->get(route('devoirs.index'))
            ->assertOk()
            ->assertSee('Mon devoir archivé')
            ->assertSee('Archivé')
            ->assertDontSee('Devoir d’un autre enseignant');
    }

    public function test_students_only_see_published_devoirs_and_can_view_their_details(): void
    {
        $student = User::factory()->create();
        $teacher = User::factory()->create(['role' => User::ROLE_TEACHER]);
        $published = $teacher->devoirs()->create([
            'titre' => 'Devoir publié visible',
            'consignes' => 'Répondre aux questions.',
            'date_limite' => '2026-11-06',
            'statut' => Devoir::STATUS_PUBLISHED,
        ]);
        $teacher->devoirs()->create([
            'titre' => 'Brouillon caché',
            'consignes' => 'Texte non publié.',
            'date_limite' => '2026-11-07',
            'statut' => Devoir::STATUS_DRAFT,
        ]);
        $teacher->devoirs()->create([
            'titre' => 'Devoir archivé caché',
            'consignes' => 'Ancien texte.',
            'date_limite' => '2026-11-08',
            'statut' => Devoir::STATUS_ARCHIVED,
        ]);

        $this->actingAs($student)
            ->get(route('devoirs.etudiants.index'))
            ->assertOk()
            ->assertSee('Devoir publié visible')
            ->assertDontSee('Brouillon caché')
            ->assertDontSee('Devoir archivé caché');
        $this->get(route('devoirs.etudiants.show', $published))
            ->assertOk()
            ->assertSee('Répondre aux questions.');
    }

    public function test_students_cannot_open_a_draft_or_archived_devoir(): void
    {
        $student = User::factory()->create();
        $teacher = User::factory()->create(['role' => User::ROLE_TEACHER]);
        $draft = $teacher->devoirs()->create([
            'titre' => 'Brouillon privé',
            'consignes' => 'Ne pas afficher.',
            'date_limite' => '2026-11-06',
            'statut' => Devoir::STATUS_DRAFT,
        ]);
        $archived = $teacher->devoirs()->create([
            'titre' => 'Devoir retiré',
            'consignes' => 'Ne pas afficher.',
            'date_limite' => '2026-11-07',
            'statut' => Devoir::STATUS_ARCHIVED,
        ]);

        $this->actingAs($student)
            ->get(route('devoirs.etudiants.show', $draft))
            ->assertNotFound();
        $this->get(route('devoirs.etudiants.show', $archived))
            ->assertNotFound();
    }

    public function test_devoir_title_and_instructions_are_escaped_in_student_views(): void
    {
        $student = User::factory()->create();
        $teacher = User::factory()->create(['role' => User::ROLE_TEACHER]);
        $devoir = $teacher->devoirs()->create([
            'titre' => '<script>alert(1)</script>',
            'consignes' => '<img src=x onerror=alert(1)>',
            'date_limite' => '2026-11-06',
            'statut' => Devoir::STATUS_PUBLISHED,
        ]);

        $this->actingAs($student)
            ->get(route('devoirs.etudiants.index'))
            ->assertOk()
            ->assertSee('&lt;script&gt;alert(1)&lt;/script&gt;', false)
            ->assertDontSee('<script>alert(1)</script>', false);
        $this->get(route('devoirs.etudiants.show', $devoir))
            ->assertOk()
            ->assertSee('&lt;img src=x onerror=alert(1)&gt;', false)
            ->assertDontSee('<img src=x onerror=alert(1)>', false);
    }
}
