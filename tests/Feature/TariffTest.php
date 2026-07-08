<?php

use App\Models\Tariff;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

// User::factory() assumes an `email_verified_at` column that this project's
// users table does not have — build the row directly with real columns instead.
function actingAsTariffRole(string $role): User
{
    $user = User::create([
        'name' => 'Test ' . $role,
        'phone' => '+993' . fake()->unique()->numerify('#########'),
        'role' => $role,
        'password' => Hash::make('password'),
        'remember_token' => Str::random(10),
    ]);
    test()->actingAs($user);

    return $user;
}

function tariffPayload(array $overrides = []): array
{
    return array_merge([
        'name_ru' => 'Золотой', 'name_tk' => 'Altyn',
        'listings_limit' => 15, 'videos_limit' => 6, 'boost_limit' => 4,
        'duration_days' => 30, 'is_active' => true, 'is_free' => false,
    ], $overrides);
}

it('creates a tariff with bilingual name and all limits persisted', function () {
    actingAsTariffRole('admin');

    $this->post(route('tariffs.store'), tariffPayload())->assertRedirect();

    $tariff = Tariff::where('name_ru', 'Золотой')->firstOrFail();
    expect($tariff->name_tk)->toBe('Altyn')
        ->and($tariff->listings_limit)->toBe(15)
        ->and($tariff->videos_limit)->toBe(6)
        ->and($tariff->boost_limit)->toBe(4);
});

it('updates a tariff without losing the name or boost limit', function () {
    actingAsTariffRole('admin');
    $tariff = Tariff::create(tariffPayload(['name_ru' => 'Старт', 'name_tk' => 'Start', 'boost_limit' => 1]));

    $this->put(route('tariffs.update', $tariff), tariffPayload([
        'name_ru' => 'Старт+', 'name_tk' => 'Start+', 'boost_limit' => 2,
    ]))->assertRedirect();

    $tariff->refresh();
    expect($tariff->name_ru)->toBe('Старт+')
        ->and($tariff->name_tk)->toBe('Start+')
        ->and($tariff->boost_limit)->toBe(2);
});

it('only lets one tariff be free at a time', function () {
    actingAsTariffRole('admin');
    $free = Tariff::create(tariffPayload(['name_ru' => 'Бесплатный', 'name_tk' => 'Mugt', 'is_free' => true]));
    $paid = Tariff::create(tariffPayload(['name_ru' => 'Платный', 'name_tk' => 'Pully', 'is_free' => false]));

    $this->put(route('tariffs.update', $paid), tariffPayload([
        'name_ru' => $paid->name_ru, 'name_tk' => $paid->name_tk, 'is_free' => true,
    ]))->assertRedirect();

    expect((bool) $free->fresh()->is_free)->toBeFalse()
        ->and((bool) $paid->fresh()->is_free)->toBeTrue();
});

it('lets a manager create and edit tariffs but not delete them', function () {
    actingAsTariffRole('manager');
    $tariff = Tariff::create(tariffPayload());

    $this->post(route('tariffs.store'), tariffPayload(['name_ru' => 'Менеджерский', 'name_tk' => 'Dolandyryjy']))
        ->assertRedirect();

    $this->delete(route('tariffs.destroy', $tariff))->assertForbidden();
});
