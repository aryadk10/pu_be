<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TransactionResource\Pages;
use App\Filament\Resources\TransactionResource\RelationManagers;
use App\Models\Transaction;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Columns\ViewColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Enums\FiltersLayout;

class TransactionResource extends Resource
{
    protected static ?string $model = Transaction::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationLabel = 'Monitoring Transaksi';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn(Builder $query) => $query->whereIn('status' ,['unpaid','partial','paid'])->whereIn('type' ,['unit_payment','bundling_payment'])->where('parent_id' ,null))
            ->columns([
                TextColumn::make('payment_code')->label('QRIS'),
                TextColumn::make('npwrd')->label('NPWRD'),
                ViewColumn::make('detail')->view('custom.transaction.custom-columns')->label('Detail Layanan'),
                TextColumn::make('total')->label('Total')->money('Rp.'),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'unpaid' => 'warning',
                        'paid' => 'success',
                        'cancel' => 'danger',
                    })
                    ->label('Status'),
            ])
            ->filters([
                SelectFilter::make('service.upt')
                    ->relationship('service', 'upt')
                    ->options([
                        'iplt' => 'IPLT',
                        'heavy_tools' => 'Alat Berat',
                        'rusunawa' => 'Rusunawa',
                        'lab' => 'Laboratorium',
                    ]),
                SelectFilter::make('status')
                    ->options([
                        'billed' => 'Menunggu',
                        'paid' => 'Berhasil'
                    ]),
                ], layout: FiltersLayout::AboveContent)
            ->actions([
                // Tables\Actions\EditAction::make()->label('Ubah'),
                // Tables\Actions\DeleteAction::make()->label('Hapus'),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageTransactions::route('/'),
        ];
    }
}
