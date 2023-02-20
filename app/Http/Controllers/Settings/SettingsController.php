<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function index()
    {
        $general = Setting::where('group', 'Time')->orderBy('id', 'ASC')->get();
        return view('settings.settings', compact('general'));
    }

    public function general(Request $rq)
    {
        $general = Setting::where('group', 'Time')->get();
        foreach ($general as $conf) {
            $conf->value = is_null($rq[$conf->key]) ? $conf->value : $rq[$conf->key];
            $conf->save();
        }

        return AccionCorrecta('', '');
    }
}
