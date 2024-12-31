<?php

namespace App\Filament\Resources\RetributorResource\Pages;

use App\Filament\Resources\RetributorResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageRetributors extends ManageRecords
{
    protected static string $resource = RetributorResource::class;
    protected static ?string $title = 'Retributor';

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->mutateFormDataUsing(function (array $data)   {
                $data['payment_code'] = '-';
                return $data;
            })->label('Tambah'),
        ];
    }
}
