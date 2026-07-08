<?php

use App\Models\Message;
use App\Models\User;

it('marks a user dialog as read when an admin opens the chat', function () {
    $admin = User::factory()->admin()->create();
    $chatUser = User::factory()->create(['role' => 'user']);

    Message::create(['user_id' => $chatUser->id, 'sender' => 'user', 'text' => 'Привет', 'is_read' => false]);

    $this->actingAs($admin)->get(route('chat.show', $chatUser))->assertOk();

    expect(Message::where('user_id', $chatUser->id)->where('is_read', false)->count())->toBe(0);
});
