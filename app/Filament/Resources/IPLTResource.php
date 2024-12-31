<?php

namespace App\Filament\Resources;

use App\Filament\Resources\IPLTResource\Pages;
use App\Filament\Resources\IPLTResource\RelationManagers;
use App\Models\IPLT;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class IPLTResource extends Resource
{
    protected static ?string $model = IPLT::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Master Data';

    protected static ?string $navigationLabel = 'Layanan IPLT';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('code')
                ->label('Kode')
                ->required()
                ->maxLength(10)
                ->unique(ignoreRecord: true)
                ->readOnly(),

            Forms\Components\TextInput::make('description')
                ->label('Deskripsi')
                ->required()
                ->maxLength(255)
                ->readOnly(),

            Forms\Components\TextInput::make('area')
                ->label('Area')
                ->required()
                ->maxLength(255)
                ->readOnly(),

            Forms\Components\TextInput::make('cost')
                ->label('Biaya')
                ->numeric()
                ->minValue(1)
                ->required(),

            Forms\Components\TextInput::make('unit')
                ->label('Satuan')
                ->required()
                ->maxLength(10),
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

                Tables\Columns\TextColumn::make('description')
                    ->label('Deskripsi')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('area')
                    ->label('Area')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('cost')
                    ->label('Biaya')
                    ->money('Rp.')
                    ->sortable(),

                Tables\Columns\TextColumn::make('unit')
                    ->label('Satuan')
                    ->sortable(),
            ])
            ->filters([
                // Tambahkan filter jika diperlukan
            ])
            ->actions([
                Tables\Actions\EditAction::make()->label('Ubah')
                    ->modalHeading('Ubah IPLT')
                    ->modalSubmitActionLabel('Ubah Data')
            ])
            ->headerActions([]); // Menghapus tombol Create
    }


    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageIPLTS::route('/'),
        ];
    }
}
