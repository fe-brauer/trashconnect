<?php

namespace App\Filament\Resources\Participants;

use App\Filament\Resources\Participants\Pages;
use App\Models\Participant;
use App\Models\Show;
use Filament\Forms;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;   // form: Schema (Filament v4)
use Filament\Tables;
use Filament\Tables\Table;     // table: Table (Filament v4)
use Filament\Resources\Resource;

// Filament v4 Actions (vereinheitlicht)
use Filament\Actions\ActionGroup;
use Filament\Actions\ViewAction;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkAction;
use Illuminate\Database\Eloquent\Collection;

class ParticipantResource extends Resource
{
    protected static ?string $model = Participant::class;

    protected static \UnitEnum|string|null   $navigationGroup = 'Inhalte';
    protected static ?string                 $navigationLabel = 'Teilnahmen';
    protected static \BackedEnum|string|null $navigationIcon  = 'heroicon-o-user-group';

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            Forms\Components\Select::make('show_id')
                ->label('Show')
                ->options(fn () => Show::query()->orderBy('name')->pluck('name', 'id'))
                ->searchable()
                ->preload()
                ->required()
                ->live()                   // wichtig: triggert Update der Saison-Auswahl
                ->dehydrated(false)        // wird NICHT gespeichert
                ->default(fn (?Participant $record) => $record?->season?->show_id)
                ->afterStateUpdated(fn ($state, Set $set) => $set('season_id', null)),

            Forms\Components\Select::make('season_id')
                ->label('Staffel')
                ->relationship(
                    name: 'season',
                    titleAttribute: 'name',
                    modifyQueryUsing: function ($query, Get $get) {
                        $showId = $get('show_id');
                        if ($showId) {
                            $query->where('show_id', $showId);
                        } else {
                            // Keine Show gewählt => noch keine Staffeln anzeigen (klare UX)
                            $query->whereRaw('1 = 0');
                        }
                    }
                )
                ->searchable()
                ->preload()
                ->required()
                ->placeholder('Bitte zuerst Show wählen'),

            Forms\Components\Select::make('candidate_id')
                ->label('Kandidat:in')
                ->relationship('candidate', 'name')
                ->searchable()
                ->preload()
                ->required(),

            Forms\Components\TextInput::make('role')->label('Rolle'),
            Forms\Components\TextInput::make('placement')->label('Platzierung'),
            Forms\Components\TextInput::make('prize_won')->label('Gewinn')->numeric(),
        ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('season.show.name')->label('Show')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('season.name')->label('Staffel')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('candidate.name')->label('Kandidat:in')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('role')->label('Rolle'),
                Tables\Columns\TextColumn::make('placement')->label('Platzierung'),
                Tables\Columns\TextColumn::make('updated_at')->since()->label('Geändert')->sortable(),
            ])

            // ❌ KEINE headerActions() hier – Create-Button kommt aus der List-Page oben rechts

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

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListParticipants::route('/'),
            'create' => Pages\CreateParticipant::route('/create'),
            'edit'   => Pages\EditParticipant::route('/{record}/edit'),
        ];
    }
}
