<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('job_openings', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('department')->nullable();
            $table->string('location')->default('Remote — Worldwide');
            $table->string('employment_type')->default('full-time'); // full-time, part-time, contract, internship, freelance
            $table->string('experience_level')->nullable();
            $table->boolean('is_remote')->default(true);
            $table->string('salary_range')->nullable();
            $table->text('summary')->nullable();
            $table->text('description')->nullable();       // plain text, rendered with line breaks
            $table->text('responsibilities')->nullable();  // one item per line
            $table->text('requirements')->nullable();      // one item per line
            $table->string('apply_url')->nullable();       // external apply link; defaults to /contact
            $table->boolean('is_published')->default(true);
            $table->integer('sort_order')->default(0);
            $table->date('posted_at')->nullable();
            $table->date('closes_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('job_openings');
    }
};
