<?php

namespace App\Filament\Resources\Candidates\Tables;

use Filament\Forms;
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
        return $schema->schema([
            Forms\Components\Select::make('season_id')->label('Staffel')
                ->relationship('season', 'name')->searchable()->required(),
            Forms\Components\TextInput::make('role')->label('Rolle'),
            Forms\Components\TextInput::make('placement')->label('Platzierung'),
            Forms\Components\TextInput::make('prize_won')->label('Gewinn')->numeric(),
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
