<?php

namespace App\Actions;

use App\Models\Setting;

class ToggleManagerNewsPermissionAction
{
    public function execute(bool $canManageNews): void
    {
        Setting::set('manager_can_manage_news', $canManageNews ? '1' : '0');
    }
}
