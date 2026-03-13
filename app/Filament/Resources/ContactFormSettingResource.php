<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ContactFormSettingResource\Pages;
use App\Models\ContactFormSetting;
use Filament\Actions;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class ContactFormSettingResource extends Resource
{
    protected static ?string $model = ContactFormSetting::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-envelope';

    protected static ?string $navigationLabel = 'Contact';

    protected static string|\UnitEnum|null $navigationGroup = 'CMS';

    protected static ?int $navigationSort = 3;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Text above form')
                ->schema([
                    TextInput::make('title')
                        ->label('Title')
                        ->required()
                        ->maxLength(120),

                    Textarea::make('intro_text')
                        ->label('Intro text')
                        ->rows(3),
                ])
                ->columns(1)
                ->columnSpanFull(),

            Section::make('Form fields')
                ->schema([
                    Repeater::make('fields')
                        ->label('Fields')
                        ->default([
                            [
                                'key' => 'name',
                                'label' => 'Your name',
                                'type' => 'text',
                                'required' => true,
                                'placeholder' => '',
                            ],
                            [
                                'key' => 'email',
                                'label' => 'Email address',
                                'type' => 'email',
                                'required' => true,
                                'placeholder' => '',
                            ],
                            [
                                'key' => 'message',
                                'label' => 'Message',
                                'type' => 'textarea',
                                'required' => true,
                                'placeholder' => '',
                            ],
                        ])
                        ->schema([
                            TextInput::make('key')
                                ->label('Field key')
                                ->helperText('Unique key, for example: name, email, company, message.')
                                ->required()
                                ->maxLength(50)
                                ->regex('/^[a-z0-9_]+$/'),

                            TextInput::make('label')
                                ->label('Label')
                                ->required()
                                ->maxLength(100),

                            Select::make('type')
                                ->label('Type')
                                ->options([
                                    'text' => 'Text',
                                    'email' => 'Email',
                                    'textarea' => 'Textarea',
                                    'tel' => 'Telephone',
                                ])
                                ->required(),

                            Toggle::make('required')
                                ->label('Required')
                                ->default(false),

                            TextInput::make('placeholder')
                                ->label('Placeholder')
                                ->maxLength(120)
                                ->columnSpanFull(),
                        ])
                        ->columns(2)
                        ->reorderable()
                        ->collapsible()
                        ->cloneable()
                        ->columnSpanFull(),
                ])
                ->columns(1)
                ->columnSpanFull(),

            Section::make('Button')
                ->schema([
                    TextInput::make('button_label')
                        ->label('Button text')
                        ->required()
                        ->maxLength(100),
                ])
                ->columns(1)
                ->columnSpanFull(),

            Section::make('Toast messages')
                ->schema([
                    TextInput::make('success_toast')
                        ->label('Success toast')
                        ->required()
                        ->maxLength(160),

                    TextInput::make('error_toast')
                        ->label('Error toast')
                        ->required()
                        ->maxLength(160),
                ])
                ->columns(2)
                ->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label('Title')
                    ->searchable(),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Last updated')
                    ->dateTime()
                    ->since()
                    ->sortable(),
            ])
            ->defaultSort('updated_at', 'desc')
            ->actions([
                Actions\EditAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListContactFormSettings::route('/'),
            'create' => Pages\CreateContactFormSetting::route('/create'),
            'edit' => Pages\EditContactFormSetting::route('/{record}/edit'),
        ];
    }
}
