<?php

namespace App\Filament\Resources\Participants\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class ParticipantForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('season_id')
                    ->required()
                    ->numeric(),
                TextInput::make('candidate_id')
                    ->required()
                    ->numeric(),
                TextInput::make('role'),
                TextInput::make('placement'),
                TextInput::make('prize_won')
                    ->numeric(),
            ]);
    }
}
