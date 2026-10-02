<?php

namespace Tests\Feature\Admin;

use App\Models\Client;
use App\Models\Note;
use App\Models\Order;
use App\Models\City;
use App\Models\Locality;
use App\Models\State;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class NotificationAndNoteTest extends TestCase
{
    use RefreshDatabase;

    public function test_notification_list_filters_the_authenticated_users_read_state(): void
    {
        $admin = $this->makeUser('admin');
        $unread = $admin->notify(new class extends \Illuminate\Notifications\Notification {
            public function via($notifiable): array { return ['database']; }
            public function toDatabase($notifiable): array { return ['message' => 'New order']; }
        });
        $read = $admin->notifications()->create([
            'id' => (string) \Illuminate\Support\Str::uuid(),
            'type' => 'test', 'data' => ['message' => 'Already read'], 'read_at' => now(),
        ]);
        $otherUser = $this->makeUser('admin');
        $otherUser->notify(new class extends \Illuminate\Notifications\Notification {
            public function via($notifiable): array { return ['database']; }
            public function toDatabase($notifiable): array { return ['message' => 'Private to another user']; }
        });

        $this->actingAs($admin)->get(route('notifications.index', ['status' => 'unread']))
            ->assertOk()->assertInertia(fn (Assert $page) => $page
                ->component('Admin/Notifications')
                ->where('status', 'unread')
                ->has('notifications.data', 1)
                ->where('notifications.data.0.data.message', 'New order'));

        $this->actingAs($admin)->get(route('notifications.index', ['status' => 'read']))
            ->assertOk()->assertInertia(fn (Assert $page) => $page
                ->where('status', 'read')
                ->has('notifications.data', 1)
                ->where('notifications.data.0.data.message', 'Already read'));
    }

    public function test_admin_can_mark_only_their_own_notifications_as_read(): void
    {
        $admin = $this->makeUser('admin');
        $otherUser = $this->makeUser('admin');
        $ownNotification = $admin->notifications()->create([
            'id' => (string) \Illuminate\Support\Str::uuid(), 'type' => 'test', 'data' => ['message' => 'Mine'],
        ]);
        $otherNotification = $otherUser->notifications()->create([
            'id' => (string) \Illuminate\Support\Str::uuid(), 'type' => 'test', 'data' => ['message' => 'Theirs'],
        ]);

        $this->actingAs($admin)->patch(route('notifications.markAsRead', $ownNotification->id))
            ->assertOk()->assertJson(['success' => true]);
        $this->assertNotNull($ownNotification->fresh()->read_at);

        $this->actingAs($admin)->patch(route('notifications.markAsRead', $otherNotification->id))->assertOk();
        $this->assertNull($otherNotification->fresh()->read_at);

        $anotherUnread = $admin->notifications()->create([
            'id' => (string) \Illuminate\Support\Str::uuid(), 'type' => 'test', 'data' => ['message' => 'Unread'],
        ]);
        $this->actingAs($admin)->patch(route('notifications.readAll'))->assertRedirect();
        $this->assertNotNull($anotherUnread->fresh()->read_at);
        $this->assertNull($otherNotification->fresh()->read_at);
    }

    public function test_admins_share_order_and_client_notes_with_author_and_creation_time(): void
    {
        $author = $this->makeUser('admin');
        $viewer = $this->makeUser('admin');
        $state = State::factory()->create();
        $city = City::query()->create(['state_id' => $state->id]);
        $locality = Locality::query()->forceCreate(['city_id' => $city->id, 'postal_code' => '1000']);
        $client = Client::query()->create([
            'name' => 'Shared Note Client', 'phone' => '22123456', 'address' => 'Test address',
            'state_id' => $state->id, 'city_id' => $city->id, 'locality_id' => $locality->id,
        ]);
        $order = Order::query()->create([
            'client_id' => $client->id, 'client_name' => $client->name, 'phone' => $client->phone,
            'address' => $client->address, 'state_id' => $state->id, 'city_id' => $city->id,
            'locality_id' => $locality->id, 'status' => 'pending', 'source' => 'admin',
            'amount' => 10000, 'shipping_cost' => 0, 'delivery_date' => now()->addDay()->toDateString(),
        ]);

        $this->actingAs($author)->post(route('notes.store'), [
            'content' => 'Call after 4 PM', 'type' => 'order', 'id' => $order->id,
        ])->assertRedirect();
        $this->actingAs($author)->post(route('notes.store'), [
            'content' => 'Prefers text messages', 'type' => 'client', 'id' => $client->id,
        ])->assertRedirect();

        $this->assertDatabaseHas('notes', [
            'content' => 'Call after 4 PM', 'user_id' => $author->id,
            'notable_id' => $order->id, 'notable_type' => Order::class,
        ]);
        $this->assertDatabaseHas('notes', [
            'content' => 'Prefers text messages', 'user_id' => $author->id,
            'notable_id' => $client->id, 'notable_type' => Client::class,
        ]);

        $this->actingAs($viewer)->get(route('orders.show', $order))
            ->assertOk()->assertInertia(fn (Assert $page) => $page
                ->component('Admin/Orders/Show')
                ->where('order.data.notes.0.content', 'Call after 4 PM')
                ->where('order.data.notes.0.user.first_name', $author->first_name));

        $this->actingAs($viewer)->get(route('clients.show', $client))
            ->assertOk()->assertInertia(fn (Assert $page) => $page
                ->component('Admin/Clients/Show')
                ->where('notes.data.0.content', 'Prefers text messages')
                ->where('notes.data.0.user.first_name', $author->first_name));
    }

    private function makeUser(string $role): User
    {
        return User::query()->create([
            'first_name' => ucfirst($role), 'last_name' => 'Tester',
            'email' => fake()->unique()->safeEmail(), 'password' => Hash::make('password'),
            'role' => $role, 'status' => 'active',
        ]);
    }
}
