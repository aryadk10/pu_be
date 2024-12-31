<?php

namespace App\Filament\Resources\LabCostResource\Pages;

use App\Filament\Resources\LabCostResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageLabCosts extends ManageRecords
{
    protected static string $resource = LabCostResource::class;
    protected static ?string $title = 'Biaya Pengujian Lab';

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->label('Tambah'),
        ];
    }
}
