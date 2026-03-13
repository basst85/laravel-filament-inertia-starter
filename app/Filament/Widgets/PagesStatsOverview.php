<?php

namespace App\Filament\Widgets;

use App\Models\Page;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class PagesStatsOverview extends StatsOverviewWidget
{
    protected ?string $heading = 'Page stats';

    protected function getStats(): array
    {
        $total = Page::query()->count();
        $published = Page::query()->where('is_published', true)->count();
        $draft = Page::query()->where('is_published', false)->count();

        return [
            Stat::make('Total Pages', $total)
                ->description('All pages in CMS')
                ->color('primary'),

            Stat::make('Live Pages', $published)
                ->description('Publicly visible')
                ->color('success'),

            Stat::make('Draft Pages', $draft)
                ->description('Not yet published')
                ->color('warning'),
        ];
    }
}
