<?php

use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\ChatController;
use App\Http\Controllers\Admin\CityController;
use App\Http\Controllers\Admin\ComplaintController;
use App\Http\Controllers\Admin\ComplaintReasonController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\DistrictController;
use App\Http\Controllers\Admin\ListingController;
use App\Http\Controllers\Admin\NewsController;
use App\Http\Controllers\Admin\PushController;
use App\Http\Controllers\Admin\RegionController;
use App\Http\Controllers\Admin\RejectionReasonController;
use App\Http\Controllers\Admin\ReviewController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\SmsGatewayController;
use App\Http\Controllers\Admin\StatisticsController;
use App\Http\Controllers\Admin\TariffController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\StatusController;
use App\Http\Controllers\Admin\VideoController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn() => redirect()->route('dashboard'));

Route::middleware(['auth', 'role:admin,manager'])->group(function () {

    Route::get('/dashboard', DashboardController::class)->name('dashboard');

    // Users
    Route::resource('users', UserController::class)->except('create', 'edit', 'show');
    Route::get('users/check-phone',         [UserController::class, 'checkPhone'])->name('users.check-phone');
    Route::get('users/{user}',              [UserController::class, 'show'])->name('users.show');
    Route::patch('users/{user}/block',      [UserController::class, 'block'])->name('users.block');
    Route::patch('users/{user}/unblock',    [UserController::class, 'unblock'])->name('users.unblock');
    Route::post('users/{user}/tariff',      [UserController::class, 'assignTariff'])->name('users.tariff');

    // Listings
    Route::resource('listings', ListingController::class)->except('create', 'edit');
    Route::patch('listings/{listing}/approve', [ListingController::class, 'approve'])->name('listings.approve');
    Route::patch('listings/{listing}/reject',  [ListingController::class, 'reject'])->name('listings.reject');
    Route::patch('listings/{listing}/boost',   [ListingController::class, 'boost'])->name('listings.boost');

    // Videos
    Route::resource('videos', VideoController::class)->except('create', 'edit');
    Route::patch('videos/{video}/approve', [VideoController::class, 'approve'])->name('videos.approve');
    Route::patch('videos/{video}/reject',  [VideoController::class, 'reject'])->name('videos.reject');

    // Chat
    Route::get('chat',                [ChatController::class, 'index'])->name('chat.index');
    Route::get('chat/{user}',         [ChatController::class, 'show'])->name('chat.show');
    Route::post('chat/{user}/reply',  [ChatController::class, 'reply'])->name('chat.reply');
    Route::patch('chat/{user}/read',  [ChatController::class, 'markRead'])->name('chat.read');

    // Categories
    Route::resource('categories', CategoryController::class)->except('create', 'edit', 'show');
    Route::patch('categories/{category}/toggle', [CategoryController::class, 'toggle'])->name('categories.toggle');
    Route::patch('categories/{category}/move',   [CategoryController::class, 'move'])->name('categories.move');

    // Regions & Cities & Districts (Регион → Город → Район)
    Route::resource('regions', RegionController::class)->except('create', 'edit', 'show');
    Route::patch('regions/{region}/toggle', [RegionController::class, 'toggle'])->name('regions.toggle');

    Route::resource('cities',  CityController::class)->except('create', 'edit', 'show');
    Route::patch('cities/{city}/toggle', [CityController::class, 'toggle'])->name('cities.toggle');

    Route::post('cities/{city}/districts',       [DistrictController::class, 'store'])->name('districts.store');
    Route::put('districts/{district}',           [DistrictController::class, 'update'])->name('districts.update');
    Route::patch('districts/{district}/toggle',  [DistrictController::class, 'toggle'])->name('districts.toggle');
    Route::delete('districts/{district}',        [DistrictController::class, 'destroy'])->name('districts.destroy');

    // Tariffs
    Route::resource('tariffs', TariffController::class)->except('create', 'edit', 'show');
    Route::patch('tariffs/{tariff}/toggle', [TariffController::class, 'toggle'])->name('tariffs.toggle');

    // News — manager needs can_manage_news permission (see Settings → Роли и права)
    Route::middleware('news.permission')->group(function () {
        Route::resource('news', NewsController::class)->except('create', 'edit', 'show');
        Route::patch('news/{news}/publish',   [NewsController::class, 'publish'])->name('news.publish');
        Route::patch('news/{news}/unpublish', [NewsController::class, 'unpublish'])->name('news.unpublish');
    });

    // Complaints
    Route::get('complaints',                          [ComplaintController::class, 'index'])->name('complaints.index');
    Route::patch('complaints/{complaint}/resolve',    [ComplaintController::class, 'resolve'])->name('complaints.resolve');

    // Reviews
    Route::get('reviews',                     [ReviewController::class, 'index'])->name('reviews.index');
    Route::patch('reviews/{review}/approve',  [ReviewController::class, 'approve'])->name('reviews.approve');
    Route::patch('reviews/{review}/reject',   [ReviewController::class, 'reject'])->name('reviews.reject');

    // Statistics
    Route::get('statistics', [StatisticsController::class, 'index'])->name('statistics.index');

    // Push, Settings — admin only
    Route::middleware('role:admin')->group(function () {
        Route::get('push',       [PushController::class, 'index'])->name('push.index');
        Route::post('push/send', [PushController::class, 'send'])->name('push.send');

        Route::resource('rejection-reasons', RejectionReasonController::class)->except('create', 'edit', 'show');
        Route::resource('complaint-reasons', ComplaintReasonController::class)->except('create', 'edit', 'show');

        // Settings (мониторинг, права менеджера, справочники причин, локализация, SMS-шлюз)
        Route::get('settings',                        [SettingsController::class, 'index'])->name('settings.index');
        Route::get('settings/monitoring',              StatusController::class)->name('settings.monitoring');
        Route::patch('settings/manager-permissions',   [SettingsController::class, 'updateManagerPermissions'])->name('settings.manager-permissions');
        Route::patch('settings/localization',          [SettingsController::class, 'updateLocalization'])->name('settings.localization');
        Route::get('settings/sms-gateway',             [SmsGatewayController::class, 'status'])->name('settings.sms-gateway');
        Route::post('settings/sms-gateway/test',       [SmsGatewayController::class, 'test'])->name('settings.sms-gateway.test');
    });
});

require __DIR__ . '/auth.php';
