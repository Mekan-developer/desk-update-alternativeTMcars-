<?php

use App\Models\Category;
use App\Models\City;
use App\Models\Listing;
use App\Models\Region;
use App\Models\Review;
use App\Models\User;
use Laravel\Sanctum\Sanctum;

beforeEach(function () {
    $this->region   = Region::create(['name_ru' => 'Ахал', 'name_tk' => 'Ahal']);
    $this->city     = City::create(['region_id' => $this->region->id, 'name_ru' => 'Анау', 'name_tk' => 'Änew']);
    $this->category = Category::create(['name_ru' => 'Транспорт', 'name_tk' => 'Ulag', 'slug' => 'transport', 'level' => 1]);

    $this->user  = User::factory()->create();
    $this->owner = User::factory()->create();

    $this->listing = Listing::create([
        'user_id'     => $this->owner->id,
        'category_id' => $this->category->id,
        'region_id'   => $this->region->id,
        'city_id'     => $this->city->id,
        'title'       => 'Продам велосипед',
        'type'        => 'goods',
        'phone'       => $this->owner->phone,
        'status'      => 'approved',
    ]);
});

it('requires auth to leave a review', function () {
    $this->postJson('/api/v1/reviews', ['text' => 'Отлично', 'listing_id' => $this->listing->id])
        ->assertUnauthorized();
});

it('creates a listing review with pending status', function () {
    Sanctum::actingAs($this->user);

    $this->postJson('/api/v1/reviews', [
        'text'       => 'Отличный продавец, всё честно',
        'rating'     => 5,
        'listing_id' => $this->listing->id,
    ])
        ->assertCreated()
        ->assertJsonPath('data.status', 'pending')
        ->assertJsonPath('data.rating', 5)
        ->assertJsonPath('message', __('messages.review_submitted'));

    $review = Review::sole();
    expect($review->user_id)->toBe($this->user->id)
        ->and($review->listing_id)->toBe($this->listing->id)
        ->and($review->target_user_id)->toBeNull()
        ->and($review->status)->toBe('pending');
});

it('creates a user review with pending status', function () {
    Sanctum::actingAs($this->user);

    $this->postJson('/api/v1/reviews', [
        'text'           => 'Надёжный человек',
        'target_user_id' => $this->owner->id,
    ])
        ->assertCreated()
        ->assertJsonPath('data.status', 'pending')
        ->assertJsonPath('data.target_user_id', $this->owner->id);

    expect(Review::sole()->listing_id)->toBeNull();
});

it('requires exactly one review target — listing or user', function () {
    Sanctum::actingAs($this->user);

    // Ни одного объекта
    $this->postJson('/api/v1/reviews', ['text' => 'Текст'])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['listing_id', 'target_user_id']);

    // Оба объекта сразу
    $this->postJson('/api/v1/reviews', [
        'text'           => 'Текст',
        'listing_id'     => $this->listing->id,
        'target_user_id' => $this->owner->id,
    ])->assertUnprocessable();
});

it('validates rating range', function () {
    Sanctum::actingAs($this->user);

    $this->postJson('/api/v1/reviews', [
        'text'       => 'Текст',
        'rating'     => 6,
        'listing_id' => $this->listing->id,
    ])->assertUnprocessable()->assertJsonValidationErrors('rating');
});

it('forbids blocked user from leaving a review', function () {
    Sanctum::actingAs(User::factory()->blocked()->create());

    $this->postJson('/api/v1/reviews', [
        'text'       => 'Текст',
        'listing_id' => $this->listing->id,
    ])->assertForbidden();
});

it('renders admin reviews page with paginator, counts and search filter', function () {
    $admin = User::factory()->admin()->create();

    Review::create(['user_id' => $this->user->id, 'listing_id' => $this->listing->id, 'text' => 'Отличный велосипед', 'status' => 'pending']);
    Review::create(['user_id' => $this->user->id, 'target_user_id' => $this->owner->id, 'text' => 'Надёжный продавец', 'status' => 'approved']);

    $this->actingAs($admin)->get(route('reviews.index'))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('Reviews/Index')
            ->has('reviews.data', 2)
            ->has('reviews.links')
            ->where('counts.pending', 1)
            ->where('counts.approved', 1)
            ->has('rejectionReasons'));

    // Серверный поиск: по тексту и по имени объекта-пользователя
    $this->actingAs($admin)->get(route('reviews.index', ['search' => 'велосипед']))
        ->assertInertia(fn ($page) => $page->has('reviews.data', 1));
});
