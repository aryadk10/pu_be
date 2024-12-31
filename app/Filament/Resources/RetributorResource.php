<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RetributorResource\Pages;
use App\Models\Retributor;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class RetributorResource extends Resource
{
    protected static ?string $model = Retributor::class;

    protected static ?string $navigationLabel = 'Manajemen Retributor';

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form->schema([
            // Detail
            Forms\Components\Section::make('Detail')
                ->schema([
                    Select::make('retributor_type')
                        ->label('Jenis Retributor')
                        ->columnSpan(2)
                        ->options([
                            'Individu' => 'Individu',
                            'Nirlaba' => 'Nirlaba',
                            'Perusahaan' => 'Perusahaan'
                        ]),


                    Forms\Components\TextInput::make('npwrd_code')
                        ->label('Kode NPWRD \ Bayar')
                        // ->unique(ignoreRecord: true)
                        ->required()
                        ->columnSpan(2)
                        ->maxLength(10),
                ])
                ->columns(2),

            // Isi sesuai KTP
            Forms\Components\Section::make('Isi Sesuai KTP')
                ->schema([
                    Forms\Components\TextInput::make('first_name')
                        ->label('Nama Depan')
                        ->required()
                        ->maxLength(255),

                    Forms\Components\TextInput::make('last_name')
                        ->label('Nama Belakang (Opsional)')
                        ->nullable()
                        ->maxLength(255),

                    Forms\Components\TextArea::make('address')
                        ->label('Alamat')
                        ->required()
                        ->maxLength(255),
                ])
                ->columns(1),

            // Kontak dan Legal
            Forms\Components\Section::make('Kontak dan Legal')
                ->schema([
                    Forms\Components\TextInput::make('phone_number')
                        ->label('Nomor Telepon')
                        ->required()
                        ->maxLength(255)
                        ->tel()
                        ->telRegex('/^[+]*[(]{0,1}[0-9]{1,4}[)]{0,1}[-\s\.\/0-9]*$/'),

                    Forms\Components\TextInput::make('email')
                        ->label('Alamat Surel')
                        ->email()
                        // ->unique(ignoreRecord: true)
                        ->required()
                        ->maxLength(255),

                    Forms\Components\TextInput::make('passport_id')
                        ->label('Nomor Passport'),

                    Forms\Components\FileUpload::make('passport_photo')
                        ->label('Unggah Foto Passpor 4x6')
                        ->image()
                        ->nullable(),

                    Forms\Components\TextInput::make('ktp_id')
                        ->label('Nomor KTP')
                        ->required(),

                    Forms\Components\FileUpload::make('ktp_photo')
                        ->label('Unggah KTP')
                        ->image()
                        ->nullable(),
                ])
                ->columns(2),

            // Khusus Penghuni Rusunawa
            Forms\Components\Section::make('Khusus Penghuni Rusunawa')
                ->schema([
                    Forms\Components\FileUpload::make('family_card_photo')
                        ->label('Unggah Kartu Keluarga')
                        ->nullable(),

                    Forms\Components\FileUpload::make('certificate_no_home_ownership')
                        ->label('Unggah Surat Belum Miliki Rumah dari Kelurahan')
                        ->nullable(),
                ])
                ->columns(1),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('npwrd_code')
                    ->label('NPWRD')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('first_name')
                    ->label('Nama')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('phone_number')
                    ->label('No. Telp')
                    ->sortable(),

                Tables\Columns\TextColumn::make('email')
                    ->label('Surel')

                    ->sortable(),

                Tables\Columns\TextColumn::make('retributor_type')
                    ->label('Tipe Pelanggan')
                    ->sortable(),

                Tables\Columns\TextColumn::make('payment_code')
                    ->label('Layanan')
                    ->sortable(),
            ])
            ->filters([
                // Tambahkan filter jika diperlukan
            ])
            ->actions([
                Tables\Actions\EditAction::make()->label('Ubah'),
                Tables\Actions\DeleteAction::make()->label('Hapus')
                ->modalDescription('Seluruh data yang behubungan dengan retributor ini akan dihapus dan tidak dapat dikembalikan'),
                Tables\Actions\Action::make('customRedirect')
                    ->label('Rincian') // Label untuk tombol
                    ->url(fn($record) => route('filament.admin.resources.service-retributors.index', ['retributor' => $record->id]))
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageRetributors::route('/'),
        ];
    }
}
