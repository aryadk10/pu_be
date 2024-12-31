<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RusunawaResource\Pages;
use App\Filament\Resources\RusunawaResource\RelationManagers;
use App\Models\Rusunawa;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class RusunawaResource extends Resource
{
    protected static ?string $model = Rusunawa::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Master Data';

    protected static ?string $navigationLabel = 'Daftar Rusunawa';

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
                    ->required()
                    ->label('Deskripsi'),

                Forms\Components\TextInput::make('room_qty')
                    ->numeric()
                    ->required()
                    ->minValue(1)
                    ->label('Jumlah Kamar'),
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
                    ->searchable()
                    ->limit(50),

                Tables\Columns\TextColumn::make('room_qty')
                    ->label('Jumlah Kamar')
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
            'index' => Pages\ManageRusunawas::route('/'),
        ];
    }
}
