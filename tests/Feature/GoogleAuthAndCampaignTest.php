<?php

namespace Tests\Feature;

use App\Mail\CampaignEmail;
use App\Models\EmailLog;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Http;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\User as SocialiteUser;
use Mockery;
use Tests\TestCase;

class GoogleAuthAndCampaignTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test redirect to Google login.
     */
    public function test_google_login_redirects_to_google(): void
    {
        $response = $this->get(route('google.login'));
        
        // Assert redirecting to google oauth url (begins with accounts.google.com)
        $this->assertTrue(
            str_contains($response->getTargetUrl(), 'accounts.google.com')
        );
    }

    /**
     * Test Google callback registers new user and logs them in.
     */
    public function test_google_login_callback_registers_new_user_and_logs_in(): void
    {
        // Mock Socialite User details
        $googleUserMock = Mockery::mock(SocialiteUser::class);
        $googleUserMock->shouldReceive('getId')->andReturn('google-id-123');
        $googleUserMock->shouldReceive('getEmail')->andReturn('newuser@gmail.com');
        $googleUserMock->shouldReceive('getName')->andReturn('New Google User');
        $googleUserMock->shouldReceive('getAvatar')->andReturn('https://lh3.googleusercontent.com/avatar/1');

        $providerMock = Mockery::mock(\Laravel\Socialite\Two\GoogleProvider::class);
        $providerMock->shouldReceive('stateless')->andReturnSelf();
        $providerMock->shouldReceive('user')->andReturn($googleUserMock);

        Socialite::shouldReceive('driver')->with('google')->andReturn($providerMock);

        $response = $this->get(route('google.callback'));

        // Assert redirect to home
        $response->assertRedirect(route('home'));
        $response->assertSessionHas('success', 'Đăng nhập thành công!');

        // Assert user exists in database
        $this->assertDatabaseHas('users', [
            'email' => 'newuser@gmail.com',
            'google_id' => 'google-id-123',
            'name' => 'New Google User',
            'avatar' => 'https://lh3.googleusercontent.com/avatar/1',
        ]);

        // Assert user is authenticated
        $this->assertTrue(Auth::check());
        $this->assertEquals('newuser@gmail.com', Auth::user()->email);
    }

    /**
     * Test Google callback updates existing user and logs them in.
     */
    public function test_google_login_callback_updates_existing_user_and_logs_in(): void
    {
        // Pre-create user with same email but different google_id and name
        $existingUser = User::create([
            'name' => 'Old Name',
            'email' => 'existing@gmail.com',
            'google_id' => null,
            'avatar' => null,
        ]);

        $googleUserMock = Mockery::mock(SocialiteUser::class);
        $googleUserMock->shouldReceive('getId')->andReturn('google-id-existing');
        $googleUserMock->shouldReceive('getEmail')->andReturn('existing@gmail.com');
        $googleUserMock->shouldReceive('getName')->andReturn('Updated Name');
        $googleUserMock->shouldReceive('getAvatar')->andReturn('https://lh3.googleusercontent.com/avatar/updated');

        $providerMock = Mockery::mock(\Laravel\Socialite\Two\GoogleProvider::class);
        $providerMock->shouldReceive('stateless')->andReturnSelf();
        $providerMock->shouldReceive('user')->andReturn($googleUserMock);

        Socialite::shouldReceive('driver')->with('google')->andReturn($providerMock);

        $response = $this->get(route('google.callback'));

        $response->assertRedirect(route('home'));

        // Assert DB was updated
        $this->assertDatabaseHas('users', [
            'id' => $existingUser->id,
            'email' => 'existing@gmail.com',
            'name' => 'Updated Name',
            'google_id' => 'google-id-existing',
            'avatar' => 'https://lh3.googleusercontent.com/avatar/updated',
        ]);

        $this->assertTrue(Auth::check());
    }

    /**
     * Test user logout.
     */
    public function test_user_logout_invalidates_session_and_redirects(): void
    {
        $user = User::create([
            'name' => 'Test User',
            'email' => 'test@gmail.com',
        ]);

        $response = $this->actingAs($user)->post(route('logout'));

        $response->assertRedirect(route('home'));
        $response->assertSessionHas('success', 'Đăng xuất thành công!');
        $this->assertFalse(Auth::check());
    }

    /**
     * Test cannot send email when unauthenticated.
     */
    public function test_cannot_send_email_when_unauthenticated(): void
    {
        $response = $this->post(route('email.send'), [
            'subject' => 'Test Subject',
            'content' => 'Test Content',
        ]);

        // Should redirect back to home (guest status redirect configured in bootstrap/app.php)
        $response->assertRedirect('/');
    }

    /**
     * Test cannot send email with empty subject or content.
     */
    public function test_cannot_send_email_with_empty_subject_or_content(): void
    {
        $user = User::create([
            'name' => 'Test User',
            'email' => 'test@gmail.com',
        ]);

        $response = $this->actingAs($user)->post(route('email.send'), [
            'subject' => '',
            'content' => '',
            'recipients' => ['test@example.com'],
        ]);

        $response->assertSessionHasErrors(['subject', 'content']);
        $this->assertDatabaseEmpty('email_logs');
    }

    /**
     * Test successful campaign send triggers mail and database logs.
     */
    public function test_successful_campaign_send_triggers_mail_and_database_logs(): void
    {
        Mail::fake();

        $user = User::create([
            'name' => 'Campaign Admin',
            'email' => 'admin@gmail.com',
        ]);

        $response = $this->actingAs($user)->post(route('email.send'), [
            'subject' => 'New Product Broadcast',
            'content' => 'Hello team,\nCheck out our new launch!',
            'recipients' => [
                'devmelon2601@gmail.com',
                'dev.watermelon2602@gmail.com',
                'dev.pineapple.salt@gmail.com',
            ],
        ]);

        $response->assertRedirect(route('email.logs'));
        $response->assertSessionHas('success');

        // Check Mail was sent to all 3 recipients
        $recipients = [
            'devmelon2601@gmail.com',
            'dev.watermelon2602@gmail.com',
            'dev.pineapple.salt@gmail.com',
        ];

        foreach ($recipients as $recipient) {
            Mail::assertSent(CampaignEmail::class, function ($mail) use ($recipient) {
                return $mail->hasTo($recipient) &&
                       $mail->emailSubject === 'New Product Broadcast' &&
                       str_contains($mail->emailContent, 'Hello team');
            });
        }

        // Check EmailLog is created in DB
        $this->assertDatabaseHas('email_logs', [
            'user_id' => $user->id,
            'subject' => 'New Product Broadcast',
            'content' => 'Hello team,\nCheck out our new launch!',
            'total_recipients' => 3,
            'sent_success' => 3,
            'sent_failed' => 0,
        ]);
    }

    /**
     * Test successful campaign send via Gmail API when user has google_token.
     */
    public function test_can_send_email_via_gmail_api_when_token_present(): void
    {
        Http::fake([
            'https://gmail.googleapis.com/*' => Http::response(['id' => '12345'], 200),
        ]);

        $user = User::create([
            'name' => 'Campaign Admin With Token',
            'email' => 'admin@gmail.com',
            'google_token' => 'mock-oauth2-access-token-999',
        ]);

        $response = $this->actingAs($user)->post(route('email.send'), [
            'subject' => 'Gmail API Broadcast',
            'content' => 'Sent dynamically via Google API!',
            'recipients' => [
                'devmelon2601@gmail.com',
                'dev.watermelon2602@gmail.com',
            ],
        ]);

        $response->assertRedirect(route('email.logs'));
        $response->assertSessionHas('success');

        // Check HTTP Request was made to Gmail API twice
        Http::assertSent(function ($request) {
            return str_contains($request->url(), 'gmail.googleapis.com/gmail/v1/users/me/messages/send') &&
                   $request->hasHeader('Authorization', 'Bearer mock-oauth2-access-token-999') &&
                   !empty($request['raw']);
        });

        // Check EmailLog is created in DB
        $this->assertDatabaseHas('email_logs', [
            'user_id' => $user->id,
            'subject' => 'Gmail API Broadcast',
            'content' => 'Sent dynamically via Google API!',
            'total_recipients' => 2,
            'sent_success' => 2,
            'sent_failed' => 0,
        ]);
    }
}
