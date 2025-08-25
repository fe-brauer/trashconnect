<?php

namespace App\Filament\Resources\Shows\Tables;

use Filament\Forms;
use Filament\Schemas\Schema;   // form: Schema
use Filament\Tables;
use Filament\Tables\Table;     // table: Table
use Filament\Resources\RelationManagers\RelationManager;
use Illuminate\Support\Str;

// Filament v4 Actions
use Filament\Actions\CreateAction;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;

class SeasonsRelationManager extends RelationManager
{
    protected static string $relationship = 'seasons';
    protected static ?string $title = 'Staffeln';

    public function form(Schema $schema): Schema
    {
        return $schema->schema([
            Forms\Components\TextInput::make('name')->label('Name')->required()->live(onBlur: true)
                ->afterStateUpdated(fn($state, callable $set) => $set('slug', Str::slug($state))),
            Forms\Components\TextInput::make('slug')->label('Slug')->required()->unique(ignoreRecord: true, table: 'seasons'),
            Forms\Components\TextInput::make('year')->label('Jahr')->numeric()->minValue(1900)->maxValue(2100),
            Forms\Components\TextInput::make('episode_count')->label('Folgen')->numeric()->minValue(0),
            Forms\Components\TextInput::make('meta_title')->label('Meta Title')->maxLength(70),
            Forms\Components\TextInput::make('meta_description')->label('Meta Description')->maxLength(160),
        ])->columns(2);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->label('Name')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('slug')->label('Slug')->copyable(),
                Tables\Columns\TextColumn::make('year')->label('Jahr')->sortable(),
                Tables\Columns\TextColumn::make('episode_count')->label('Folgen'),
            ])

            // ✅ Create-Button hier ist korrekt (Relation-Header)
            ->headerActions([
                CreateAction::make(),
            ])

            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }
}
