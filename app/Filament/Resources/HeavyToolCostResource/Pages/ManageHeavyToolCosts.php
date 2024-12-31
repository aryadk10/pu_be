<?php

namespace App\Filament\Resources\HeavyToolCostResource\Pages;

use App\Filament\Resources\HeavyToolCostResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageHeavyToolCosts extends ManageRecords
{
    protected static string $resource = HeavyToolCostResource::class;
    protected static ?string $title = 'Biaya Alat Berat';

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->label('Tambah'),
        ];
    }
}
