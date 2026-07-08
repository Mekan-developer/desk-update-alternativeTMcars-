<?php

namespace App\Actions;

use App\Models\Setting;

class ToggleManagerBannerPermissionAction
{
    public function execute(bool $canManageBanners): void
    {
        Setting::set('manager_can_manage_banners', $canManageBanners ? '1' : '0');
    }
}
