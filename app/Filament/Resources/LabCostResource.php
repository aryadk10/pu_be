<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LabCostResource\Pages;
use App\Filament\Resources\LabCostResource\RelationManagers;
use App\Models\LabCategory;
use App\Models\LabCost;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class LabCostResource extends Resource
{
    protected static ?string $model = LabCost::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Master Data';

    protected static ?string $navigationLabel = 'Biaya Pengujian Lab';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('code')->required()->label('Kode')->unique(ignoreRecord: true)->maxLength(10),
                Select::make('category_id')
                    ->label('Kategori')
                    ->options(LabCategory::all()->pluck('description', 'id'))
                    ->required()
                    ->searchable(),
                TextInput::make('description')->label('Deskripsi')->required(),
                TextInput::make('cost')->numeric()->required()->minValue(1)->label('Biaya'),
                TextInput::make('unit')->required()->label('Satuan'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('code')->label('Kode')->searchable(),
                TextColumn::make('category.description')->label('Kategori')->searchable(),
                TextColumn::make('description')->label('Deskripsi')->searchable(),
                TextColumn::make('cost')->label('Biaya')->money('Rp.'),
                TextColumn::make('unit')->label('Satuan'),
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
            'index' => Pages\ManageLabCosts::route('/'),
        ];
    }
}
