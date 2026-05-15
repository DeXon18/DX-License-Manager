<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;
use App\Notifications\NewUserCredentials;
use App\Models\User;

class EmailLogTest extends TestCase
{
    use RefreshDatabase;

    public function test_email_is_logged_in_database_after_notification(): void
    {
        $user = User::factory()->create(['email' => 'test@example.com']);
        
        $user->notify(new NewUserCredentials('password123'));

        $this->assertDatabaseHas('email_logs', [
            'recipient' => 'test@example.com',
            'subject' => '🔐 Tus credenciales de acceso — DX License Manager',
            'status' => 'sent'
        ]);
    }
}
