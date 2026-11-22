<?php

namespace App\Support;

use App\Models\Setting;

/**
 * Admin sidebar: fixed items + one collapsible group per website page (sub-menu = page sections)
 * + Website Settings group. Built from config/cms.php so new sections appear automatically.
 */
class AdminMenu
{
    public static function build(): array
    {
        $request = request();
        $currentPage = $request->route('page');
        $currentSection = $request->route('section');
        $currentGroup = $request->route('group');

        $pages = [];
        foreach (Cms::pages() as $pageKey => $page) {
            $children = [];
            foreach ($page['extra_links'] ?? [] as [$route, $pattern, $icon, $label]) {
                $children[] = [
                    'url' => route($route),
                    'label' => $label,
                    'icon' => $icon,
                    'active' => $request->routeIs($pattern),
                ];
            }
            foreach ($page['sections'] as $sectionKey => $section) {
                $children[] = [
                    'url' => route('admin.content.edit', [$pageKey, $sectionKey]),
                    'label' => $section['label'],
                    'icon' => $section['icon'] ?? 'fa-solid fa-pen',
                    'active' => $request->routeIs('admin.content.*') && $currentPage === $pageKey && $currentSection === $sectionKey,
                ];
            }
            $pages[] = [
                'key' => $pageKey,
                'label' => $page['label'],
                'icon' => $page['icon'],
                'children' => $children,
                'open' => collect($children)->contains('active', true),
            ];
        }

        $settings = [];
        foreach (Setting::GROUPS as $groupKey => $group) {
            $settings[] = [
                'url' => route('admin.settings.edit', $groupKey),
                'label' => $group['label'],
                'icon' => $group['icon'],
                'active' => $request->routeIs('admin.settings.*') && ($currentGroup ?? 'general') === $groupKey,
            ];
        }

        return [
            'pages' => $pages,
            'settings' => [
                'key' => 'settings',
                'label' => 'Website Settings',
                'icon' => 'fa-solid fa-gear',
                'children' => $settings,
                'open' => $request->routeIs('admin.settings.*'),
            ],
        ];
    }
}
