<?php

namespace Tests\Feature\Admin;

use App\Models\Filiere;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ClasseTest extends TestCase
{
    use RefreshDatabase;

    public function test_an_administrator_can_create_and_rename_a_class_in_a_filiere(): void
    {
        $administrator = User::factory()->create(['role' => User::ROLE_ADMINISTRATOR]);
        $filiere = Filiere::query()->create(['nom' => 'Lettres']);

        $this->actingAs($administrator)
            ->post(route('admin.classes.store', $filiere), ['nom' => 'Première A'])
            ->assertRedirectToRoute('admin.filieres.index')
            ->assertSessionHas('success', 'La classe a été créée.');

        $classe = $filiere->classes()->sole();

        $this->put(route('admin.classes.update', [$filiere, $classe]), ['nom' => 'Terminale A'])
            ->assertRedirectToRoute('admin.filieres.index')
            ->assertSessionHas('success', 'La classe a été modifiée.');

        $this->assertDatabaseHas('classes', [
            'id' => $classe->id,
            'filiere_id' => $filiere->id,
            'nom' => 'Terminale A',
        ]);
    }

    public function test_a_class_name_must_be_unique_within_its_filiere(): void
    {
        $administrator = User::factory()->create(['role' => User::ROLE_ADMINISTRATOR]);
        $filiere = Filiere::query()->create(['nom' => 'Sciences']);
        $filiere->classes()->create(['nom' => 'Terminale A']);

        $this->actingAs($administrator)
            ->from(route('admin.filieres.index'))
            ->post(route('admin.classes.store', $filiere), ['nom' => 'Terminale A'])
            ->assertRedirect(route('admin.filieres.index'))
            ->assertSessionHasErrors('nom');

        $this->assertDatabaseCount('classes', 1);
    }

    public function test_a_class_cannot_be_renamed_through_another_filiere_route(): void
    {
        $administrator = User::factory()->create(['role' => User::ROLE_ADMINISTRATOR]);
        $filiere = Filiere::query()->create(['nom' => 'Sciences']);
        $anotherFiliere = Filiere::query()->create(['nom' => 'Lettres']);
        $classe = $filiere->classes()->create(['nom' => 'Terminale A']);

        $this->actingAs($administrator)
            ->put(route('admin.classes.update', [$anotherFiliere, $classe]), ['nom' => 'Terminale B'])
            ->assertNotFound();

        $this->assertDatabaseHas('classes', [
            'id' => $classe->id,
            'filiere_id' => $filiere->id,
            'nom' => 'Terminale A',
        ]);
    }
}
