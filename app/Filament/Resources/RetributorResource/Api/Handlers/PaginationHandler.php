<?php

namespace App\Filament\Resources\RetributorResource\Api\Handlers;

use Illuminate\Http\Request;
use Rupadana\ApiService\Http\Handlers;
use Spatie\QueryBuilder\QueryBuilder;
use App\Filament\Resources\RetributorResource;
use App\Models\PersonalAccessToken;
use App\Models\User;
use Illuminate\Support\Collection;

class PaginationHandler extends Handlers
{
    public static string | null $uri = '/';
    public static string | null $resource = RetributorResource::class;
    // public static bool $public = true;


    public function handler(Request $request)
    {
        $query = static::getEloquentQuery();
        $model = static::getModel();

        $query = QueryBuilder::for($query)
            ->allowedFields($model::getAllowedFields() ?? [])
            ->allowedSorts($model::getAllowedSorts() ?? [])
            ->allowedFilters($model::getAllowedFilters() ?? [])
            // ->allowedIncludes(['bill','bill.service'])
            // ->join('transactions', 'retributors.npwrd_code', '=', 'transactions.npwrd')
            // ->join('service_retributors', 'transactions.service_id', '=', 'service_retributors.id')
            // ->where('npwrd_code', $request->npwrd)
            // ->where('service_retributors.upt', 'lab')
            // ->where('bill.service.upt', 'heavy_tools')
            // ->whereHas('bill.service', function ($query) {
            //     $query->where('upt', 'lab');
            // })
            ->paginate(request()->query('per_page'))
            ->appends(request()->query());

        // var_dump($query->toSql());
        // die;

        return static::getApiTransformer()::collection($query);
    }



    public function transformRetributorsResponse(Collection $retributors)
    {
        return $retributors->map(function ($retributor) {
            return [
                'id' => $retributor->id,
                'user_id' => $retributor->user_id,
                'retributor_type' => $retributor->retributor_type,
                'npwrd_code' => $retributor->npwrd_code,
                'payment_code' => $retributor->payment_code,
                'first_name' => $retributor->first_name,
                'last_name' => $retributor->last_name,
                'address' => str_replace(' ', '', $retributor->address), // Hilangkan spasi
                'phone_number' => $retributor->phone_number,
                'email' => $retributor->email,
                'passport_photo' => $retributor->passport_photo,
                'ktp_photo' => $retributor->ktp_photo,
                'family_card_photo' => $retributor->family_card_photo,
                'certificate_no_home_ownership' => $retributor->certificate_no_home_ownership,
                'created_at' => $retributor->created_at,
                'updated_at' => $retributor->updated_at,
                'services' => $retributor->bills->map(function ($bill) {
                    return [
                        'service_id' => $bill->service->id,
                        'transaction_id' => $bill->id, // Tambahkan bill_id
                        'transaction_code' => $bill->payment_code, // Tambahkan payment_code
                        'upt' => $bill->service->upt,
                        'iplt_services' => $bill->service->iplt_services,
                        'heavy_tool_services' => $bill->service->heavy_tool_services,
                        'rusunawa_services' => $bill->service->rusunawa_services,
                        'lab_services' => $bill->service->lab_services,
                        'service_date' => $bill->service->service_date,
                        'repeat' => $bill->service->repeat,
                        'status' => $bill->status,
                        'cost' => $bill->amount, // Cost diambil dari amount
                        'created_at' => $bill->service->created_at,
                        'updated_at' => $bill->service->updated_at,
                    ];
                }),
            ];
        });
    }
}
