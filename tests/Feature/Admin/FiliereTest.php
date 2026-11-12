<?php

namespace Tests\Feature\Admin;

use App\Models\Filiere;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FiliereTest extends TestCase
{
    use RefreshDatabase;

    public function test_only_an_administrator_can_manage_filieres(): void
    {
        $student = User::factory()->create();

        $this->actingAs($student)
            ->get(route('admin.filieres.index'))
            ->assertForbidden();

        $this->post(route('admin.filieres.store'), ['nom' => 'Sciences'])
            ->assertForbidden();

        $this->assertDatabaseCount('filieres', 0);
    }

    public function test_an_administrator_can_view_filieres_classes_and_usage_counts(): void
    {
        $administrator = User::factory()->create(['role' => User::ROLE_ADMINISTRATOR]);
        $filiere = Filiere::query()->create(['nom' => 'Sciences']);
        $classe = $filiere->classes()->create(['nom' => 'Terminale A']);
        User::factory()->create(['classe_id' => $classe->id]);

        $this->actingAs($administrator)
            ->get(route('admin.filieres.index'))
            ->assertOk()
            ->assertSee('Gestion des classes et filières')
            ->assertSee('Sciences')
            ->assertSee('Terminale A')
            ->assertSee('1 étudiant(s) · 0 devoir(s)');
    }

    public function test_an_administrator_can_create_and_rename_a_filiere(): void
    {
        $administrator = User::factory()->create(['role' => User::ROLE_ADMINISTRATOR]);

        $this->actingAs($administrator)
            ->post(route('admin.filieres.store'), ['nom' => 'Sciences'])
            ->assertRedirectToRoute('admin.filieres.index')
            ->assertSessionHas('success', 'La filière a été créée.');

        $filiere = Filiere::query()->sole();

        $this->put(route('admin.filieres.update', $filiere), ['nom' => 'Sciences exactes'])
            ->assertRedirectToRoute('admin.filieres.index')
            ->assertSessionHas('success', 'La filière a été modifiée.');

        $this->assertDatabaseHas('filieres', [
            'id' => $filiere->id,
            'nom' => 'Sciences exactes',
        ]);
    }

    public function test_a_filiere_name_must_be_unique(): void
    {
        $administrator = User::factory()->create(['role' => User::ROLE_ADMINISTRATOR]);
        Filiere::query()->create(['nom' => 'Sciences']);

        $this->actingAs($administrator)
            ->from(route('admin.filieres.index'))
            ->post(route('admin.filieres.store'), ['nom' => 'Sciences'])
            ->assertRedirect(route('admin.filieres.index'))
            ->assertSessionHasErrors('nom');

        $this->assertDatabaseCount('filieres', 1);
    }
}
