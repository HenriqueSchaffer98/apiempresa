<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Run the auth controller login endpoint test.
     */
    public function testLoginSuccessfully()
    {
        User::factory()->create([
            'email' => 'test@test.com',
            'password' => bcrypt('password'),
        ]);

        $response = $this->postJson('/api/login', [
            'email' => 'test@test.com',
            'password' => 'password',
        ]);

        $response->assertStatus(200)->assertJsonStructure(['access_token']);
    }

    /**
     * Test a failed login attempt with incorrect credentials.
     */
    public function testLoginFailedIncorrectCredentials()
    {
        $response = $this->postJson('/api/login', [
            'email' => 'test@test.com',
            'password' => 'xpto',
        ]);

        $response->assertStatus(401)->assertJson(['error' => 'As credenciais fornecidas estão incorretas.']);
    }

    /**
     * Test a failed login attempt with missing email.
     */
    public function testLoginFailedLoginMissingEmail()
    {
        $response = $this->postJson('/api/login', [
            'password' => 'password',
        ]);

        $response->assertStatus(422)->assertJsonValidationErrors(['email']);
    }

    /**
     * Test a failed login attempt with missing password.
     */
    public function testLoginFailedLoginMissingPassword()
    {
        $response = $this->postJson('/api/login', [
            'email' => 'test@test.com',
        ]);

        $response->assertStatus(422)->assertJsonValidationErrors(['password']);
    }

    /**
     * Test a failed login attempt with a non-existent email.
     */
    public function testLoginFailedLoginNonExistentEmail()
    {
        $response = $this->postJson('/api/login', [
            'email' => 'noone@test.com',
            'password' => 'password',
        ]);

        $response->assertStatus(401)->assertJson(['error' => 'As credenciais fornecidas estão incorretas.']);
    }
}
