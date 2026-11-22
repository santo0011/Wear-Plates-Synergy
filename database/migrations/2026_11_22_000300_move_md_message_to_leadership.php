<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

/**
 * The "Message from the Managing Director" section moved from the About Us page
 * to the Leadership page. Move the saved row so edited text and the uploaded photo are kept.
 */
return new class extends Migration
{
    public function up(): void
    {
        $this->move('about', 'leadership');
    }

    public function down(): void
    {
        $this->move('leadership', 'about');
    }

    private function move(string $from, string $to): void
    {
        $exists = DB::table('page_sections')->where(['page' => $to, 'section' => 'md_message'])->exists();

        if (! $exists) {
            DB::table('page_sections')
                ->where(['page' => $from, 'section' => 'md_message'])
                ->update(['page' => $to, 'updated_at' => now()]);
        }

        Cache::forget(\App\Models\PageSection::CACHE_KEY);
    }
};
