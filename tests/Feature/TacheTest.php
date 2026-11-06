<?php

namespace Tests\Feature;

use App\Models\Rappel;
use App\Models\Tache;
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
            ->assertSee('Modifier')
            ->assertSee('Supprimer')
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

        $response->assertOk()
            ->assertSee('&lt;script&gt;alert(1)&lt;/script&gt;', false)
            ->assertDontSee('<script>alert(1)</script>', false);
    }

    public function test_a_non_student_cannot_open_the_task_creation_form(): void
    {
        $teacher = User::factory()->create(['role' => User::ROLE_TEACHER]);

        $this->actingAs($teacher)
            ->get(route('taches.create'))
            ->assertForbidden();
    }

    public function test_a_student_can_edit_and_update_their_task(): void
    {
        $student = User::factory()->create();
        $anotherStudent = User::factory()->create();
        $tache = Tache::create([
            'user_id' => $student->id,
            'titre' => 'Ancien titre',
            'description' => 'Ancienne description',
            'date_limite' => '2026-11-02',
            'priorite' => 'faible',
            'statut' => 'a_faire',
        ]);

        $this->actingAs($student)
            ->get(route('taches.edit', $tache))
            ->assertOk()
            ->assertSee('Modifier la tâche')
            ->assertSee('Ancien titre');

        $response = $this->put(route('taches.update', $tache), [
            'titre' => 'Nouveau titre',
            'description' => 'Description mise à jour',
            'date_limite' => '2026-11-05',
            'priorite' => 'elevee',
            'statut' => 'terminee',
            'user_id' => $anotherStudent->id,
        ]);

        $response->assertRedirectToRoute('taches.index')
            ->assertSessionHas('success', 'La tâche a été mise à jour avec succès.');
        $this->assertDatabaseHas('taches', [
            'id' => $tache->id,
            'user_id' => $student->id,
            'titre' => 'Nouveau titre',
            'description' => 'Description mise à jour',
            'date_limite' => '2026-11-05',
            'priorite' => 'elevee',
            'statut' => 'terminee',
        ]);
    }

    public function test_invalid_task_updates_are_rejected_without_changing_the_task(): void
    {
        $student = User::factory()->create();
        $tache = Tache::create([
            'user_id' => $student->id,
            'titre' => 'Titre conservé',
            'priorite' => 'moyenne',
            'statut' => 'a_faire',
        ]);

        $this->actingAs($student)
            ->from(route('taches.edit', $tache))
            ->put(route('taches.update', $tache), [
                'titre' => '',
                'priorite' => 'urgente',
                'statut' => 'inconnu',
            ])
            ->assertRedirect(route('taches.edit', $tache))
            ->assertSessionHasErrors(['titre', 'priorite', 'statut']);

        $this->assertDatabaseHas('taches', [
            'id' => $tache->id,
            'titre' => 'Titre conservé',
            'priorite' => 'moyenne',
            'statut' => 'a_faire',
        ]);
    }

    public function test_a_student_can_delete_their_task_and_its_reminders(): void
    {
        $student = User::factory()->create();
        $tache = Tache::create([
            'user_id' => $student->id,
            'titre' => 'Tâche à supprimer',
            'priorite' => 'moyenne',
            'statut' => 'a_faire',
        ]);
        $rappel = Rappel::create([
            'tache_id' => $tache->id,
            'date_heure_rappel' => '2026-10-31 09:00:00',
        ]);

        $response = $this->actingAs($student)->delete(route('taches.destroy', $tache));

        $response->assertRedirectToRoute('taches.index')
            ->assertSessionHas('success', 'La tâche a été supprimée avec succès.');
        $this->assertDatabaseMissing('taches', ['id' => $tache->id]);
        $this->assertDatabaseMissing('rappels', ['id' => $rappel->id]);
    }

    public function test_a_student_cannot_edit_update_or_delete_another_students_task(): void
    {
        $student = User::factory()->create();
        $anotherStudent = User::factory()->create();
        $tache = Tache::create([
            'user_id' => $anotherStudent->id,
            'titre' => 'Tâche privée',
            'priorite' => 'moyenne',
            'statut' => 'a_faire',
        ]);

        $this->actingAs($student)
            ->get(route('taches.edit', $tache))
            ->assertNotFound();

        $this->put(route('taches.update', $tache), [
            'titre' => 'Titre falsifié',
            'priorite' => 'elevee',
            'statut' => 'terminee',
        ])->assertNotFound();

        $this->delete(route('taches.destroy', $tache))
            ->assertNotFound();

        $this->assertDatabaseHas('taches', [
            'id' => $tache->id,
            'user_id' => $anotherStudent->id,
            'titre' => 'Tâche privée',
            'statut' => 'a_faire',
        ]);
    }
}
