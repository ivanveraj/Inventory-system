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

    public function getSettings()
    {
        return Setting::pluck('value', 'key');
    }

    public function addSetting($group, $key, $value, $description = null)
    {
        return Setting::updateOrCreate(
            ['group' => $group, 'key' => $key], // Criterios de búsqueda
            ['value' => $value, 'description' => $description] // Valores a actualizar o insertar
        );
    }
}
