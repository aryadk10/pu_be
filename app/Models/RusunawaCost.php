<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RusunawaCost extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'rusunawa_id',
        'description',
        'cost',
    ];

    /**
     * Field yang diizinkan untuk query string.
     */
    public static function getAllowedFields(): array
    {
        return [
            'code',
            'rusunawa_id',
            'cost',
        ];
    }

    /**
     * Field yang diizinkan untuk sorting.
     */
    public static function getAllowedSorts(): array
    {
        return [
            'code',
            'rusunawa_id',
            'cost',
        ];
    }

    /**
     * Field yang diizinkan untuk filtering.
     */
    public static function getAllowedFilters(): array
    {
        return [
            'code',
            'rusunawa_id',
            'cost',
        ];
    }

    public function rusunawa()
    {
        return $this->belongsTo(Rusunawa::class, 'rusunawa_id', 'id');
    }
}
