<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pages', function (Blueprint $table): void {
            $table->string('hero_title')->nullable()->after('description');
            $table->text('hero_text')->nullable()->after('hero_title');
            $table->string('hero_image_path')->nullable()->after('hero_text');
            $table->string('hero_button_label')->nullable()->after('hero_image_path');
            $table->string('hero_button_url')->nullable()->after('hero_button_label');
        });
    }

    public function down(): void
    {
        Schema::table('pages', function (Blueprint $table): void {
            $table->dropColumn([
                'hero_title',
                'hero_text',
                'hero_image_path',
                'hero_button_label',
                'hero_button_url',
            ]);
        });
    }
};
