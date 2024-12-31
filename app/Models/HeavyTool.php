<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HeavyTool extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'description',
        'stock',
    ];

    // Fungsi untuk menentukan fields yang dapat dipilih dalam query string
    public function getAllowedFields(): array
    {
        return [
            'code',         // Field 'code' dapat dipilih
            'description',  // Field 'description' dapat dipilih
            'stock',        // Tambahkan field 'stock' jika ingin
        ];
    }

    // Fungsi untuk menentukan fields yang dapat digunakan untuk sorting
    public function getAllowedSorts(): array
    {
        return [
            'code',         // Sorting berdasarkan 'code'
            'description',  // Sorting berdasarkan 'description'
            'stock',        // Sorting berdasarkan 'stock'
        ];
    }

    // Fungsi untuk menentukan fields yang dapat digunakan untuk filtering
    public function getAllowedFilters(): array
    {
        return [
            'code',         // Filter berdasarkan 'code'
            'description',  // Filter berdasarkan 'description'
            'stock',        // Filter berdasarkan 'stock'
        ];
    }
}
