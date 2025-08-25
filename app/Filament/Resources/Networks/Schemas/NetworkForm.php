<?php

namespace App\Filament\Resources\Networks\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class NetworkForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
                TextInput::make('slug')
                    ->required(),
                TextInput::make('url'),
                TextInput::make('logo_path'),
            ]);
    }
}
