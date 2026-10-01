<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class AuthenticationAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_from_protected_admin_pages_to_login(): void
    {
        $this->get(route('dashboard'))->assertRedirect(route('login'));
        $this->get(route('products.index'))->assertRedirect(route('login'));
    }

    public function test_user_can_log_in_with_valid_credentials_and_log_out(): void
    {
        $user = $this->makeUser();

        $this->post(route('login'), [
            'email' => $user->email,
            'password' => 'password',
        ])->assertRedirect(route('dashboard', absolute: false));

        $this->assertAuthenticatedAs($user);

        $this->post(route('logout'))->assertRedirect('/');
        $this->assertGuest();
    }

    public function test_invalid_password_does_not_authenticate_the_user(): void
    {
        $user = $this->makeUser();

        $this->from(route('login'))->post(route('login'), [
            'email' => $user->email,
            'password' => 'wrong-password',
        ])->assertRedirect(route('login'));

        $this->assertGuest();
    }

    public function test_moderator_can_use_regular_admin_pages_but_not_admin_only_pages(): void
    {
        $moderator = $this->makeUser(role: 'moderator');

        $this->actingAs($moderator)->get(route('products.index'))->assertOk();
        $this->actingAs($moderator)->get(route('employees.index'))->assertForbidden();
    }

    public function test_admin_can_access_admin_only_employee_management(): void
    {
        $admin = $this->makeUser(role: 'admin');

        $this->actingAs($admin)
            ->get(route('employees.index'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page->component('Admin/Employees/Index'));
    }

    public function test_banned_user_is_sent_to_the_banned_page_and_active_user_cannot_open_it(): void
    {
        $banned = $this->makeUser(status: 'banned');

        $this->actingAs($banned)->get(route('dashboard'))->assertRedirect(route('user.banned'));
        $this->actingAs($banned)
            ->get(route('user.banned'))
            ->assertOk()
            ->assertInertia(fn (Assert $page) => $page->component('UserBanned'));

        $active = $this->makeUser();
        $this->actingAs($active)->get(route('user.banned'))->assertRedirect(route('dashboard'));
    }

    public function test_user_can_update_profile_details_and_password(): void
    {
        $user = $this->makeUser();

        $this->actingAs($user)->patch(route('profile.update'), [
            'first_name' => 'Updated',
            'last_name' => 'Customer',
            'email' => 'updated@example.test',
        ])->assertRedirect(route('profile.edit'));

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'first_name' => 'Updated',
            'last_name' => 'Customer',
            'email' => 'updated@example.test',
        ]);

        $this->put(route('password.update'), [
            'current_password' => 'password',
            'password' => 'New-password-123',
            'password_confirmation' => 'New-password-123',
        ])->assertRedirect();

        $this->assertTrue(Hash::check('New-password-123', $user->fresh()->password));
    }

    private function makeUser(string $role = 'moderator', string $status = 'active'): User
    {
        return User::create([
            'first_name' => 'Test',
            'last_name' => 'Account',
            'email' => fake()->unique()->safeEmail(),
            'password' => Hash::make('password'),
            'role' => $role,
            'status' => $status,
        ]);
    }
}
