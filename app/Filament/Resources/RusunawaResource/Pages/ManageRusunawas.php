<?php

namespace App\Filament\Resources\RusunawaResource\Pages;

use App\Filament\Resources\RusunawaResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageRusunawas extends ManageRecords
{
    protected static string $resource = RusunawaResource::class;
    protected static ?string $title = 'Daftar Rusunawa';

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->label('Tambah'),
        ];
    }
}
