<?php

namespace App\Filament\Resources\Updates;

use App\Filament\Resources\Updates\Pages\CreateUpdate;
use App\Filament\Resources\Updates\Pages\EditUpdate;
use App\Filament\Resources\Updates\Pages\ListUpdates;
use App\Support\Html;
use Filament\Forms;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\ToggleButtons;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Builder as EloquentBuilder;
use Filament\Actions\{ActionGroup, EditAction, DeleteAction};
use Filament\Forms\Components\Builder;
use Filament\Forms\Components\Builder\Block;

class UpdateResource extends Resource
{
    protected static ?string $model = \App\Models\Update::class;
    protected static \UnitEnum|string|null $navigationGroup = 'Inhalte';
    protected static ?string $navigationLabel = 'Updates';
    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-megaphone';

    public static function getEloquentQuery(): EloquentBuilder
    {
        return parent::getEloquentQuery()
            ->orderByDesc('is_pinned')
            ->orderByDesc('published_at')
            ->orderByDesc('id');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Inhalt')->schema([
                Forms\Components\TextInput::make('title')
                    ->label('Titel')->required()->live(onBlur: true)
                    ->afterStateUpdated(fn($state, $set) => $set('slug', \Illuminate\Support\Str::slug($state))),
                Forms\Components\TextInput::make('slug')
                    ->label('Slug')->required()->unique(ignoreRecord: true),

                Forms\Components\TextInput::make('excerpt')
                    ->label('Kurzbeschreibung')->maxLength(250),

                Forms\Components\RichEditor::make('content')
                    ->label('Inhalt (RTE)')
                    ->toolbarButtons([
                        'bold','italic','underline','strike','link',
                        'h2','h3','blockquote','orderedList','bulletList','table','codeBlock',
                    ])
                    ->dehydrated(false),

                // Hidden Field für die eigentliche DB-Spalte
                Forms\Components\Hidden::make('content'),
            ]),

            Section::make('Kategorisierung')->schema([
                Forms\Components\Select::make('kind')->label('Art')
                    ->options([
                        'news'    => 'News',
                        'bug'     => 'Bug',
                        'change'  => 'Change',
                        'feature' => 'Feature',
                    ])->default('news'),

                Forms\Components\TextInput::make('status')->label('Status')->placeholder('z. B. planned, fixed …'),

                // Shows als Tags (verlinkte Entities)
                Forms\Components\Select::make('shows')
                    ->label('Shows (Tags)')
                    ->relationship('shows', 'name')
                    ->multiple()->preload()->searchable(),

                // Freie Tags mit Inline-Erstellung
                Forms\Components\Select::make('tags')
                    ->label('Freie Tags')
                    ->relationship('tags', 'name')
                    ->multiple()->preload()->searchable()
                    ->createOptionForm([
                        Forms\Components\TextInput::make('name')->required()
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn($state,$set)=>$set('slug', Str::slug($state))),
                        Forms\Components\TextInput::make('slug')->required()->unique(table: 'tags', ignoreRecord: true),
                        Forms\Components\TextInput::make('color')->placeholder('#7c4cff oder text-tv-violet'),
                    ])
                    ->editOptionForm([
                        Forms\Components\TextInput::make('name')->required(),
                        Forms\Components\TextInput::make('slug')->required()->unique(table:'tags', ignoreRecord: true),
                        Forms\Components\TextInput::make('color')->placeholder('#7c4cff oder text-tv-violet'),
                    ]),
            ])->columns(3),

            Section::make('Medien & SEO')->schema([
                Forms\Components\TextInput::make('meta_title')->label('Meta Title')->maxLength(70),
                Forms\Components\TextInput::make('meta_description')->label('Meta Description')->maxLength(160),
            ])->columns(2),

            Section::make('Veröffentlichung')->schema([
                Forms\Components\DatePicker::make('published_at')->label('Veröffentlicht am')->seconds(false),
                Forms\Components\Toggle::make('is_public')->label('Öffentlich')->default(true),
                Forms\Components\Toggle::make('is_pinned')->label('Angepinnt')->default(false),
            ])->columns(3),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')->label('Titel')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('kind')->label('Art')->badge(),
                Tables\Columns\TextColumn::make('published_at')->label('Datum')->date('d.m.Y')->sortable(),
                Tables\Columns\IconColumn::make('is_pinned')->label('Pin')->boolean(),
                Tables\Columns\IconColumn::make('is_public')->label('Öffentlich')->boolean(),
            ])
            ->recordActions([ ActionGroup::make([ EditAction::make(), DeleteAction::make() ]) ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListUpdates::route('/'),
            'create' => CreateUpdate::route('/create'),
            'edit'   => EditUpdate::route('/{record}/edit'),
        ];
    }
}
