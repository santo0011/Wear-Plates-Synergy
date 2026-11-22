<?php

namespace App\Support;

use ArrayAccess;

/**
 * Read-only wrapper around one CMS section, used in Blade:
 *   $hero = cms('home.hero');  $hero->title;  $hero->items('slides');  $hero->lines('points');
 */
class SectionContent implements ArrayAccess
{
    public function __construct(private array $data = [], private bool $active = true) {}

    public function active(): bool
    {
        return $this->active;
    }

    public function get(string $key, $default = null)
    {
        $value = $this->data[$key] ?? null;

        return ($value === null || $value === '' || $value === []) ? $default : $value;
    }

    public function has(string $key): bool
    {
        return $this->get($key) !== null;
    }

    public function __get(string $key)
    {
        return $this->get($key);
    }

    public function __isset(string $key): bool
    {
        return $this->has($key);
    }

    /** Repeater items (empty items removed). */
    public function items(string $key): array
    {
        $items = $this->data[$key] ?? [];

        return is_array($items) ? array_values(array_filter($items, fn ($i) => is_array($i) && array_filter($i, fn ($v) => $v !== null && $v !== ''))) : [];
    }

    /** "lines" field: one entry per line. */
    public function lines(string $key): array
    {
        return cms_lines($this->data[$key] ?? '');
    }

    /** "table" field: rows of cells, first row is the header. */
    public function table(string $key): array
    {
        return cms_table($this->data[$key] ?? '');
    }

    public function toArray(): array
    {
        return $this->data;
    }

    public function offsetExists(mixed $offset): bool
    {
        return $this->has($offset);
    }

    public function offsetGet(mixed $offset): mixed
    {
        return $this->get($offset);
    }

    public function offsetSet(mixed $offset, mixed $value): void {}

    public function offsetUnset(mixed $offset): void {}
}
