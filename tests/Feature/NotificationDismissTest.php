<?php

use App\Models\User;

function notificationKeys(\Illuminate\Testing\TestResponse $response): array
{
    preg_match('/data-page="(.*?)"/s', $response->getContent(), $matches);
    $page = json_decode(html_entity_decode($matches[1]), true);

    return collect($page['props']['notifications'])->pluck('key')->all();
}

it('lists a new user as a notification and removes it after dismiss', function () {
    $admin = User::factory()->admin()->create();
    $this->actingAs($admin);

    $newUser = User::factory()->create(['role' => 'user']);

    $before = $this->get(route('dashboard'));
    expect(notificationKeys($before))->toContain("user:{$newUser->id}");

    $this->post(route('notifications.dismiss'), ['key' => "user:{$newUser->id}"])
        ->assertRedirect();

    $after = $this->get(route('dashboard'));
    expect(notificationKeys($after))->not->toContain("user:{$newUser->id}");
});

it('scopes dismissals per admin', function () {
    $adminA = User::factory()->admin()->create();
    $adminB = User::factory()->admin()->create();
    $newUser = User::factory()->create(['role' => 'user']);

    $this->actingAs($adminA)
        ->post(route('notifications.dismiss'), ['key' => "user:{$newUser->id}"])
        ->assertRedirect();

    $forB = $this->actingAs($adminB)->get(route('dashboard'));
    expect(notificationKeys($forB))->toContain("user:{$newUser->id}");
});
