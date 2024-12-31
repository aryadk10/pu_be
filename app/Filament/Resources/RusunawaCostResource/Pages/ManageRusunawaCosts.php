<?php

namespace App\Filament\Resources\RusunawaCostResource\Pages;

use App\Filament\Resources\RusunawaCostResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageRusunawaCosts extends ManageRecords
{
    protected static string $resource = RusunawaCostResource::class;
    protected static ?string $title = 'Biaya Rusunawa';

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->label('Tambah'),
        ];
    }
}
