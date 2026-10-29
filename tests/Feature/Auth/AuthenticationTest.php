<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_screen_can_be_rendered(): void
    {
        $response = $this->get('/login');

        $response->assertOk()
            ->assertSee('Connexion à ClassPilot')
            ->assertSee('Adresse e-mail')
            ->assertSee('Se connecter')
            ->assertSee('Créer un compte étudiant');
    }

    public function test_users_can_authenticate_using_the_login_screen(): void
    {
        $user = User::factory()->create();

        $response = $this->post('/login', [
            'email' => ' '.strtoupper($user->email).' ',
            'password' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('dashboard', absolute: false));
    }

    public function test_unverified_users_are_redirected_to_email_verification_after_login(): void
    {
        $user = User::factory()->unverified()->create();

        $response = $this->followingRedirects()->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $this->assertAuthenticatedAs($user);
        $response->assertOk()
            ->assertSee('Vérifie ton adresse e-mail');
    }

    public function test_users_can_not_authenticate_with_invalid_password(): void
    {
        $user = User::factory()->create();

        $response = $this->from('/login')->post('/login', [
            'email' => $user->email,
            'password' => 'wrong-password',
        ]);

        $this->assertGuest();
        $response->assertRedirect('/login')
            ->assertSessionHasErrors([
                'email' => 'Ces identifiants ne correspondent pas à nos informations.',
            ]);
    }

    public function test_login_attempts_are_limited_after_five_failures(): void
    {
        $credentials = [
            'email' => 'inconnu@example.com',
            'password' => 'wrong-password',
        ];

        for ($attempt = 0; $attempt < 5; $attempt++) {
            $this->post('/login', $credentials);
        }

        $response = $this->from('/login')
            ->followingRedirects()
            ->post('/login', $credentials);

        $this->assertGuest();
        $response->assertOk()
            ->assertSee('Trop de tentatives de connexion.');
    }

    public function test_disabled_users_can_not_authenticate(): void
    {
        $user = User::factory()->create([
            'statut' => User::STATUS_DISABLED,
        ]);

        $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $this->assertGuest();
    }

    public function test_disabled_user_is_logged_out_when_their_account_is_deactivated(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $user->forceFill(['statut' => User::STATUS_DISABLED])->save();

        $response = $this->get('/profile');

        $this->assertGuest();
        $response->assertRedirect(route('login'));
    }

    public function test_only_students_can_access_student_task_routes(): void
    {
        $teacher = User::factory()->create([
            'role' => User::ROLE_TEACHER,
        ]);

        $this->actingAs($teacher)
            ->get(route('taches.index'))
            ->assertForbidden();
    }

    public function test_administrators_cannot_access_student_task_routes(): void
    {
        $administrator = User::factory()->create([
            'role' => User::ROLE_ADMINISTRATOR,
        ]);

        $this->actingAs($administrator)
            ->get(route('taches.index'))
            ->assertForbidden();
    }

    public function test_students_can_access_student_task_routes(): void
    {
        $student = User::factory()->create([
            'role' => User::ROLE_STUDENT,
        ]);

        $this->actingAs($student)
            ->get(route('taches.index'))
            ->assertOk();
    }

    public function test_guests_are_redirected_to_login_from_student_task_routes(): void
    {
        $this->get(route('taches.index'))
            ->assertRedirect(route('login'));
    }

    public function test_users_can_logout(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/logout');

        $this->assertGuest();
        $response->assertRedirect('/');
    }
}
