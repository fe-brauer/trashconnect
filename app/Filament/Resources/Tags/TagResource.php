<?php

namespace App\Filament\Resources\Tags;

use App\Filament\Resources\Tags\Pages\CreateTag;
use App\Filament\Resources\Tags\Pages\EditTag;
use App\Filament\Resources\Tags\Pages\ListTags;
use App\Filament\Resources\Tags\Schemas\TagForm;
use App\Filament\Resources\Tags\Tables\TagsTable;
use App\Models\Tag;
use BackedEnum;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class TagResource extends Resource
{
    protected static ?string $model = Tag::class;
    protected static \UnitEnum|string|null $navigationGroup = 'Inhalte';
    protected static ?string $navigationLabel = 'Tags';
    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-tag';

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make()->schema([
                TextInput::make('name')->required()
                    ->live(onBlur: true)
                    ->afterStateUpdated(fn($state,$set)=>$set('slug', Str::slug($state))),
                TextInput::make('slug')->required()->unique(ignoreRecord: true),
                TextInput::make('color')->placeholder('#7c4cff oder text-tv-violet'),
            ])->columns(3),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('name')->searchable()->sortable(),
            TextColumn::make('slug')->copyable(),
            TextColumn::make('color')->label('Farbe')->limit(20),
            TextColumn::make('updates_count')->counts('updates')->label('Updates'),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListTags::route('/'),
            'create' => CreateTag::route('/create'),
            'edit'   => EditTag::route('/{record}/edit'),
        ];
    }
}
