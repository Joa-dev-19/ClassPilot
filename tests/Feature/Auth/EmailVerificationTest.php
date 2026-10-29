<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Auth\Events\Verified;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class EmailVerificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_email_verification_screen_can_be_rendered(): void
    {
        $user = User::factory()->unverified()->create();

        $response = $this->actingAs($user)->get('/verify-email');

        $response->assertOk()
            ->assertSee('Vérifie ton adresse e-mail')
            ->assertSee('Renvoyer le lien de vérification');
    }

    public function test_verification_notification_renders_the_french_classpilot_email(): void
    {
        $user = User::factory()->unverified()->create([
            'name' => 'Camille Étudiante',
        ]);

        $mail = (new VerifyEmail)->toMail($user);
        $html = (string) $mail->render();
        $verificationUrl = $mail->viewData['url'];

        $this->assertSame('Confirme ton adresse e-mail - ClassPilot', $mail->subject);
        $this->assertStringContainsString('Bienvenue sur ClassPilot', $html);
        $this->assertStringContainsString('Bonjour Camille Étudiante', $html);
        $this->assertStringContainsString('Confirmer mon adresse e-mail', $html);
        $this->assertStringContainsString(
            '/verify-email/'.$user->id.'/',
            $verificationUrl
        );
        $this->assertStringContainsString(e($verificationUrl), $html);
    }

    public function test_unverified_users_cannot_access_private_application_pages(): void
    {
        $user = User::factory()->unverified()->create();

        $this->actingAs($user)
            ->get(route('dashboard'))
            ->assertRedirect(route('verification.notice'));

        $this->get(route('taches.index'))
            ->assertRedirect(route('verification.notice'));

        $this->get(route('calendrier.index'))
            ->assertRedirect(route('verification.notice'));

        $this->get(route('profile.edit'))
            ->assertRedirect(route('verification.notice'));
    }

    public function test_email_can_be_verified(): void
    {
        $user = User::factory()->unverified()->create();

        Event::fake();

        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => $user->id, 'hash' => sha1($user->email)]
        );

        $response = $this->actingAs($user)
            ->followingRedirects()
            ->get($verificationUrl);

        Event::assertDispatched(Verified::class);
        $this->assertTrue($user->fresh()->hasVerifiedEmail());
        $response->assertOk()
            ->assertSee('Ton adresse e-mail a bien été confirmée. Tu peux maintenant utiliser ton espace ClassPilot.');
    }

    public function test_email_is_not_verified_with_invalid_hash(): void
    {
        $user = User::factory()->unverified()->create();

        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => $user->id, 'hash' => sha1('wrong-email')]
        );

        $this->actingAs($user)->get($verificationUrl);

        $this->assertFalse($user->fresh()->hasVerifiedEmail());
    }
}
