<?php
namespace App\Filament\Resources\LabCategoryResource\Api;

use Rupadana\ApiService\ApiService;
use App\Filament\Resources\LabCategoryResource;
use Illuminate\Routing\Router;


class LabCategoryApiService extends ApiService
{
    protected static string | null $resource = LabCategoryResource::class;

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
