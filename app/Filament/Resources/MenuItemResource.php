<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MenuItemResource\Pages;
use App\Models\MenuItem;
use Filament\Actions;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class MenuItemResource extends Resource
{
    protected static ?string $model = MenuItem::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-bars-3';

    protected static ?string $navigationLabel = 'Menu-items';

    protected static string|\UnitEnum|null $navigationGroup = 'CMS';

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Menu item')
                ->schema([
                    TextInput::make('label')
                        ->label('Label')
                        ->required()
                        ->maxLength(100),

                    Select::make('page_id')
                        ->label('Page (optional)')
                        ->relationship('page', 'title')
                        ->searchable()
                        ->preload(),

                    Select::make('named_route')
                        ->label('Internal route (optional)')
                        ->options([
                            'contact' => 'Contact',
                        ])
                        ->helperText('Use this for routes that are not CMS pages.'),

                    TextInput::make('url')
                        ->label('External or manual URL (optional)')
                        ->helperText('Use this for external links or custom paths. Leave empty to use the selected page.')
                        ->maxLength(255),

                    TextInput::make('sort')
                        ->label('Order')
                        ->numeric()
                        ->default(0)
                        ->required(),

                    Toggle::make('is_visible')
                        ->label('Visible')
                        ->default(true),

                    Toggle::make('open_in_new_tab')
                        ->label('Open in new tab')
                        ->default(false),
                ])
                ->columns(2)
                ->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('label')
                    ->label('Label')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('page.title')
                    ->label('Page')
                    ->toggleable(),

                Tables\Columns\TextColumn::make('named_route')
                    ->label('Route')
                    ->formatStateUsing(fn (?string $state): string => match ($state) {
                        'contact' => 'Contact',
                        default => '-',
                    })
                    ->toggleable(),

                Tables\Columns\TextColumn::make('url')
                    ->label('URL')
                    ->limit(40)
                    ->toggleable(),

                Tables\Columns\TextColumn::make('sort')
                    ->label('Order')
                    ->sortable(),

                Tables\Columns\IconColumn::make('is_visible')
                    ->label('Visible')
                    ->boolean(),

                Tables\Columns\IconColumn::make('open_in_new_tab')
                    ->label('Open in new tab')
                    ->boolean()
                    ->toggleable(),
            ])
            ->defaultSort('sort', 'asc')
            ->actions([
                Actions\EditAction::make(),
            ])
            ->bulkActions([
                Actions\BulkActionGroup::make([
                    Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMenuItems::route('/'),
            'create' => Pages\CreateMenuItem::route('/create'),
            'edit' => Pages\EditMenuItem::route('/{record}/edit'),
        ];
    }
}
