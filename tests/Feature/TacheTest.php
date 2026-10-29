<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TacheTest extends TestCase
{
    use RefreshDatabase;

    public function test_an_authenticated_student_can_open_the_task_creation_form(): void
    {
        $student = User::factory()->create();

        $response = $this->actingAs($student)->get(route('taches.create'));

        $response->assertOk()
            ->assertSee('Nouvelle tâche')
            ->assertSee('name="titre"', false)
            ->assertSee('name="date_limite"', false);
    }

    public function test_a_guest_is_redirected_to_login_from_the_task_creation_form(): void
    {
        $this->get(route('taches.create'))
            ->assertRedirect(route('login'));
    }

    public function test_an_authenticated_student_can_create_a_task_for_their_own_account(): void
    {
        $student = User::factory()->create();
        $anotherStudent = User::factory()->create();

        $response = $this->actingAs($student)->post(route('taches.store'), [
            'titre' => 'Réviser les mathématiques',
            'description' => 'Revoir les exercices du chapitre 2.',
            'date_limite' => '2026-10-30',
            'priorite' => 'elevee',
            'statut' => 'en_cours',
            'user_id' => $anotherStudent->id,
        ]);

        $response->assertRedirectToRoute('taches.index')
            ->assertSessionHas('success', 'La tâche a été créée avec succès.');

        $this->assertDatabaseHas('taches', [
            'user_id' => $student->id,
            'titre' => 'Réviser les mathématiques',
            'description' => 'Revoir les exercices du chapitre 2.',
            'date_limite' => '2026-10-30',
            'priorite' => 'elevee',
            'statut' => 'en_cours',
        ]);
        $this->assertDatabaseMissing('taches', [
            'user_id' => $anotherStudent->id,
            'titre' => 'Réviser les mathématiques',
        ]);
    }

    public function test_a_student_can_create_a_task_without_optional_description_or_deadline(): void
    {
        $student = User::factory()->create();

        $response = $this->actingAs($student)->post(route('taches.store'), [
            'titre' => 'Lire le chapitre 3',
            'priorite' => 'moyenne',
            'statut' => 'a_faire',
        ]);

        $response->assertRedirectToRoute('taches.index');
        $this->assertDatabaseHas('taches', [
            'user_id' => $student->id,
            'titre' => 'Lire le chapitre 3',
            'description' => null,
            'date_limite' => null,
        ]);
    }

    public function test_invalid_task_data_is_rejected_without_creating_a_task(): void
    {
        $student = User::factory()->create();

        $response = $this->actingAs($student)
            ->from(route('taches.create'))
            ->followingRedirects()
            ->post(route('taches.store'), [
                'titre' => '',
                'date_limite' => 'pas-une-date',
                'priorite' => 'urgente',
                'statut' => 'inconnu',
            ]);

        $response->assertOk()
            ->assertSee('Le champ titre est obligatoire.')
            ->assertSee('aria-describedby=', false)
            ->assertSee('titre-error')
            ->assertSee('id="titre-error"', false);

        $this->assertDatabaseCount('taches', 0);
    }

    public function test_a_student_only_sees_their_own_tasks(): void
    {
        $student = User::factory()->create();
        $anotherStudent = User::factory()->create();

        $this->actingAs($student)->post(route('taches.store'), [
            'titre' => 'Ma tâche privée',
            'priorite' => 'moyenne',
            'statut' => 'a_faire',
        ]);

        $this->actingAs($anotherStudent)->post(route('taches.store'), [
            'titre' => 'Tâche d’un autre étudiant',
            'priorite' => 'moyenne',
            'statut' => 'a_faire',
        ]);

        $response = $this->actingAs($student)->get(route('taches.index'));

        $response->assertOk()
            ->assertSee('Ma tâche privée')
            ->assertDontSee('Tâche d’un autre étudiant');
    }

    public function test_task_content_is_escaped_in_the_task_list(): void
    {
        $student = User::factory()->create();

        $this->actingAs($student)->post(route('taches.store'), [
            'titre' => '<script>alert(1)</script>',
            'priorite' => 'moyenne',
            'statut' => 'a_faire',
        ]);

        $response = $this->get(route('taches.index'));

        $response->assertSee('&lt;script&gt;alert(1)&lt;/script&gt;', false)
            ->assertDontSee('<script>alert(1)</script>', false);
    }

    public function test_a_non_student_cannot_open_the_task_creation_form(): void
    {
        $teacher = User::factory()->create(['role' => User::ROLE_TEACHER]);

        $this->actingAs($teacher)
            ->get(route('taches.create'))
            ->assertForbidden();
    }
}
