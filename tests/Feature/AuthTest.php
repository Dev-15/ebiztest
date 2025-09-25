<?php
namespace Tests\Feature;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;

class AuthTest extends TestCase
{
    use RefreshDatabase;
    public function test_register_and_login()
    {
        $this->postJson('/api/auth/register', ['name'=>'T','email'=>'t@example.com','password'=>'password'])->assertStatus(201);
        $this->postJson('/api/auth/login', ['email'=>'t@example.com','password'=>'password'])->assertStatus(200);
    }
}
