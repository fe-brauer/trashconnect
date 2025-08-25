<?php

namespace App\Filament\Resources\Shared;

use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Resources\RelationManagers\RelationManager;

// ✅ Actions unified
use Filament\Actions\CreateAction;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;

class SeoRelationManager extends RelationManager
{
    protected static string $relationship = 'seo';
    protected static ?string $title = 'SEO';

    public function form(Schema $schema): Schema
    {
        return $schema->schema([
            Forms\Components\TextInput::make('title')->label('SEO Title')->maxLength(70),
            Forms\Components\TextInput::make('description')->label('SEO Description')->maxLength(160),
            Forms\Components\TextInput::make('keywords')->label('Keywords (kommasepariert)'),
            Forms\Components\Textarea::make('schema_markup')->label('Schema.org JSON-LD')->rows(8),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')->label('Title'),
                Tables\Columns\TextColumn::make('description')->label('Description')->limit(60),
                Tables\Columns\TextColumn::make('updated_at')->since()->label('Geändert')->sortable(),
            ])

            // Bei morphOne nur 1 Datensatz zulassen:
            ->headerActions([
                CreateAction::make()
                    ->visible(fn () => $this->getOwnerRecord()->seo()->doesntExist()),
            ])

            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }
}
