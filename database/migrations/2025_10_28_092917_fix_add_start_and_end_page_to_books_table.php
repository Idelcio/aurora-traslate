<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('books', function (Blueprint $table) {
            if (!Schema::hasColumn('books', 'start_page')) {
                $table->integer('start_page')->nullable()->after('total_pages');
            }
            if (!Schema::hasColumn('books', 'end_page')) {
                $table->integer('end_page')->nullable()->after('start_page');
            }
            // Remove max_pages if it exists
            if (Schema::hasColumn('books', 'max_pages')) {
                $table->dropColumn('max_pages');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('books', function (Blueprint $table) {
            $table->dropColumn(['start_page', 'end_page']);
            $table->integer('max_pages')->nullable()->after('total_pages');
        });
    }
};
