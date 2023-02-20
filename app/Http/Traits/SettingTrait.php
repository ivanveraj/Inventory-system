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
    public function addSetting($group, $key, $value)
    {
        return Setting::create([
            'group' => $group,
            'key' => $key,
            'value' =>  $value
        ]);
    }
}
