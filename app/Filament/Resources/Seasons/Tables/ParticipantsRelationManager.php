<?php

namespace App\Filament\Resources\Seasons\Tables;

use Filament\Forms;
use Filament\Schemas\Schema;   // form: Schema
use Filament\Tables;
use Filament\Tables\Table;     // table: Table
use Filament\Resources\RelationManagers\RelationManager;

// Filament v4 Actions
use Filament\Actions\CreateAction;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;

class ParticipantsRelationManager extends RelationManager
{
    protected static string $relationship = 'participants';
    protected static ?string $title = 'Teilnehmende';

    public function form(Schema $schema): Schema
    {
        return $schema->schema([
            Forms\Components\Select::make('candidate_id')->label('Kandidat:in')
                ->relationship('candidate', 'name')->searchable()->required(),
            Forms\Components\TextInput::make('role')->label('Rolle'),
            Forms\Components\TextInput::make('placement')->label('Platzierung'),
            Forms\Components\TextInput::make('prize_won')->label('Gewinn')->numeric(),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('candidate.name')->label('Kandidat:in')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('role')->label('Rolle'),
                Tables\Columns\TextColumn::make('placement')->label('Platzierung'),
                Tables\Columns\TextColumn::make('updated_at')->since()->label('Geändert')->sortable(),
            ])
            ->headerActions([
                CreateAction::make(), // Create im Relation-Header
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }
}
