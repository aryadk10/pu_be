<?php
namespace App\Filament\Resources\RetributorResource\Api;

use Rupadana\ApiService\ApiService;
use App\Filament\Resources\RetributorResource;
use Illuminate\Routing\Router;


class RetributorApiService extends ApiService
{
    protected static string | null $resource = RetributorResource::class;

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
