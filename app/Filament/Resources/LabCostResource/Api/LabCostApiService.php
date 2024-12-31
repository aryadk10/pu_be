<?php
namespace App\Filament\Resources\LabCostResource\Api;

use Rupadana\ApiService\ApiService;
use App\Filament\Resources\LabCostResource;
use Illuminate\Routing\Router;


class LabCostApiService extends ApiService
{
    protected static string | null $resource = LabCostResource::class;

    public static function handlers() : array
    {
        return [
            Handlers\CreateHandler::class,
            Handlers\UpdateHandler::class,
            Handlers\DeleteHandler::class,
            Handlers\PaginationHandler::class,
            Handlers\DetailHandler::class
        ];

    }
}
