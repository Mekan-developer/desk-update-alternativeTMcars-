<?php

use App\Models\City;
use App\Models\Region;
use App\Models\SmsCode;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\Sanctum;

beforeEach(function () {
    Storage::fake('public');
    $this->user = User::factory()->create(['name' => 'Old Name']);
    Sanctum::actingAs($this->user);
});

it('returns the current profile', function () {
    $this->getJson('/api/v1/profile')
        ->assertOk()
        ->assertJsonPath('data.id', $this->user->id)
        ->assertJsonPath('data.phone', $this->user->phone);
});

it('updates profile fields partially', function () {
    $region = Region::create(['name_ru' => 'Ахал', 'name_tk' => 'Ahal']);
    $city = City::create(['region_id' => $region->id, 'name_ru' => 'Анау', 'name_tk' => 'Änew']);

    $this->putJson('/api/v1/profile', [
        'name'       => 'New Name',
        'gender'     => 'male',
        'birth_date' => '1995-04-12',
        'region_id'  => $region->id,
        'city_id'    => $city->id,
    ])
        ->assertOk()
        ->assertJsonPath('data.name', 'New Name')
        ->assertJsonPath('data.region.id', $region->id);

    // Частичное обновление: остальные поля не затираются
    $this->putJson('/api/v1/profile', ['gender' => 'female'])->assertOk();

    $fresh = $this->user->fresh();
    expect($fresh->name)->toBe('New Name')
        ->and($fresh->gender)->toBe('female')
        ->and($fresh->birth_date->format('Y-m-d'))->toBe('1995-04-12');
});

it('allows clearing optional fields', function () {
    $this->putJson('/api/v1/profile', ['name' => null])
        ->assertOk()
        ->assertJsonPath('data.name', null);
});

it('validates profile fields', function () {
    $this->putJson('/api/v1/profile', [
        'gender'     => 'other',
        'birth_date' => '2999-01-01',
        'region_id'  => 999,
    ])
        ->assertStatus(422)
        ->assertJsonValidationErrors(['gender', 'birth_date', 'region_id']);
});

it('uploads an avatar as webp and replaces the old one', function () {
    $first = $this->postJson('/api/v1/profile/avatar', [
        'avatar' => UploadedFile::fake()->image('me.jpg', 800, 600),
    ])->assertOk()->json('data.avatar');

    $firstPath = ltrim(str_replace('/storage/', '', $first), '/');
    expect($first)->toEndWith('.webp');
    Storage::disk('public')->assertExists($firstPath);

    $second = $this->postJson('/api/v1/profile/avatar', [
        'avatar' => UploadedFile::fake()->image('me2.png', 500, 500),
    ])->assertOk()->json('data.avatar');

    Storage::disk('public')->assertMissing($firstPath);
    Storage::disk('public')->assertExists(ltrim(str_replace('/storage/', '', $second), '/'));
});

it('deletes the avatar', function () {
    $this->postJson('/api/v1/profile/avatar', [
        'avatar' => UploadedFile::fake()->image('me.jpg', 400, 400),
    ])->assertOk();

    $path = $this->user->fresh()->avatar;

    $this->deleteJson('/api/v1/profile/avatar')
        ->assertOk()
        ->assertJsonPath('data.avatar', null);

    Storage::disk('public')->assertMissing($path);
});

it('changes phone after sms confirmation', function () {
    $newPhone = '+99365999888';

    $this->postJson('/api/v1/profile/phone/send-code', ['phone' => $newPhone])->assertOk();

    $code = SmsCode::where('phone', $newPhone)->first()->code;

    $this->postJson('/api/v1/profile/phone/confirm', ['phone' => $newPhone, 'code' => $code])
        ->assertOk()
        ->assertJsonPath('data.phone', $newPhone);

    expect($this->user->fresh()->phone)->toBe($newPhone);
});

it('rejects phone change to an already taken number', function () {
    $other = User::factory()->create();

    $this->postJson('/api/v1/profile/phone/send-code', ['phone' => $other->phone])
        ->assertStatus(422)
        ->assertJsonValidationErrors('phone');
});

it('rejects phone change with a wrong code', function () {
    $newPhone = '+99365999888';
    $oldPhone = $this->user->phone;

    $this->postJson('/api/v1/profile/phone/send-code', ['phone' => $newPhone])->assertOk();

    $this->postJson('/api/v1/profile/phone/confirm', ['phone' => $newPhone, 'code' => '000000'])
        ->assertStatus(422);

    expect($this->user->fresh()->phone)->toBe($oldPhone);
});
