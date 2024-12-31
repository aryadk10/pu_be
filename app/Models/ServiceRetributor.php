<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceRetributor extends Model
{
    use HasFactory;

    protected $fillable = [
        'retributor_id',
        'upt',
        'product_id',
        'product_code',
        'product_text',
        'iplt_services',
        'heavy_tool_services',
        'rusunawa_services',
        'lab_services',
        'service_date',
        'repeat',
        'status',
    ];

    // Which fields can be selected from the database through the query string
    public function getAllowedFields(): array
    {
        // Your implementation here
        return [
            'retributor_id',
            'upt',
            'iplt_services',
            'heavy_tool_services',
            'rusunawa_services',
            'lab_services',
            'service_date',
            'repeat',
            'status',
        ];
    }

    // Which fields can be used to sort the results through the query string
    public function getAllowedSorts(): array
    {
        return [
            'retributor_id',
            'upt',
            'iplt_services',
            'heavy_tool_services',
            'rusunawa_services',
            'lab_services',
            'service_date',
            'repeat',
            'status',
        ];
    }

    public function getAllowedFilters(): array
    {
        return [
            'retributor_id',
            'upt',
            'iplt_services',
            'heavy_tool_services',
            'rusunawa_services',
            'lab_services',
            'service_date',
            'repeat',
            'status',
        ];
    }

    public function getAllowedIncludes(): array
    {
        return [
            'retributor',
        ];
    }

    public function retributor()
    {
        return $this->belongsTo(Retributor::class, 'retributor_id', 'id');
    }
}
