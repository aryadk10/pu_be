<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;



class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationLabel = 'Manajemen Pengguna';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                FileUpload::make('profile_picture')->label('Unggah foto profil')->columnSpan(2),
                TextInput::make('name')->label('Nama Depan')->required(),
                TextInput::make('lastname')->label('Nama Belakang'),
                Select::make('gender')
                    ->label('Jenis Kelamin')
                    ->options([
                        'L' => 'Laki-laki',
                        'P' => 'Perempuan'
                    ])
                    ->columnSpan(2)
                    ->required(),
                TextInput::make('email')->label('Alamat Surel')->email()->required()->email(),
                TextInput::make('phone')->label('No telp')->required()->tel()->telRegex('/^[+]*[(]{0,1}[0-9]{1,4}[)]{0,1}[-\s\.\/0-9]*$/'),
                Select::make('role')
                    ->options(
                        function () {
                            if (auth()->user()->role === 'super_admin') {
                                return [
                                    'admin' => 'Admin',
                                    'user' => 'User'
                                ];
                            } else {
                                return [
                                    'user' => 'User'
                                ];
                            }
                        }
                    )
                    ->columnSpan(2)
                    ->hidden(function (?Model $record) {
                        return $record && (auth()->user()->id == $record->id);
                    })
                    ->required(),

                TextInput::make('password')->label('Kata sandi')
                    ->password()
                    ->dehydrateStateUsing(fn(string $state): string => Hash::make($state))
                    ->dehydrated(fn(?string $state): bool => filled($state))
                    ->required(fn(string $operation): bool => $operation === 'create')
                    ->revealable(),

                TextInput::make('password_confirm')->label('Konfirmasi kata sandi')
                    ->password()
                    ->same('password')
                    ->dehydrateStateUsing(fn(string $state): string => Hash::make($state))
                    ->dehydrated(fn(?string $state): bool => filled($state))
                    ->required(fn(string $operation): bool => $operation === 'create')
                    ->revealable(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn(Builder $query) => auth()->user()->role === 'admin' ? $query->whereNotIn('role' ,['super_admin']) : $query)
            ->columns([
                TextColumn::make('email')->label('Surel')->searchable(),
                TextColumn::make('phone')->label('No Telp')->searchable(),
                TextColumn::make('name')->label('Nama')->searchable(),
                TextColumn::make('gender')->label('Jenis Kelamin'),
                TextColumn::make('role'),
            ])
            ->actions([
                Tables\Actions\EditAction::make()->label('Ubah'),
                Tables\Actions\DeleteAction::make()->label('Hapus')
                ->modalDescription('Seluruh data yang behubungan dengan pengguna ini akan dihapus dan tidak dapat dikembalikan'),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageUsers::route('/'),
        ];
    }
}
