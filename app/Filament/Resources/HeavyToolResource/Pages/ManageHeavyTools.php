<?php

namespace App\Filament\Resources\HeavyToolResource\Pages;

use App\Filament\Resources\HeavyToolResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageHeavyTools extends ManageRecords
{
    protected static string $resource = HeavyToolResource::class;
    protected static ?string $title = 'Daftar Alat Berat';

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->label('Tambah'),
        ];
    }
}
