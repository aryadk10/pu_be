<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Rupadana\ApiService\Contracts\HasAllowedFields;
use Rupadana\ApiService\Contracts\HasAllowedFilters;
use Rupadana\ApiService\Contracts\HasAllowedIncludes;
use Rupadana\ApiService\Contracts\HasAllowedSorts;

class Retributor extends Model implements HasAllowedFields, HasAllowedSorts, HasAllowedFilters, HasAllowedIncludes
{
    use HasFactory;

    protected $fillable = [
        'retributor_type',
        'npwrd_code',
        'payment_code',
        'first_name',
        'last_name',
        'address',
        'phone_number',
        'email',
        'passport_photo',
        'ktp_photo',
        'family_card_photo',
        'certificate_no_home_ownership',
    ];

    public static function getAllowedFields(): array
    {
        return [
            'retributor_type',
            'npwrd_code',
            'payment_code',
            'first_name',
            'last_name',
            'address',
            'phone_number',
            'email',
            'passport_photo',
            'ktp_photo',
            'family_card_photo',
            'certificate_no_home_ownership',
        ];
    }

    public static function getAllowedSorts(): array
    {
        return [
            'retributor_type',
            'npwrd_code',
            'payment_code',
            'first_name',
            'last_name',
            'address',
            'phone_number',
            'email',
            'passport_photo',
            'ktp_photo',
            'family_card_photo',
            'certificate_no_home_ownership',
        ];
    }

    public static function getAllowedFilters(): array
    {
        return [
            'retributor_type',
            'npwrd_code',
            'payment_code',
            'first_name',
            'last_name',
            'address',
            'phone_number',
            'email',
            'passport_id',
            'passport_photo',
            'ktp_id',
            'ktp_photo',
            'family_card_photo',
            'certificate_no_home_ownership',
            'bill.service.upt',
        ];
    }

    public static function getAllowedIncludes() : array
    {
        return [
            'services',
        ];
    }



    public function services()
    {
        return $this->hasMany(ServiceRetributor::class, 'retributor_id', 'id');
    }

    public function bill()
    {
        return $this->hasMany(Transaction::class, 'npwrd', 'npwrd_code');
    }
}
