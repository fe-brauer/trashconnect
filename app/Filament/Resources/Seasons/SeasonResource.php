<?php

namespace App\Filament\Resources\Seasons;

use App\Filament\Resources\Seasons\Pages;
use App\Filament\Resources\Seasons\Tables\ParticipantsRelationManager;
use App\Filament\Resources\Shared\SeoRelationManager;
use App\Models\Season;
use Filament\Forms;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;   // form: Schema
use Filament\Tables;
use Filament\Tables\Table;     // table: Table
use Filament\Resources\Resource;
use Illuminate\Support\Str;

// Filament v4 Actions
use Filament\Actions\ActionGroup;
use Filament\Actions\ViewAction;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkAction;
use Illuminate\Database\Eloquent\Collection;

class SeasonResource extends Resource
{
    protected static ?string $model = Season::class;

    // Typen so wählen, dass sie zum Parent passen (oder einfach untypisiert lassen)
    protected static \UnitEnum|string|null $navigationGroup = 'Inhalte';
    protected static ?string $navigationLabel = 'Staffeln';
    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-queue-list';

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            Forms\Components\Select::make('show_id')->label('Show')
                ->relationship('show','name')->searchable()->required(),

            Forms\Components\TextInput::make('name')->label('Name')->required()->live(onBlur: true)
                ->afterStateUpdated(fn($state, callable $set) => $set('slug', Str::slug($state))),

            Forms\Components\TextInput::make('slug')->label('Slug')->required()->unique(ignoreRecord: true, table: 'seasons'),

            Forms\Components\TextInput::make('year')->label('Jahr')->numeric()->minValue(1900)->maxValue(2100),
            Forms\Components\TextInput::make('episode_count')->label('Folgen')->numeric()->minValue(0),

            Section::make('SEO')->schema([
                Forms\Components\TextInput::make('meta_title')->label('Meta Title')->maxLength(70),
                Forms\Components\TextInput::make('meta_description')->label('Meta Description')->maxLength(160),
            ])->collapsible(),
        ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('show.name')->label('Show')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('name')->label('Staffel')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('slug')->label('Slug')->copyable(),
                Tables\Columns\TextColumn::make('year')->label('Jahr')->sortable(),
                Tables\Columns\TextColumn::make('episode_count')->label('Folgen'),
                Tables\Columns\TextColumn::make('updated_at')->since()->label('Geändert')->sortable(),
            ])

            // ❌ keine headerActions() hier – sonst doppelter „New“-Button

            ->recordActions([
                ActionGroup::make([
                    ViewAction::make(),
                    EditAction::make(),
                    DeleteAction::make(),
                ]),
            ])

            ->groupedBulkActions([
                BulkAction::make('delete')
                    ->label('Ausgewählte löschen')
                    ->requiresConfirmation()
                    ->action(fn (Collection $records) => $records->each->delete()),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            ParticipantsRelationManager::class, // Teilnehmende der Staffel
            SeoRelationManager::class,          // morphOne SEO
        ];
    }

    public static function getPages(): array
    {
        return [
            // Create-Button kommt automatisch aus der List-Page (oben rechts)
            'index'  => Pages\ListSeasons::route('/'),
            'create' => Pages\CreateSeason::route('/create'),
            'edit'   => Pages\EditSeason::route('/{record}/edit'),
        ];
    }
}
