<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Table;
use Tests\TestCase;

class ValidateTableSessionTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_without_table_can_browse_menu()
    {
        $response = $this->get('/');
        $response->assertStatus(200);
    }

    public function test_table_access_requires_token()
    {
        $table = Table::create([
            'name' => 'Masa 1',
            'token' => 'masa-1-token',
            'restaurant_id' => 1,
            'session_token' => 'xyz123',
            'session_expires_at' => now()->addMinutes(30)
        ]);

        $response = $this->get('/?masa=masa-1-token');
        $response->assertStatus(403);
    }

    public function test_table_access_blocked_on_invalid_token()
    {
        $table = Table::create([
            'name' => 'Masa 1',
            'token' => 'masa-1-token',
            'restaurant_id' => 1,
            'session_token' => 'xyz123',
            'session_expires_at' => now()->addMinutes(30)
        ]);

        $response = $this->get('/?masa=masa-1-token&token=wrongtoken');
        $response->assertStatus(403);
    }

    public function test_table_access_blocked_on_expired_token()
    {
        $table = Table::create([
            'name' => 'Masa 1',
            'token' => 'masa-1-token',
            'restaurant_id' => 1,
            'session_token' => 'xyz123',
            'session_expires_at' => now()->subMinutes(10)
        ]);

        $response = $this->get('/?masa=masa-1-token&token=xyz123');
        $response->assertStatus(403);
    }

    public function test_table_access_allowed_on_valid_token()
    {
        $table = Table::create([
            'name' => 'Masa 1',
            'token' => 'masa-1-token',
            'restaurant_id' => 1,
            'session_token' => 'xyz123',
            'session_expires_at' => now()->addMinutes(30)
        ]);

        $response = $this->get('/?masa=masa-1-token&token=xyz123');
        $response->assertStatus(200);
        $this->assertEquals('xyz123', session('table_session_token_' . $table->id));
        $this->assertEquals($table->id, session('active_table_id'));
    }

    public function test_table_access_allowed_on_subsequent_request_without_token_parameter()
    {
        $table = Table::create([
            'name' => 'Masa 1',
            'token' => 'masa-1-token',
            'restaurant_id' => 1,
            'session_token' => 'xyz123',
            'session_expires_at' => now()->addMinutes(30)
        ]);

        $response = $this->withSession([
            'active_table_id' => $table->id,
            'table_session_token_' . $table->id => 'xyz123'
        ])->get('/?masa=masa-1-token');

        $response->assertStatus(200);
    }
}
