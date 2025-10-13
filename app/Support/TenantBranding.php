<?php

namespace App\Support;

class TenantBranding
{
    public static function current(): array
    {
        if (! function_exists('tenant') || ! tenant()) {
            return self::defaults('central');
        }

        $t = tenant();
        $id = method_exists($t, 'getTenantKey') ? $t->getTenantKey() : $t->id;
        
        // Safely read branding data from tenants.data JSON
        $get = fn (string $key, $default = null) => method_exists($t, 'get') ? $t->get($key, $default) : data_get($t->data ?? [], $key, $default);
      
        return [
            'slug'          => (string) $id,
            'display_name'  => ($t['display_name'] ?? 'Default Tenant'),
            'logo_url'      => ($t['logo_url'] ?? 'https://via.placeholder.com/64x64.png?text=B'),
            'primary_color' => ($t['primary_color'] ?? '#4f46e5'),
            'accent_color'  => ($t['accent_color'] ?? '#22c55e'),
            'bg_color'      => ($t['bg_color'] ?? '#f8fafc'),
            'text_color'    => ($t['text_color'] ?? '#111827'),
        ];
    }

    public static function defaults(string $name = 'central'): array
    {
        return [
            'slug'          => 'alpha fitness',
            'display_name'  => 'Alpha Fitness',
            'logo_url'      => null,
            'primary_color' => '#6d28d9',
            'accent_color'  => '#f59e0b',
            'bg_color'      => '#f5f3ff',
            'text_color'    => '#111827',
        ];
    }
}
