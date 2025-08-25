<?php

namespace App\Filament\Resources\Shows;

use App\Filament\Resources\Shows\Pages;
use App\Filament\Resources\Shows\Tables\SeasonsRelationManager;
use App\Filament\Resources\Shared\SeoRelationManager;
use App\Models\Show;
use Filament\Forms;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;          // form: Schema
use Filament\Tables;
use Filament\Tables\Table;            // table: Table
use Filament\Resources\Resource;
use Illuminate\Support\Str;

// Filament v4 Actions
use Filament\Actions\ActionGroup;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkAction;
use Illuminate\Database\Eloquent\Collection;

class ShowResource extends Resource
{
    protected static ?string $model = Show::class;

    protected static \UnitEnum|string|null $navigationGroup = 'Inhalte';
    protected static ?string $navigationLabel = 'Shows';
    protected static \BackedEnum|string|null $navigationIcon  = 'heroicon-o-rectangle-stack';

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            Section::make('Show')->schema([
                Forms\Components\TextInput::make('name')->label('Name')->required()->live(onBlur: true)
                    ->afterStateUpdated(fn($state, callable $set) => $set('slug', Str::slug($state))),
                Forms\Components\TextInput::make('slug')->label('Slug')->required()->unique(ignoreRecord: true),
                Forms\Components\TextInput::make('genre')->label('Genre'),
                Forms\Components\Select::make('network_id')
                    ->label('Sender/Plattform')
                    ->relationship('network', 'name')
                    ->searchable()->preload()
                    ->createOptionForm([
                        Forms\Components\TextInput::make('name')->label('Name')->required()
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn($state, callable $set) => $set('slug', \Illuminate\Support\Str::slug($state))),
                        Forms\Components\TextInput::make('slug')->label('Slug')->required()->unique(table: 'networks', ignoreRecord: true),
                        Forms\Components\TextInput::make('url')->label('Website')->url()->maxLength(2048),
                        Forms\Components\FileUpload::make('logo_path')->label('Logo')
                            ->directory('networks/logos')->disk('public')->visibility('public')
                            ->acceptedFileTypes(['image/svg+xml','image/svg','image/png','image/webp'])
                            ->imageEditor(false)->maxSize(2048),
                    ])
                    ->editOptionForm([
                        Forms\Components\TextInput::make('name')->label('Name')->required(),
                        Forms\Components\TextInput::make('slug')->label('Slug')->required()->unique(table: 'networks', ignoreRecord: true),
                        Forms\Components\TextInput::make('url')->label('Website')->url(fn ($state) => filled($state) ? $state : null)->maxLength(2048),
                        Forms\Components\FileUpload::make('logo_path')->label('Logo')
                            ->directory('networks/logos')->disk('public')->visibility('public')
                            ->acceptedFileTypes(['image/svg+xml','image/svg','image/png','image/webp'])
                            ->imageEditor(false)->maxSize(2048),
                    ]),

                Forms\Components\TextInput::make('streaming_url')
                    ->label('Streaming-Link')
                    ->placeholder('https://…')
                    ->url()                 // validiert auf URL
                    ->maxLength(2048),
                Forms\Components\Textarea::make('description')->label('Beschreibung')->rows(5),
            ])->columns(2),

            Section::make('Logo')->schema([
                Forms\Components\FileUpload::make('logo_path')
                    ->label('Offizielles Logo')
                    // Wichtig: NICHT ->image() benutzen, wenn du SVG zulassen willst!
                    ->acceptedFileTypes(['image/svg+xml','image/png','image/webp', 'image/avif'])
                    ->maxSize(2048)
                    ->fetchFileInformation(false)// 2MB
                    ->directory('shows/logos')     // storage/app/public/shows/logos
                    ->disk('public')               // nutzt public disk
                    ->visibility('public')
                    ->previewable(true)
                    ->imageEditor(false)           // Editor aus, SVG mag den Editor nicht
                    ->imageResizeMode(null)        // keine Resize-Versuche bei SVG
                    ->getUploadedFileNameForStorageUsing(function ($file) {
                        $ext  = $file->getClientOriginalExtension();
                        $base = \Illuminate\Support\Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME));
                        return $base.'-'.\Illuminate\Support\Str::random(6).'.'.$ext;
                    })
                    ->helperText('PNG/WebP/SVG, max. 2 MB'),
            ]),

            Section::make('SEO')->schema([
                Forms\Components\TextInput::make('meta_title')->label('Meta Title')->maxLength(70),
                Forms\Components\TextInput::make('meta_description')->label('Meta Description')->maxLength(160),
            ])->collapsible(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('name', direction: 'asc')
            ->columns([
                Tables\Columns\TextColumn::make('name')->label('Name')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('slug')->label('Slug')->copyable(),
                Tables\Columns\TextColumn::make('seasons_count')->counts('seasons')->label('Staffeln'),
                Tables\Columns\TextColumn::make('updated_at')->since()->label('Geändert')->sortable(),
            ])

            // ❌ KEINE headerActions() hier – sonst Doppel-Button

            ->recordActions([
                ActionGroup::make([
                    ViewAction::make(),
                    EditAction::make(),
                    DeleteAction::make(),
                ]),
            ])

            ->groupedBulkActions([
                BulkAction::make('delete')
                    ->label('Ausgewählte löschen')
                    ->requiresConfirmation()
                    ->action(fn (Collection $records) => $records->each->delete()),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            SeasonsRelationManager::class,
            SeoRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            // Der "New Show"-Button kommt automatisch aus der List-Page
            'index'  => Pages\ListShows::route('/'),
            'create' => Pages\CreateShow::route('/create'),
            'edit'   => Pages\EditShow::route('/{record}/edit'),
        ];
    }
}
