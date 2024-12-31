<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HeavyToolCost extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'heavy_tool_id',
        'area',
        'cost',
        'unit',
    ];

    // Allowed fields
    public function getAllowedFields(): array
    {
        return [
            'code',
            'heavy_tool_id',
            'area',
            'cost',
            'unit',
        ];
    }

    // Allowed sorts
    public function getAllowedSorts(): array
    {
        return [
            'code',
            'heavy_tool_id',
            'area',
            'cost',
            'unit',
        ];
    }

    // Allowed filters
    public function getAllowedFilters(): array
    {
        return [
            'code',
            'heavy_tool_id',
            'area',
            'cost',
            'unit',
        ];
    }

    public function heavyTool(){
        return $this->belongsTo(HeavyTool::class,'heavy_tool_id', 'id');
    }
}
