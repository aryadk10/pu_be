<?php

namespace App\Filament\Resources\IPLTResource\Pages;

use App\Filament\Resources\IPLTResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageIPLTS extends ManageRecords
{
    protected static string $resource = IPLTResource::class;
    protected static ?string $title = 'Pelayanan IPLT';
}
