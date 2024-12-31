<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RusunawaCostResource\Pages;
use App\Filament\Resources\RusunawaCostResource\RelationManagers;
use App\Models\RusunawaCost;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class RusunawaCostResource extends Resource
{
    protected static ?string $model = RusunawaCost::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Master Data';

    protected static ?string $navigationLabel = 'Biaya Rusunawa';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('code')
                    ->label('Kode')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(10),

                Forms\Components\TextInput::make('description')
                    ->label('Description')
                    ->required()
                    ->maxLength(255),

                Forms\Components\Select::make('rusunawa_id')
                    ->label('Rusunawa')
                    ->options(\App\Models\Rusunawa::all()->pluck('description', 'id'))
                    ->searchable()
                    ->required()
                    ->placeholder('Pilih Rusunawa'),

                Forms\Components\TextInput::make('cost')
                    ->numeric()
                    ->required()
                    ->minValue(1)
                    ->label('Biaya'),
            ]);
    }


    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('code')
                    ->label('Kode')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('rusunawa.description')
                    ->label('Rusunawa')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('description')
                    ->label('Deskripsi')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('cost')
                    ->label('Biaya')
                    ->money('Rp.')
                    ->sortable(),
            ])
            ->filters([
                // Tambahkan filter jika diperlukan
            ])
            ->actions([
                Tables\Actions\EditAction::make()->label('Ubah'),
                Tables\Actions\DeleteAction::make()->label('Hapus')
                ->modalDescription('Data ini tidak akan dapat digunakan lagi setelah dilanjutkan untuk dihapus dan akan memutus relevansi pada data lainnya'),
            ]);
    }


    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageRusunawaCosts::route('/'),
        ];
    }
}
