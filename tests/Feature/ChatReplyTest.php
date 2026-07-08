<?php

use App\Models\Message;
use App\Models\User;

it('lets an admin reply to a user dialog', function () {
    $admin = User::factory()->admin()->create();
    $chatUser = User::factory()->create(['role' => 'user']);

    Message::create(['user_id' => $chatUser->id, 'sender' => 'user', 'text' => 'Привет', 'is_read' => false]);

    $this->actingAs($admin)
        ->post(route('chat.reply', $chatUser), ['text' => 'Здравствуйте, чем можем помочь?'])
        ->assertRedirect();

    $this->assertDatabaseHas('messages', [
        'user_id'  => $chatUser->id,
        'sender'   => 'admin',
        'admin_id' => $admin->id,
        'text'     => 'Здравствуйте, чем можем помочь?',
        'is_read'  => false,
    ]);
});

it('requires non-empty text to reply', function () {
    $admin = User::factory()->admin()->create();
    $chatUser = User::factory()->create(['role' => 'user']);

    $this->actingAs($admin)
        ->post(route('chat.reply', $chatUser), ['text' => ''])
        ->assertSessionHasErrors('text');
});

it('passes dialogs to Chat/Show as a flat array, not a paginator object', function () {
    // Регрессия: раньше show() передавал $this->chatService->dialogs() (LengthAwarePaginator),
    // а Chat/Show.vue делает v-for="d in dialogs" и route('chat.show', d.id) ожидая plain Array —
    // на клиенте это падало в белый экран, как только появлялся хотя бы один диалог.
    $admin = User::factory()->admin()->create();
    $chatUser = User::factory()->create(['role' => 'user']);

    Message::create(['user_id' => $chatUser->id, 'sender' => 'user', 'text' => 'Привет', 'is_read' => false]);

    $this->actingAs($admin)
        ->get(route('chat.show', $chatUser))
        ->assertInertia(fn ($page) => $page
            ->component('Chat/Show')
            ->has('dialogs', 1)
            ->where('dialogs.0.id', $chatUser->id)
        );
});
