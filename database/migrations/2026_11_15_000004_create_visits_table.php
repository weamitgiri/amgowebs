<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('visits', function (Blueprint $table) {
            $table->id();
            $table->char('country', 2)->nullable()->index();   // ISO-3166 alpha-2
            $table->string('country_name')->nullable();
            $table->string('path', 512)->nullable();
            $table->string('referrer_host')->nullable();
            $table->string('device', 20)->nullable();           // mobile / tablet / desktop
            $table->string('visitor_hash', 64)->nullable()->index(); // daily-salted IP hash (no raw IP stored)
            $table->timestamp('created_at')->nullable()->index();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('visits');
    }
};
