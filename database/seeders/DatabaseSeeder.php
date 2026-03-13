<?php

namespace Database\Seeders;

use App\Models\Page;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        Page::query()->firstOrCreate(
            ['slug' => 'home'],
            [
                'title' => 'Home',
                'description' => 'Welcome to the homepage',
                'content' => '<h2>Welcome</h2><p>Customize this content via Filament CMS.</p>',
                'is_homepage' => true,
                'is_published' => true,
            ]
        );

        Page::query()->firstOrCreate(
            ['slug' => 'about-us'],
            [
                'title' => 'About Us',
                'description' => 'Example page',
                'content' => '<h2>About Us</h2><p>This is an example page.</p>',
                'is_homepage' => false,
                'is_published' => true,
            ]
        );
    }
}
