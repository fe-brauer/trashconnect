<?php

namespace App\Filament\Resources\Candidates\Tables;

use App\Models\Season;
use App\Models\Show;
use Filament\Forms;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Resources\RelationManagers\RelationManager;

// ✅ Actions unified
use Filament\Actions\CreateAction;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;

class ParticipantsRelationManager extends RelationManager
{
    protected static string $relationship = 'participants';
    protected static ?string $title = 'Auftritte';


    public function form(Schema $schema): Schema
    {
        return $schema->components([
            // 1) Show wählen (nur fürs Form – wird NICHT gespeichert)
            Forms\Components\Select::make('show_id')
                ->label('Show')
                ->options(fn () => Show::query()->orderBy('name')->pluck('name', 'id'))
                ->searchable()
                ->preload()
                ->live() // macht das Feld reaktiv
                ->dehydrated(false) // nicht in DB
                ->afterStateUpdated(fn (Set $set) => $set('season_id', null))
                ->afterStateHydrated(function (Set $set, $record) {
                    // beim Edit die zugehörige Show aus der Staffel vorauswählen
                    if ($record?->season?->show_id) {
                        $set('show_id', $record->season->show_id);
                    }
                })
                ->hint('Wähle zuerst eine Show – dann die Staffel.'),

            // 2) Staffel – abhängig von show_id
            Forms\Components\Select::make('season_id')
                ->label('Staffel')
                ->required()
                ->searchable()
                ->preload()
                ->options(function (Get $get) {
                    $showId = $get('show_id');
                    return $showId
                        ? Season::query()
                            ->where('show_id', $showId)
                            ->orderByRaw('COALESCE(year, 0) DESC')
                            ->orderBy('name')
                            ->pluck('name', 'id')
                        : [];
                })
                // Staffel on-the-fly anlegen – inkl. Show-Auswahl
                ->createOptionForm([
                    Forms\Components\Select::make('show_id')
                        ->label('Show')
                        ->options(Show::query()->orderBy('name')->pluck('name','id'))
                        ->required()
                        ->default(fn (Get $get) => $get('show_id')),
                    Forms\Components\TextInput::make('name')->label('Name')->required(),
                    Forms\Components\TextInput::make('year')->label('Jahr')->numeric()->minValue(1900)->maxValue(2100),
                    Forms\Components\TextInput::make('episode_count')->label('Episoden')->numeric()->minValue(0),
                ])
                ->createOptionUsing(function (array $data) {
                    return Season::create([
                        'show_id'       => $data['show_id'],
                        'name'          => $data['name'],
                        'year'          => $data['year'] ?? null,
                        'episode_count' => $data['episode_count'] ?? null,
                    ])->getKey();
                }),

            // (optional) weitere Felder
            Forms\Components\TextInput::make('role')->label('Rolle')->maxLength(100),
            Forms\Components\TextInput::make('placement')->label('Platzierung')->maxLength(100),
            Forms\Components\TextInput::make('prize_won')->label('Preis')->maxLength(100),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('season.show.name')->label('Show')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('season.name')->label('Staffel')->sortable()->searchable(),
                Tables\Columns\TextColumn::make('role')->label('Rolle'),
                Tables\Columns\TextColumn::make('placement')->label('Platzierung'),
            ])

            // ✅ Header: Create
            ->headerActions([
                CreateAction::make(),
            ])

            // ✅ Row actions
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }
}
