<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contact_form_settings', function (Blueprint $table): void {
            $table->id();
            $table->string('title')->default('Contact us');
            $table->text('intro_text')->nullable();
            $table->string('name_label')->default('Your name');
            $table->string('email_label')->default('Email address');
            $table->string('message_label')->default('Message');
            $table->string('button_label')->default('Send message');
            $table->string('success_toast')->default('Your message has been sent successfully.');
            $table->string('error_toast')->default('Something went wrong while sending your message.');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contact_form_settings');
    }
};
