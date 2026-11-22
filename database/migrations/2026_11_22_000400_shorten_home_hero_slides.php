<?php

use Database\Seeders\ContentSeeder;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

/**
 * Home hero: replace the four long slides with three short ones.
 * Background photos already uploaded are kept (by position).
 */
return new class extends Migration
{
    public function up(): void
    {
        $row = DB::table('page_sections')->where(['page' => 'home', 'section' => 'hero'])->first();
        if (! $row) {
            return;
        }

        $content = json_decode($row->content ?? '[]', true) ?: [];
        $old = array_values($content['slides'] ?? []);
        $keys = ['eyebrow', 'title', 'text', 'image', 'button_text', 'button_link'];

        $content['slides'] = array_map(function ($values, $i) use ($keys, $old) {
            $slide = array_combine($keys, $values);
            $slide['image'] = $old[$i]['image'] ?? null;

            return $slide;
        }, ContentSeeder::HERO_SLIDES, array_keys(ContentSeeder::HERO_SLIDES));

        DB::table('page_sections')->where('id', $row->id)->update([
            'content' => json_encode($content, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
            'updated_at' => now(),
        ]);

        Cache::forget(\App\Models\PageSection::CACHE_KEY);
    }

    public function down(): void
    {
        // Content change only – nothing to roll back structurally.
    }
};
