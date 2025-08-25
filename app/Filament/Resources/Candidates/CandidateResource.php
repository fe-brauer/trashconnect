<?php

namespace App\Filament\Resources\Candidates;

use App\Filament\Resources\Candidates\Pages;
use App\Filament\Resources\Candidates\Tables\ParticipantsRelationManager;
use App\Filament\Resources\Shared\SeoRelationManager;
use App\Models\Candidate;
use Filament\Forms;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Illuminate\Support\Str;

// ✅ NEU: Actions aus dem vereinheitlichten Namespace
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkAction;
use Illuminate\Database\Eloquent\Collection;

class CandidateResource extends Resource
{
    protected static ?string $model = Candidate::class;
    protected static \UnitEnum|string|null $navigationGroup = 'Inhalte';
    protected static ?string $navigationLabel = 'Kandidat:innen';
    protected static ?string $modelLabel = 'Kandidat:in';
    protected static ?string $pluralModelLabel = 'Kandidat:innen';
    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-user';

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            Section::make('Stammdaten')->schema([
                Forms\Components\TextInput::make('name')->label('Name')->required()->live(onBlur: true)
                    ->afterStateUpdated(fn($state, callable $set) => $set('slug', Str::slug($state))),
                Forms\Components\TextInput::make('slug')->label('Slug')->required()->unique(ignoreRecord: true),
                Forms\Components\DatePicker::make('birth_date')->label('Geburtsdatum'),
                Forms\Components\Textarea::make('bio')->label('Bio')->rows(4),
                Forms\Components\KeyValue::make('social_media')->label('Social Links')
                    ->keyLabel('Netzwerk')->valueLabel('URL')->reorderable(),
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
                Tables\Columns\TextColumn::make('name')->label('Name')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('slug')->label('Slug')->copyable(),
                Tables\Columns\TextColumn::make('participants_count')->counts('participants')->label('Auftritte'),
                Tables\Columns\TextColumn::make('updated_at')->since()->label('Geändert')->sortable(),
            ])

            // ✅ Row-Actions
            ->recordActions([
                ActionGroup::make([
                    ViewAction::make(),
                    EditAction::make(),
                    DeleteAction::make(),
                ]),
            ])

            // ✅ Bulk-Actions (vereinfacht – gruppiert geht auch)
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
            ParticipantsRelationManager::class,
            SeoRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListCandidates::route('/'),
            'create' => Pages\CreateCandidate::route('/create'),
            'edit'   => Pages\EditCandidate::route('/{record}/edit'),
        ];
    }
}
