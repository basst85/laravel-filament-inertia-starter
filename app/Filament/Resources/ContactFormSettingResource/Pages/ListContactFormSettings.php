<?php

namespace App\Filament\Resources\ContactFormSettingResource\Pages;

use App\Filament\Resources\ContactFormSettingResource;
use App\Models\ContactFormSetting;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListContactFormSettings extends ListRecords
{
    protected static string $resource = ContactFormSettingResource::class;

    protected function getHeaderActions(): array
    {
        if (ContactFormSetting::query()->exists()) {
            return [];
        }

        return [
            Actions\CreateAction::make(),
        ];
    }
}
