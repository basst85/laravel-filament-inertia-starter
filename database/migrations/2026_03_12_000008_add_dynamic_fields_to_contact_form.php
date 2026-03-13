<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('contact_form_settings', function (Blueprint $table): void {
            $table->json('fields')->nullable()->after('intro_text');
        });

        Schema::table('contact_messages', function (Blueprint $table): void {
            $table->json('payload')->nullable()->after('message');
        });
    }

    public function down(): void
    {
        Schema::table('contact_messages', function (Blueprint $table): void {
            $table->dropColumn('payload');
        });

        Schema::table('contact_form_settings', function (Blueprint $table): void {
            $table->dropColumn('fields');
        });
    }
};
