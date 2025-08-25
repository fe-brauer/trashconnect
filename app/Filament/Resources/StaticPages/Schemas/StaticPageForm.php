<?php

namespace App\Filament\Resources\StaticPages\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class StaticPageForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('title')
                    ->required(),
                TextInput::make('slug')
                    ->required(),
                Textarea::make('content')
                    ->columnSpanFull(),
                Toggle::make('show_in_nav')
                    ->required(),
                TextInput::make('nav_order')
                    ->numeric(),
                Toggle::make('published')
                    ->required(),
                TextInput::make('meta_title'),
                TextInput::make('meta_description'),
            ]);
    }
}
