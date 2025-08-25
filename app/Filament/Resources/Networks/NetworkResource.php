<?php

namespace App\Filament\Resources\Networks;

use App\Filament\Resources\Networks\Pages;
use App\Models\Network;
use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Illuminate\Support\Str;

// Actions v4
use Filament\Actions\ActionGroup;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;

class NetworkResource extends Resource
{
    protected static ?string $model = Network::class;

    protected static \UnitEnum|string|null   $navigationGroup = 'Inhalte';
    protected static ?string                 $navigationLabel = 'Sender/Plattformen';
    protected static \BackedEnum|string|null $navigationIcon  = 'heroicon-o-signal';

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            Forms\Components\TextInput::make('name')->label('Name')->required()
                ->live(onBlur: true)
                ->afterStateUpdated(fn($state, callable $set) => $set('slug', Str::slug($state))),
            Forms\Components\TextInput::make('slug')->label('Slug')->required()->unique(ignoreRecord: true),
            Forms\Components\TextInput::make('url')->label('Website')->url()->maxLength(2048),

            Forms\Components\FileUpload::make('logo_path')->label('Logo')
                ->directory('networks/logos')->disk('public')->visibility('public')
                ->acceptedFileTypes(['image/svg+xml','image/svg','image/png','image/webp'])
                ->imageEditor(false)->maxSize(2048)
                ->getUploadedFileNameForStorageUsing(function ($file) {
                    $ext  = $file->getClientOriginalExtension();
                    $base = Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME));
                    return $base.'-'.Str::random(6).'.'.$ext;
                }),
        ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('logo_path')->label('')->disk('public')->size(32)->circular(),
                Tables\Columns\TextColumn::make('name')->label('Name')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('url')->label('Website')->limit(30)
                    ->url(fn ($state) => filled($state) ? $state : null)->openUrlInNewTab()->toggleable(),
                Tables\Columns\TextColumn::make('shows_count')->counts('shows')->label('Shows'),
                Tables\Columns\TextColumn::make('updated_at')->since()->label('Geändert')->sortable(),
            ])
            ->recordActions([
                ActionGroup::make([
                    EditAction::make(),
                    DeleteAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListNetworks::route('/'),
            'create' => Pages\CreateNetwork::route('/create'),
            'edit'   => Pages\EditNetwork::route('/{record}/edit'),
        ];
    }
}
