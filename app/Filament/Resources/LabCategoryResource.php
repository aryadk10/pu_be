<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LabCategoryResource\Pages;
use App\Filament\Resources\LabCategoryResource\RelationManagers;
use App\Models\LabCategory;
use Filament\Forms;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class LabCategoryResource extends Resource
{
    protected static ?string $model = LabCategory::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Master Data';

    protected static ?string $navigationLabel = 'Kategori Pengujian';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('code')->required()->unique(ignoreRecord: true)->label('Kode')->maxLength(10),
                TextInput::make('description')->required()->label('Deskripsi'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('code')->label('Kode')->searchable(),
                TextColumn::make('description')->label('Deskripsi')->searchable(),
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
            'index' => Pages\ManageLabCategories::route('/'),
        ];
    }
}
