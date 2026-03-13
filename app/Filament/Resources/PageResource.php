<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PageResource\Pages;
use App\Models\Page;
use Filament\Actions;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class PageResource extends Resource
{
    protected static ?string $model = Page::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationLabel = 'Pages';

    protected static string|\UnitEnum|null $navigationGroup = 'CMS';

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Page')
                ->schema([
                    TextInput::make('title')
                        ->label('Title')
                        ->required()
                        ->maxLength(255)
                        ->live(onBlur: true)
                        ->afterStateUpdated(function ($state, callable $set, callable $get): void {
                            if (filled($get('slug'))) {
                                return;
                            }

                            $set('slug', Str::slug((string) $state));
                        }),

                    TextInput::make('description')
                        ->label('Description')
                        ->maxLength(255)
                        ->helperText('Used as the meta description tag.'),

                    TextInput::make('slug')
                        ->required()
                        ->maxLength(255)
                        ->unique(ignoreRecord: true)
                        ->helperText('For example: about-us or contact'),

                    Toggle::make('is_homepage')
                        ->label('Use as homepage'),

                    Toggle::make('is_published')
                        ->label('Published')
                        ->default(true),

                    RichEditor::make('content')
                        ->label('Content')
                        ->toolbarButtons([
                            'blockquote',
                            'bold',
                            'bulletList',
                            'codeBlock',
                            'h2',
                            'h3',
                            'italic',
                            'link',
                            'orderedList',
                            'redo',
                            'strike',
                            'underline',
                            'undo',
                        ])
                        ->columnSpanFull(),
                ])
                ->columns(2)
                ->columnSpanFull(),

            Section::make('Image slider')
                ->schema([
                    Repeater::make('slides')
                        ->relationship()
                        ->orderColumn('sort')
                        ->reorderable()
                        ->cloneable()
                        ->collapsible()
                        ->schema([
                            FileUpload::make('image_path')
                                ->label('Image')
                                ->image()
                                ->disk('public')
                                ->directory('pages/slides')
                                ->visibility('public')
                                ->required(),

                            TextInput::make('alt_text')
                                ->label('Alt text')
                                ->maxLength(255),

                            TextInput::make('caption')
                                ->label('Caption')
                                ->maxLength(255),
                        ])
                        ->defaultItems(0)
                        ->columnSpanFull(),
                ])
                ->columnSpanFull(),

            Section::make('Hero')
                ->description('Shown on the page when at least one hero field is filled.')
                ->schema([
                    TextInput::make('hero_title')
                        ->label('Hero title')
                        ->maxLength(255),

                    TextInput::make('hero_text')
                        ->label('Hero text')
                        ->maxLength(255),

                    FileUpload::make('hero_image_path')
                        ->label('Hero background image')
                        ->image()
                        ->disk('public')
                        ->directory('pages/heroes')
                        ->visibility('public')
                        ->columnSpanFull(),

                    TextInput::make('hero_button_label')
                        ->label('Hero button label')
                        ->maxLength(100),

                    TextInput::make('hero_button_url')
                        ->label('Hero button URL')
                        ->maxLength(255)
                        ->helperText('Example: /contact or https://example.com'),
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
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('slug')
                    ->label('Slug')
                    ->searchable(),

                Tables\Columns\IconColumn::make('is_homepage')
                    ->label('Homepage')
                    ->boolean(),

                Tables\Columns\IconColumn::make('is_published')
                    ->label('Published')
                    ->boolean(),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Updated At')
                    ->since()
                    ->sortable(),
            ])
            ->defaultSort('updated_at', 'desc')
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
            'index' => Pages\ListPages::route('/'),
            'create' => Pages\CreatePage::route('/create'),
            'edit' => Pages\EditPage::route('/{record}/edit'),
        ];
    }
}
