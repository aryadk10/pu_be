<?php

namespace App\Filament\Resources;

use App\Filament\Resources\HeavyToolResource\Pages;
use App\Filament\Resources\HeavyToolResource\RelationManagers;
use App\Models\HeavyTool;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class HeavyToolResource extends Resource
{
    protected static ?string $model = HeavyTool::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationLabel = 'Daftar Alat Berat';

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

                Forms\Components\Textarea::make('description')
                    ->label('Deskripsi')
                    ->required(),

                Forms\Components\TextInput::make('stock')
                    ->numeric()
                    ->label('Jumlah Alat Berat')
                    ->minValue(1)
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('code')
                    ->label('Kode')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('description')
                    ->label('Deskripsi')
                    ->searchable(),

                Tables\Columns\TextColumn::make('stock')
                    ->label('Jumlah Alat Berat')
                    ->sortable(),
            ])
            ->filters([
                //
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
            'index' => Pages\ManageHeavyTools::route('/'),
        ];
    }
}
