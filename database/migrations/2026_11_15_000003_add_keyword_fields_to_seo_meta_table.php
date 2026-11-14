<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('seo_meta', function (Blueprint $table) {
            $table->string('secondary_keywords')->nullable()->after('focus_keyword');
            $table->string('search_intent')->nullable()->after('secondary_keywords');
        });
    }

    public function down(): void
    {
        Schema::table('seo_meta', function (Blueprint $table) {
            $table->dropColumn(['secondary_keywords', 'search_intent']);
        });
    }
};
