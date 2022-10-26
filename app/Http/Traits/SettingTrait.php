<?php

namespace App\Http\Traits;

use App\Models\Setting;

trait SettingTrait
{
    public function getSetting($key)
    {
        $setting = Setting::where('key', $key)->first();
        if (is_null($setting)) {
            return "";
        }
        return $setting->value;
    }
}
