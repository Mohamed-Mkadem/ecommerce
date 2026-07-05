<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Tests\TestCase;

class PruneNotificationsCommandTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_deletes_notifications_older_than_seven_days(): void
    {
        DB::table('notifications')->insert([
            [
                'id' => Str::uuid(),
                'type' => 'App\\Notifications\\TestNotification',
                'notifiable_type' => 'App\\Models\\User',
                'notifiable_id' => 1,
                'data' => json_encode(['message' => 'old']),
                'read_at' => null,
                'created_at' => now()->subDays(8),
                'updated_at' => now()->subDays(8),
            ],
            [
                'id' => Str::uuid(),
                'type' => 'App\\Notifications\\TestNotification',
                'notifiable_type' => 'App\\Models\\User',
                'notifiable_id' => 1,
                'data' => json_encode(['message' => 'recent']),
                'read_at' => null,
                'created_at' => now()->subDays(2),
                'updated_at' => now()->subDays(2),
            ],
        ]);

        $this->assertDatabaseCount('notifications', 2);

        Artisan::call('notifications:prune');

        $this->assertDatabaseCount('notifications', 1);
        $this->assertDatabaseHas('notifications', ['data' => json_encode(['message' => 'recent'])]);
    }
}
