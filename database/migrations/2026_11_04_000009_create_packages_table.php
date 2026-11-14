<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('packages', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->decimal('price_usd', 10, 2)->nullable();        // null when custom pricing
            $table->decimal('original_price', 10, 2)->nullable();
            $table->unsignedTinyInteger('discount_percent')->nullable();
            $table->boolean('is_custom_pricing')->default(false);
            $table->string('short_description')->nullable();
            $table->longText('description')->nullable();
            $table->unsignedSmallInteger('pages_included')->nullable();
            $table->unsignedSmallInteger('delivery_days')->nullable();
            $table->unsignedSmallInteger('support_months')->nullable();
            $table->unsignedSmallInteger('revisions')->nullable();
            $table->string('icon')->nullable();
            $table->string('image')->nullable();
            $table->string('cta_label')->default('Get Started');
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_active')->default(true);
            $table->string('status')->default('published')->index(); // draft, published
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('packages');
    }
};
