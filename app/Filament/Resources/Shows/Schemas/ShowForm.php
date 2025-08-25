<?php

namespace App\Filament\Resources\Shows\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class ShowForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
                TextInput::make('slug')
                    ->required(),
                Textarea::make('description')
                    ->columnSpanFull(),
                TextInput::make('genre'),
                TextInput::make('network'),
                TextInput::make('meta_title'),
                TextInput::make('meta_description'),
            ]);
    }
}
