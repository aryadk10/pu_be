<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransactionDetail extends Model
{
    use HasFactory;

    // Tentukan nama tabel jika tidak mengikuti konvensi plural
    protected $table = 'transaction_details';

    // Tentukan kolom yang bisa diisi
    protected $fillable = [
        'transaction_id',
        'service_id',
        'attribute_code',
        'value',
        'subtotal',
        'payment_expired',
    ];

    // Tentukan relasi jika diperlukan, misalnya dengan transaksi dan layanan
    public function transaction()
    {
        return $this->belongsTo(Transaction::class, 'transaction_id');
    }
}
