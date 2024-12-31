<?php

namespace App\Filament\Resources\TransactionResource\Api\Handlers;

use Illuminate\Http\Request;
use Rupadana\ApiService\Http\Handlers;
use Spatie\QueryBuilder\QueryBuilder;
use App\Filament\Resources\TransactionResource;

class PaginationHandler extends Handlers
{
    public static string | null $uri = '/';
    public static string | null $resource = TransactionResource::class;


    public function handler(Request $request)
    {
        $query = static::getEloquentQuery();
        $model = static::getModel();

        // $request->validate('required','filter[type]');
        $request->validate([
            'filter.type' => 'required|string',
        ]);

        // Buat query dengan QueryBuilder
        $query = QueryBuilder::for($query)
            ->allowedFields($this->getAllowedFields() ?? []) // Tentukan field yang diizinkan
            ->allowedSorts($this->getAllowedSorts() ?? [])   // Tentukan sort yang diizinkan
            ->allowedFilters([
                'npwrd',
                'upt',
                'service_retributors.service_date',
                'service_retributors.product_text',
                'transactions.status',
                'transactions.created_at',
                'type'
            ])
            ->leftJoin('service_retributors', 'transactions.service_id', '=', 'service_retributors.id') // Gabungkan dengan service_retributors
            ->leftJoin('retributors', 'transactions.npwrd', '=', 'retributors.npwrd_code'); // Gabungkan dengan retributors





        $select = [
            'transactions.*',
            'transactions.type as type',
            'retributors.first_name',
            'retributors.last_name',
            'retributors.ktp_id',
            'service_retributors.service_date',
            'service_retributors.product_code',
            'service_retributors.product_text',
            'service_retributors.upt',
            'transactions.status as status_transaksi'
        ];

        // Kondisi tambahan untuk filter 'rusunawa'
        if (isset($request->filter['upt']) && $request->filter['upt'] === 'rusunawa') {
            // $query = $query->where('type',$request->filter['type']);
            $query->leftJoin('rusunawa', 'service_retributors.product_code', '=', 'rusunawa.code');
            $select = array_merge($select, ['rusunawa.description as product_name']);
        } else if (isset($request->filter['upt']) && $request->filter['upt'] === 'heavy_tools') {
            $query->leftJoin('heavy_tools', 'service_retributors.product_code', '=', 'heavy_tools.code');
            $query->leftJoin('heavy_tool_costs', 'service_retributors.product_id', '=', 'heavy_tool_costs.id');
            $select = array_merge($select, ['heavy_tools.description as product_name','heavy_tool_costs.area','heavy_tool_costs.cost as product_cost','heavy_tool_costs.unit']);
        } else if (isset($request->filter['upt']) && $request->filter['upt'] === 'lab') {
            $query->leftJoin('lab_costs', 'service_retributors.product_id', '=', 'lab_costs.id');
            $query->leftJoin('lab_categories', 'lab_costs.category_id', '=', 'lab_categories.id');
            $select = array_merge($select, ['lab_costs.description as product_name','lab_costs.cost as product_cost','lab_costs.unit','lab_categories.description as category']);
        }else if (isset($request->filter['upt']) && $request->filter['upt'] === 'iplt') {
            $query->leftJoin('iplt', 'service_retributors.product_id', '=', 'iplt.id');
            $query->leftJoin('iplt as transport', 'iplt.id', '=', 'transport.service');
            $select = array_merge($select, [
                'iplt.description as product_name',
                'iplt.area as product_area',
                'iplt.cost as product_cost',
                'iplt.unit',
                'transport.description as transport_name',
                'transport.cost as transport_cost',
                'transport.unit as transport_unit'
            ]);
        }

        // Pilih kolom yang diperlukan dalam hasil query
        $query = $query->select($select);

        // Paginate hasil query dengan parameter per_page dari request
        $query = $query->paginate($request->query('per_page'))
            ->appends($request->query()); // Tambahkan query string untuk mempertahankan parameter


        return static::getApiTransformer()::collection($query);
    }
}
