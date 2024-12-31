<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LabCost extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'category_id',
        'description',
        'cost',
        'unit',
    ];


    public function getAllowedFields(): array
    {
        // Your implementation here
        return [
            'code',
            'category_id',
            'description',
            'cost',
            'unit',
        ];
    }

    public function getAllowedSorts(): array
    {
        return [
            'code',
            'category_id',
            'description',
            'cost',
            'unit',
        ];
    }

    public function getAllowedFilters(): array
    {
        return [
            'code',
            'category_id',
            'description',
            'cost',
            'unit',
        ];
    }

    public function category()
    {
        return $this->belongsTo(LabCategory::class, 'category_id', 'id');
    }
}
