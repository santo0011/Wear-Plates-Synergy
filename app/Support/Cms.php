<?php

namespace App\Support;

/**
 * Access to the website content registry in config/cms.php with normalised field definitions.
 */
class Cms
{
    public const TYPES = ['text', 'textarea', 'image', 'lines', 'pairs', 'table', 'icon', 'url', 'boolean', 'repeater'];

    public static function pages(): array
    {
        return config('cms.pages', []);
    }

    public static function page(string $page): ?array
    {
        return static::pages()[$page] ?? null;
    }

    public static function section(string $page, string $section): ?array
    {
        $def = static::page($page)['sections'][$section] ?? null;
        if (! $def) {
            return null;
        }
        $def['fields'] = static::normalize($def['fields'] ?? []);

        return $def;
    }

    /** ['key' => [type, label, help, ...opts]] → ['key' => ['type' =>, 'label' =>, 'help' =>, ...]] */
    public static function normalize(array $fields): array
    {
        $out = [];
        foreach ($fields as $key => $def) {
            $field = [
                'type' => $def[0] ?? 'text',
                'label' => $def[1] ?? ucfirst(str_replace('_', ' ', $key)),
                'help' => $def[2] ?? null,
            ];
            foreach ($def as $optKey => $optValue) {
                if (is_string($optKey)) {
                    $field[$optKey] = $optValue;
                }
            }
            if ($field['type'] === 'repeater') {
                $field['fields'] = static::normalize($field['fields'] ?? []);
            }
            $out[$key] = $field;
        }

        return $out;
    }
}
