<?php
use App\Models\Setting;

if (!function_exists('setting')) {
    function setting(string|array $key, mixed $default = null): mixed
    {
        // Écriture : setting(['key' => 'value'])
        if (is_array($key)) {
            foreach ($key as $k => $v) {
                Setting::updateOrCreate(['key' => $k], ['value' => $v]);
            }
            return true;
        }

        // Lecture : setting('key', 'default')
        $setting = Setting::where('key', $key)->first();
        return $setting?->value ?? $default;
    }
}