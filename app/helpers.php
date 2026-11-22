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

if (! function_exists('cms')) {
    /** Read an editable page section, e.g. cms('home.hero'). Content is managed in Admin → page menus. */
    function cms(string $key): \App\Support\SectionContent
    {
        $row = \App\Models\PageSection::allContent()[$key] ?? null;

        return new \App\Support\SectionContent($row['content'] ?? [], $row ? (bool) $row['active'] : false);
    }
}

if (! function_exists('cms_lines')) {
    /** Split a multi-line text into trimmed, non-empty lines. */
    function cms_lines(?string $text): array
    {
        return array_values(array_filter(array_map('trim', preg_split('/\r\n|\r|\n/', (string) $text)), fn ($l) => $l !== ''));
    }
}

if (! function_exists('cms_pairs')) {
    /** Lines in the form "Label | Value" → [[label, value], ...]. */
    function cms_pairs(?string $text): array
    {
        return array_map(function ($line) {
            $parts = array_map('trim', explode('|', $line, 2));

            return [$parts[0], $parts[1] ?? ''];
        }, cms_lines($text));
    }
}

if (! function_exists('cms_table')) {
    /** Pipe-separated rows → array of cell arrays (first row is the header). */
    function cms_table(?string $text): array
    {
        return array_map(fn ($line) => array_map('trim', explode('|', $line)), cms_lines($text));
    }
}

if (! function_exists('rich_text')) {
    /**
     * Escape editor text and render paragraphs (blank line = new paragraph),
     * line breaks and **bold** markers. Safe for direct output.
     */
    function rich_text(?string $text, string $pClass = ''): \Illuminate\Support\HtmlString
    {
        $blocks = preg_split('/(\r\n|\r|\n){2,}/', trim((string) $text));
        $class = $pClass !== '' ? ' class="'.e($pClass).'"' : '';
        $html = '';
        foreach ($blocks as $block) {
            if (trim($block) === '') {
                continue;
            }
            $escaped = nl2br(e(trim($block)), false);
            $escaped = preg_replace('/\*\*(.+?)\*\*/s', '<strong>$1</strong>', $escaped);
            $html .= "<p{$class}>{$escaped}</p>";
        }

        return new \Illuminate\Support\HtmlString($html);
    }
}

if (! function_exists('heading_text')) {
    /** Escape a heading and render **word** as a highlighted span. */
    function heading_text(?string $text): \Illuminate\Support\HtmlString
    {
        return new \Illuminate\Support\HtmlString(preg_replace('/\*\*(.+?)\*\*/s', '<span class="hl">$1</span>', e((string) $text)));
    }
}

if (! function_exists('plain_text')) {
    /** Strip the **highlight** markers (for titles, alt text, meta tags). */
    function plain_text(?string $text): string
    {
        return trim(str_replace('**', '', (string) $text));
    }
}

if (! function_exists('cms_link')) {
    /** Turn an admin-entered link ("/products", "products", full URL, "#id") into a URL. */
    function cms_link(?string $link, string $fallback = '#'): string
    {
        $link = trim((string) $link);
        if ($link === '') {
            return $fallback;
        }
        if (preg_match('#^(https?:|mailto:|tel:|\#)#i', $link)) {
            return $link;
        }

        return url('/'.ltrim($link, '/'));
    }
}

if (! function_exists('inline_text')) {
    /** Escape a single line and render **bold** markers. */
    function inline_text(?string $text): \Illuminate\Support\HtmlString
    {
        return new \Illuminate\Support\HtmlString(preg_replace('/\*\*(.+?)\*\*/s', '<strong>$1</strong>', e((string) $text)));
    }
}
