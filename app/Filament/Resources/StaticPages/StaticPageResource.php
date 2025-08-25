<?php

namespace App\Filament\Resources\StaticPages;

use App\Filament\Resources\StaticPages\Pages;
use App\Models\StaticPage;
use Filament\Forms;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Illuminate\Support\Str;

// Filament 4 Actions
use Filament\Actions\ActionGroup;
use Filament\Actions\ViewAction;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkAction;
use Illuminate\Database\Eloquent\Collection;

class StaticPageResource extends Resource
{
    protected static ?string $model = StaticPage::class;

    protected static \UnitEnum|string|null   $navigationGroup = 'Inhalte';
    protected static ?string                 $navigationLabel = 'Statische Seiten';
    protected static \BackedEnum|string|null $navigationIcon  = 'heroicon-o-document-text';

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            Section::make('Seite')->schema([
                Forms\Components\TextInput::make('title')->label('Titel')->required()
                    ->live(onBlur: true)
                    ->afterStateUpdated(fn($state, callable $set) => $set('slug', Str::slug($state))),
                Forms\Components\TextInput::make('slug')->label('Slug')->required()->unique(ignoreRecord: true),

                Forms\Components\RichEditor::make('content')
                    ->label('Inhalt')
                    ->columns(20)
                    ->columnSpanFull()
                    ->toolbarButtons([
                        'bold','italic','underline','link','clearFormatting',
                        'h1','h2','h3','blockquote','orderedList','bulletList','codeBlock',
                    ]),

                Forms\Components\Toggle::make('show_in_nav')->label('In Hauptnavigation anzeigen')->default(true),
                Forms\Components\TextInput::make('nav_order')->label('Nav-Reihenfolge')->numeric()->minValue(0),
                Forms\Components\Toggle::make('published')->label('Veröffentlicht')->default(true),
            ])->columns(2),

            Section::make('SEO')->schema([
                Forms\Components\TextInput::make('meta_title')->label('Meta Title')->maxLength(70),
                Forms\Components\TextInput::make('meta_description')->label('Meta Description')->maxLength(160),
            ])->collapsible(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')->label('Titel')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('slug')->label('Slug')->copyable(),
                Tables\Columns\IconColumn::make('show_in_nav')->label('In Navi')->boolean(),
                Tables\Columns\TextColumn::make('nav_order')->label('Sort')->sortable(),
                Tables\Columns\IconColumn::make('published')->label('Live')->boolean(),
                Tables\Columns\TextColumn::make('updated_at')->since()->label('Geändert')->sortable(),
            ])
            ->recordActions([
                ActionGroup::make([
                    ViewAction::make(),
                    EditAction::make(),
                    DeleteAction::make(),
                ]),
            ])
            ->groupedBulkActions([
                BulkAction::make('delete')->label('Ausgewählte löschen')
                    ->requiresConfirmation()
                    ->action(fn (Collection $records) => $records->each->delete()),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListStaticPages::route('/'),
            'create' => Pages\CreateStaticPage::route('/create'),
            'edit'   => Pages\EditStaticPage::route('/{record}/edit'),
        ];
    }
}
