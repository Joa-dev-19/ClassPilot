<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_screen_can_be_rendered(): void
    {
        $response = $this->get('/register');

        $response->assertOk()
            ->assertSee('Créer un compte étudiant')
            ->assertSee('Adresse e-mail')
            ->assertSee('Mot de passe');
    }

    public function test_new_users_can_register(): void
    {
        Notification::fake();

        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => ' Test@Example.com ',
            'password' => 'password',
            'password_confirmation' => 'password',
            'role' => 'administrateur',
            'statut' => 'desactive',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('verification.notice'));
        $user = User::where('email', 'test@example.com')->firstOrFail();

        $this->assertSame(User::ROLE_STUDENT, $user->role);
        $this->assertSame(User::STATUS_ACTIVE, $user->statut);
        $this->assertNull($user->email_verified_at);
        $this->assertNotSame('password', $user->password);
        $this->assertTrue(Hash::check('password', $user->password));
        $this->assertDatabaseHas('users', [
            'email' => 'test@example.com',
            'role' => 'etudiant',
            'statut' => 'actif',
        ]);
        Notification::assertSentTo($user, VerifyEmail::class);
    }

    public function test_registration_rejects_an_email_address_that_is_already_used(): void
    {
        User::factory()->create([
            'email' => 'existing@example.com',
        ]);

        $response = $this->from('/register')->post('/register', [
            'name' => 'Another User',
            'email' => ' Existing@Example.com ',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertRedirect('/register')
            ->assertSessionHasErrors('email');

        $this->assertDatabaseCount('users', 1);
    }
}
