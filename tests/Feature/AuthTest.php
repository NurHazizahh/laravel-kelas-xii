<?php

namespace Tests\Feature;

use App\Models\Profile;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthTest extends TestCase
{
    public function test_user_can_register_successfully(): void
    {
        $email = 'newuser_' . time() . '@example.com';
        $initialUserCount = User::count();
        $initialProfileCount = Profile::count();

        $response = $this->post('/register', [
            'name' => 'Test User Baru',
            'email' => $email,
            'password' => 'password123',
            'password_confirmation' => 'password123',
            '_form' => 'register',
        ]);

        $response->assertRedirect(route('home'));
        $response->assertSessionHas('success');

        $this->assertAuthenticated();

        // 1 user and 1 profile created
        $this->assertEquals($initialUserCount + 1, User::count());
        $this->assertEquals($initialProfileCount + 1, Profile::count());

        $user = User::where('email', $email)->first();
        $this->assertNotNull($user);
        $this->assertEquals('Test User Baru', $user->name);
        $this->assertEquals(1, $user->role_id);
        $this->assertTrue(Hash::check('password123', $user->password));

        $profile = Profile::find($user->profile_id);
        $this->assertNotNull($profile);
        $this->assertEquals(0, $profile->umur);
        $this->assertEquals('-', $profile->bio);
        $this->assertEquals('-', $profile->alamat);

        // Header displays user name and logout button when authenticated
        $home = $this->actingAs($user)->get('/');
        $home->assertSee('Test User Baru');
        $home->assertSee(route('logout'));
    }

    public function test_registration_validation_fails_on_duplicate_email(): void
    {
        $existing = User::first();
        $initialUserCount = User::count();
        $initialProfileCount = Profile::count();

        $response = $this->post('/register', [
            'name' => 'Duplicate Name',
            'email' => $existing->email,
            'password' => 'password123',
            'password_confirmation' => 'password123',
            '_form' => 'register',
        ]);

        $response->assertSessionHasErrors(['email']);
        $this->assertGuest();
        $this->assertEquals($initialUserCount, User::count());
        $this->assertEquals($initialProfileCount, Profile::count());
    }

    public function test_registration_validation_fails_on_unconfirmed_password(): void
    {
        $response = $this->post('/register', [
            'name' => 'Mismatch Password User',
            'email' => 'mismatch@example.com',
            'password' => 'password123',
            'password_confirmation' => 'wrongpassword',
            '_form' => 'register',
        ]);

        $response->assertSessionHasErrors(['password']);
        $this->assertGuest();
    }

    public function test_registration_validation_fails_on_short_password(): void
    {
        $response = $this->post('/register', [
            'name' => 'Short Pass User',
            'email' => 'shortpass@example.com',
            'password' => '1234567',
            'password_confirmation' => '1234567',
            '_form' => 'register',
        ]);

        $response->assertSessionHasErrors(['password']);
        $this->assertGuest();
    }

    public function test_admin_login_redirects_to_admin_dashboard(): void
    {
        $response = $this->post('/login', [
            'email' => 'admin@example.com',
            'password' => '12345678',
            'remember' => 1,
            '_form' => 'login',
        ]);

        $response->assertRedirect(route('admin.dashboard'));
        $this->assertAuthenticated();
    }

    public function test_regular_user_login_redirects_to_home(): void
    {
        $user = User::where('role_id', 1)->first();

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => '12345678',
            'remember' => 1,
            '_form' => 'login',
        ]);

        $response->assertRedirect(route('home'));
        $this->assertAuthenticated();
    }

    public function test_user_cannot_login_with_invalid_credentials(): void
    {
        $response = $this->from('/')->post('/login', [
            'email' => 'admin@example.com',
            'password' => 'wrongpassword',
            '_form' => 'login',
        ]);

        $response->assertRedirect('/');
        $response->assertSessionHasErrors(['login_error' => 'Email atau password salah']);
        $this->assertEquals('admin@example.com', session('_old_input.email'));
        $this->assertGuest();
    }

    public function test_authenticated_user_can_logout(): void
    {
        $user = User::first();

        $response = $this->actingAs($user)->post('/logout');

        $response->assertRedirect(route('home'));
        $response->assertSessionHas('success');
        $this->assertGuest();

        // Guest view displays Login link
        $home = $this->get('/');
        $home->assertSee('Login');
    }

    public function test_direct_get_logout_returns_405(): void
    {
        $response = $this->get('/logout');
        $response->assertStatus(405);
    }

    public function test_guest_middleware_protects_login_and_register(): void
    {
        $user = User::first();

        $response = $this->actingAs($user)->post('/login', [
            'email' => 'test@example.com',
            'password' => 'password',
        ]);
        $response->assertRedirect(route('home'));

        $response = $this->actingAs($user)->post('/register', [
            'name' => 'Another User',
            'email' => 'another@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);
        $response->assertRedirect(route('home'));
    }

    public function test_unauthenticated_user_cannot_logout(): void
    {
        $response = $this->post('/logout');
        $response->assertRedirect(route('home'));
    }

    public function test_home_movies_and_detail_routes_are_normal(): void
    {
        $home = $this->get('/');
        $home->assertStatus(200);

        $movies = $this->get('/movies');
        $movies->assertStatus(200);

        $show = $this->get('/movies/1');
        $show->assertStatus(200);
    }
}
