<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rusunawa extends Model
{
    use HasFactory;

    protected $table = 'rusunawa';

    protected $fillable = [
        'code',
        'description',
        'room_qty',
    ];

    /**
     * Get allowed fields for query string.
     */
    public static function getAllowedFields(): array
    {
        return [
            'code',
            'description',
            'room_qty',
        ];
    }

    /**
     * Get allowed sorts for query string.
     */
    public static function getAllowedSorts(): array
    {
        return [
            'code',
            'description',
            'room_qty',
        ];
    }

    /**
     * Get allowed filters for query string.
     */
    public static function getAllowedFilters(): array
    {
        return [
            'code',
            'description',
            'room_qty',
        ];
    }
}

