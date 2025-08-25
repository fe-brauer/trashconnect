<?php

namespace App\Filament\Resources\Candidates\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class CandidateForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
                TextInput::make('slug')
                    ->required(),
                Textarea::make('bio')
                    ->columnSpanFull(),
                DatePicker::make('birth_date'),
                Textarea::make('social_media')
                    ->columnSpanFull(),
                TextInput::make('meta_title'),
                TextInput::make('meta_description'),
            ]);
    }
}
