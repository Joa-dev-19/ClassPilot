<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class UtilisateurTest extends TestCase
{
    use RefreshDatabase;

    public static function nonAdministratorRoles(): array
    {
        return [
            'student' => [User::ROLE_STUDENT],
            'teacher' => [User::ROLE_TEACHER],
        ];
    }

    public static function searchQueries(): array
    {
        return [
            'name' => ['Amina Traore'],
            'email' => ['amina@example.test'],
        ];
    }

    public function test_a_guest_is_redirected_to_login_from_the_admin_account_list(): void
    {
        $this->get(route('admin.utilisateurs.index'))
            ->assertRedirect(route('login'));
    }

    #[DataProvider('nonAdministratorRoles')]
    public function test_users_without_the_administrator_role_cannot_open_the_account_list(string $role): void
    {
        $user = User::factory()->create(['role' => $role]);

        $this->actingAs($user)
            ->get(route('admin.utilisateurs.index'))
            ->assertForbidden();
    }

    #[DataProvider('nonAdministratorRoles')]
    public function test_users_without_the_administrator_role_cannot_change_account_status(string $role): void
    {
        $user = User::factory()->create(['role' => $role]);
        $target = User::factory()->create();

        $this->actingAs($user)
            ->patch(route('admin.utilisateurs.deactivate', $target))
            ->assertForbidden();

        $this->assertDatabaseHas('users', [
            'id' => $target->id,
            'statut' => User::STATUS_ACTIVE,
        ]);
    }

    #[DataProvider('searchQueries')]
    public function test_an_administrator_can_find_an_account_by_name_or_email(string $search): void
    {
        $administrator = User::factory()->create(['role' => User::ROLE_ADMINISTRATOR]);
        User::factory()->create([
            'name' => 'Amina Traore',
            'email' => 'amina@example.test',
        ]);
        User::factory()->create([
            'name' => 'Moussa Diallo',
            'email' => 'moussa@example.test',
        ]);

        $this->actingAs($administrator)
            ->get(route('admin.utilisateurs.index', ['q' => $search]))
            ->assertOk()
            ->assertSee('Amina Traore')
            ->assertSee('amina@example.test')
            ->assertDontSee('Moussa Diallo')
            ->assertDontSee('moussa@example.test');
    }

    public function test_search_results_are_paginated_and_keep_the_search_query(): void
    {
        $administrator = User::factory()->create(['role' => User::ROLE_ADMINISTRATOR]);
        User::factory()
            ->count(16)
            ->sequence(fn (Sequence $sequence): array => [
                'name' => sprintf('Etudiant %02d', $sequence->index + 1),
                'email' => sprintf('etudiant%02d@example.test', $sequence->index + 1),
            ])
            ->create();

        $this->actingAs($administrator)
            ->get(route('admin.utilisateurs.index', ['q' => 'Etudiant']))
            ->assertOk()
            ->assertSee('Etudiant 01')
            ->assertDontSee('Etudiant 16')
            ->assertViewHas('users', function ($users): bool {
                return $users->count() === 15
                    && $users->total() === 16
                    && str_contains($users->url(2), 'q=Etudiant');
            });
    }

    public function test_an_administrator_sees_an_empty_state_when_the_search_has_no_match(): void
    {
        $administrator = User::factory()->create(['role' => User::ROLE_ADMINISTRATOR]);

        $this->actingAs($administrator)
            ->get(route('admin.utilisateurs.index', ['q' => 'introuvable@example.test']))
            ->assertOk()
            ->assertSee('Aucun compte ne correspond à cette recherche.');
    }

    public function test_account_details_do_not_expose_passwords_or_remember_tokens(): void
    {
        $administrator = User::factory()->create(['role' => User::ROLE_ADMINISTRATOR]);
        $target = User::factory()->create([
            'name' => 'Compte à consulter',
            'email' => 'compte@example.test',
        ]);

        $passwordHash = $target->password;
        $rememberToken = $target->remember_token;

        $this->actingAs($administrator)
            ->get(route('admin.utilisateurs.show', $target))
            ->assertOk()
            ->assertSee('Compte à consulter')
            ->assertSee('compte@example.test')
            ->assertSee('etudiant')
            ->assertDontSee($passwordHash)
            ->assertDontSee($rememberToken);
    }

    public function test_an_administrator_can_activate_a_disabled_account(): void
    {
        $administrator = User::factory()->create(['role' => User::ROLE_ADMINISTRATOR]);
        $target = User::factory()->create(['statut' => User::STATUS_DISABLED]);

        $this->actingAs($administrator)
            ->patch(route('admin.utilisateurs.activate', $target))
            ->assertRedirectToRoute('admin.utilisateurs.show', $target)
            ->assertSessionHas('success', 'Le compte a été activé.');

        $this->assertDatabaseHas('users', [
            'id' => $target->id,
            'statut' => User::STATUS_ACTIVE,
        ]);
    }

    public function test_an_administrator_can_deactivate_an_account_and_the_account_loses_access(): void
    {
        $administrator = User::factory()->create(['role' => User::ROLE_ADMINISTRATOR]);
        $target = User::factory()->create();

        $this->actingAs($administrator)
            ->patch(route('admin.utilisateurs.deactivate', $target))
            ->assertRedirectToRoute('admin.utilisateurs.show', $target)
            ->assertSessionHas('success', 'Le compte a été désactivé.');

        $this->assertDatabaseHas('users', [
            'id' => $target->id,
            'statut' => User::STATUS_DISABLED,
        ]);

        $target->refresh();

        $this->actingAs($target)
            ->get(route('dashboard'))
            ->assertRedirect(route('login'));

        $this->assertGuest();
    }

    public function test_the_last_active_administrator_cannot_be_deactivated(): void
    {
        $administrator = User::factory()->create(['role' => User::ROLE_ADMINISTRATOR]);

        $this->actingAs($administrator)
            ->from(route('admin.utilisateurs.show', $administrator))
            ->patch(route('admin.utilisateurs.deactivate', $administrator))
            ->assertRedirect(route('admin.utilisateurs.show', $administrator))
            ->assertSessionHasErrors('statut');

        $this->assertDatabaseHas('users', [
            'id' => $administrator->id,
            'statut' => User::STATUS_ACTIVE,
        ]);
    }
}
