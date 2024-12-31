<?php

namespace App\Filament\Resources\LabCategoryResource\Pages;

use App\Filament\Resources\LabCategoryResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageLabCategories extends ManageRecords
{
    protected static string $resource = LabCategoryResource::class;
    protected static ?string $title = 'Kategori Pengujian';

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->label('Tambah'),
        ];
    }
}
