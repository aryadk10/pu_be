<?php

namespace App\Filament\Resources;

use App\Filament\Resources\HeavyToolCostResource\Pages;
use App\Filament\Resources\HeavyToolCostResource\RelationManagers;
use App\Models\HeavyTool;
use App\Models\HeavyToolCost;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class HeavyToolCostResource extends Resource
{
    protected static ?string $model = HeavyToolCost::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationLabel = 'Biaya Alat Berat';

    protected static ?string $navigationGroup = 'Master Data';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('code')
                    ->required()
                    ->label('Kode')
                    ->unique(ignoreRecord: true)
                    ->maxLength(10),

                Forms\Components\Select::make('heavy_tool_id')
                    ->label('Description')
                    ->options(HeavyTool::all()->pluck('description', 'id'))
                    ->placeholder('Pilih Alat Berat')
                    ->searchable(),

                Forms\Components\Select::make('area')
                    ->label('Area')
                    ->options(['Dalam Kota'=>'Dalam Kota','Luar Kota'=>'Luar Kota'])
                    ->placeholder('Pilih Area')
                    ->searchable(),

                Forms\Components\TextInput::make('cost')
                    ->numeric()
                    ->required()
                    ->minValue(1)
                    ->label('Biaya'),

                Forms\Components\TextInput::make('unit')
                    ->required()
                    ->label('Satuan')
                    ->maxLength(255),
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

                Tables\Columns\TextColumn::make('heavyTool.description')
                    ->label('Description')
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
                    ->label('Satuan'),
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
            'index' => Pages\ManageHeavyToolCosts::route('/'),
        ];
    }
}
