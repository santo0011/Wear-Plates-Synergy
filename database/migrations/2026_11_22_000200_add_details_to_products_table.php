<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->string('subtitle', 255)->nullable()->after('name');
            $table->text('highlights')->nullable()->after('description')->comment('One key feature per line');
            $table->text('specifications')->nullable()->after('highlights')->comment('One "Label | Value" per line');
            $table->text('data_table')->nullable()->after('specifications')->comment('Pipe-separated rows, first row = header');
            $table->string('data_table_title', 255)->nullable()->after('data_table');
            $table->text('applications')->nullable()->after('data_table_title')->comment('One application per line');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['subtitle', 'highlights', 'specifications', 'data_table', 'data_table_title', 'applications']);
        });
    }
};
