<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LabCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'description',
    ];

    // Which fields can be selected from the database through the query string
    public function getAllowedFields(): array
    {
        // Your implementation here
        return [
            'code',
            'description',
        ];
    }

    // Which fields can be used to sort the results through the query string
    public function getAllowedSorts(): array
    {
        return [
            'code',
            'description',
        ];
    }

    public function getAllowedFilters(): array
    {
        return [
            'code',
            'description',
        ];
    }
}
