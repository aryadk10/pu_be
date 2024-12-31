<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Iplt extends Model
{
    use HasFactory;

    protected $table = 'iplt';

    protected $fillable = [
        'code',
        'description',
        'area',
        'cost',
        'unit',
    ];

    /**
     * Field yang diizinkan untuk query string.
     */
    public static function getAllowedFields(): array
    {
        return [
            'code',
            'description',
            'area',
            'cost',
            'unit',
        ];
    }

    /**
     * Field yang diizinkan untuk sorting.
     */
    public static function getAllowedSorts(): array
    {
        return [
            'code',
            'description',
            'area',
            'cost',
            'unit',
        ];
    }

    /**
     * Field yang diizinkan untuk filtering.
     */
    public static function getAllowedFilters(): array
    {
        return [
            'code',
            'description',
            'area',
            'cost',
            'unit',
        ];
    }

    
}

