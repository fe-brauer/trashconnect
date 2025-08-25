<?php

namespace App\Filament\Resources\Seasons\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class SeasonForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('show_id')
                    ->required()
                    ->numeric(),
                TextInput::make('name')
                    ->required(),
                TextInput::make('slug')
                    ->required(),
                TextInput::make('year')
                    ->numeric(),
                TextInput::make('episode_count')
                    ->numeric(),
                TextInput::make('meta_title'),
                TextInput::make('meta_description'),
            ]);
    }
}
