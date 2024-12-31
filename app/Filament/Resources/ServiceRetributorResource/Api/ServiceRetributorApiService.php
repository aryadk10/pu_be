<?php
namespace App\Filament\Resources\ServiceRetributorResource\Api;

use Rupadana\ApiService\ApiService;
use App\Filament\Resources\ServiceRetributorResource;
use Illuminate\Routing\Router;


class ServiceRetributorApiService extends ApiService
{
    protected static string | null $resource = ServiceRetributorResource::class;

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
