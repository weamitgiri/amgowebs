<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('package_inquiries', function (Blueprint $table) {
            $table->id();
            $table->string('reference')->unique();
            $table->foreignId('package_id')->nullable()->constrained('packages')->nullOnDelete();
            $table->string('name');
            $table->string('business_name')->nullable();
            $table->string('email');
            $table->string('country')->nullable();
            $table->string('phone')->nullable();
            $table->string('website_type')->nullable();
            $table->string('pages_required')->nullable();
            $table->string('existing_url')->nullable();
            $table->longText('description')->nullable();
            $table->text('required_features')->nullable();
            $table->dateTime('preferred_meeting_at')->nullable();
            $table->string('budget')->nullable();
            $table->text('additional_requirements')->nullable();
            $table->string('status')->default('new')->index(); // new, under_review, meeting_scheduled, proposal_sent, converted, rejected, closed
            $table->string('source_page')->nullable();
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('follow_up_at')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('package_inquiries');
    }
};
