<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_log_in_with_username(): void
    {
        $user = $this->createUser();

        $response = $this->from(route('login'))->post(route('login.store'), [
            'login' => $user->username,
            'password' => 'password123',
        ]);

        $response->assertRedirect(route('dashboard'));
        $this->assertAuthenticatedAs($user);
        $this->assertDatabaseHas('login_logs', [
            'user_id' => $user->id,
            'username_input' => $user->username,
            'status' => 'SUCCESS',
        ]);
        $this->withoutExceptionHandling()->get(route('dashboard'))->assertOk();
    }

    public function test_user_can_log_in_with_email(): void
    {
        $user = $this->createUser();

        $response = $this->from(route('login'))->post(route('login.store'), [
            'login' => $user->email,
            'password' => 'password123',
        ]);

        $response->assertRedirect(route('dashboard'));
        $this->assertAuthenticatedAs($user);
    }

    public function test_invalid_password_does_not_authenticate_user(): void
    {
        $user = $this->createUser();

        $response = $this->from(route('login'))->post(route('login.store'), [
            'login' => $user->username,
            'password' => 'wrong-password',
        ]);

        $response->assertRedirect(route('login'));
        $response->assertSessionHasErrors('login');
        $this->assertGuest();
        $this->assertDatabaseHas('login_logs', [
            'user_id' => $user->id,
            'status' => 'FAILED',
        ]);
    }

    public function test_logout_invalidates_the_authenticated_session(): void
    {
        $user = $this->createUser();
        $this->actingAs($user);

        $response = $this->post(route('logout'));

        $response->assertRedirect(route('login'));
        $this->assertGuest();
        $this->assertDatabaseHas('login_logs', [
            'user_id' => $user->id,
            'status' => 'LOGOUT',
        ]);
    }

    public function test_newly_created_admin_is_redirected_to_login_after_logout(): void
    {
        $admin = User::create([
            'name' => 'New Admin',
            'username' => 'new-admin',
            'email' => 'new-admin@example.test',
            'phone' => '6281234567891',
            'password' => Hash::make('new-admin-password'),
            'role' => 'admin',
        ]);

        $this->post(route('login.store'), [
            'login' => $admin->username,
            'password' => 'new-admin-password',
        ])->assertRedirect(route('dashboard'));

        $response = $this->post(route('logout'));
        $response
            ->assertStatus(303)
            ->assertRedirect(route('login'));
        $this->assertStringContainsString('no-store', $response->headers->get('Cache-Control'));

        $this->assertGuest();
        $this->get(route('login'))->assertOk();
        $this->get(route('dashboard'))->assertRedirect(route('login'));
    }

    private function createUser(): User
    {
        return User::create([
            'name' => 'Test User',
            'username' => 'test-user',
            'email' => 'test@example.test',
            'phone' => '6281234567890',
            'password' => Hash::make('password123'),
            'role' => 'owner',
        ]);
    }
}
