<?php

use App\Models\Setting;

if (! function_exists('setting')) {
    /** Read a website setting managed from the admin panel. */
    function setting(string $key, $default = null)
    {
        return Setting::getValue($key, $default);
    }
}

if (! function_exists('setting_list')) {
    /** Read a multi-line "list" setting as arrays of "|"-separated fields. */
    function setting_list(string $key): array
    {
        return Setting::listValue($key);
    }
}

if (! function_exists('whatsapp_url')) {
    /** Build a wa.me link to the configured WhatsApp number with an optional prefilled message. */
    function whatsapp_url(?string $message = null): ?string
    {
        $number = preg_replace('/\D+/', '', (string) setting('whatsapp_number', setting('phone')));
        if ($number === '') {
            return null;
        }

        return 'https://wa.me/'.$number.($message ? '?text='.rawurlencode($message) : '');
    }
}

if (! function_exists('tel_url')) {
    function tel_url(?string $phone): string
    {
        return 'tel:'.preg_replace('/[^\d+]/', '', (string) $phone);
    }
}
