<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    const STATUS_BILLING = 'unpaid';
    const STATUS_PENDING_PAYMENT = 'pending';
    const STATUS_PARTIAL_PAYMENT = 'partial';
    const STATUS_FULL_PAYMENT = 'paid';

    protected $fillable = [
        'payment_code',      // Kode pembayaran unik
        'qris_link',         // QRIS link (opsional)
        'npwrd',             // NPWRD
        'service_id',        // ID layanan
        'status',            // Status transaksi
        'amount',            // Jumlah transaksi
        'total',             // Total transaksi
        'type',             // Total transaksi
        'upt',             // Total transaksi
        'details',             // Total transaksi
        'payment_expired',   // Waktu kadaluarsa pembayaran
        'payment_date',   // Waktu kadaluarsa pembayaran
        'parent_id',   // Waktu kadaluarsa pembayaran
        'invoice_date',   // Waktu kadaluarsa pembayaran
        'bjb_client_type',
        'bjb_product_code',
        'bjb_invoice_no',
        'bjb_description',
        'bjb_customer_name',
        'bjb_customer_email',
        'bjb_customer_phone',
        'bjb_expired_date',
        'bjb_amount',
        'bjb_qrcode',
    ];


    public function service()
    {
        return $this->hasOne(ServiceRetributor::class, 'id', 'service_id');
    }

    public function retributor()
    {
        return $this->hasOne(Retributor::class, 'npwrd_code', 'npwrd');
    }

    public function subTransactions()
    {
        return $this->hasMany(Transaction::class, 'parent_id', 'id');
    }

    // <?php

    // class TransactionHandler {

    public function calculateTotal($data, $product)
    {
        return $data['upt'] === 'rusunawa' ? RusunawaCost::find($product[0])->cost : 0;
    }

    public function findOrCreateRusunawaTransaction($npwrd_code, $total, $upt)
    {
        $rusunawaTransaction = Transaction::where('type', 'partial')
            ->where('npwrd', $npwrd_code)
            ->first();

        if ($rusunawaTransaction) {
            $rusunawaTransaction->total += $total;
            $rusunawaTransaction->save();
        } else {
            $rusunawaTransaction = Transaction::create([
                'npwrd' => $npwrd_code,
                'payment_code' => now()->format('Ymd') . rand(1000, 9999),
                'service_id' => null,
                'attribute_code' => '',
                'type' => 'partial',
                'value' => '',
                'upt' => $upt,
                'status' => Transaction::STATUS_BILLING,
                'subtotal' => 0,
                'amount' => 0,
                'total' => $total,
                'payment_expired' => null,
                'invoice_date' => date('Y-m-d'),
            ]);
        }

        return $rusunawaTransaction->id;
    }

    public function createTransaction($data, $product, $service_id, $total, $parentId = null)
    {
        return Transaction::create([
            'npwrd' => $data['npwrd'],
            'payment_code' => now()->format('Ymd') . rand(1000, 9999),
            'service_id' => $service_id,
            'attribute_code' => $product[1],
            'value' => '[0]',
            'status' => Transaction::STATUS_BILLING,
            'type' => 'unit',
            'upt' => $data['upt'],
            'parent_id' => $parentId,
            'subtotal' => 0,
            'amount' => 0,
            'total' => $total,
            'payment_expired' => null,
            'invoice_date' => $data['service_date'],
        ]);
    }

    public function handleTransactions($data, $product, $service_id)
    {
        $total = $this->calculateTotal($data, $product);
        $parentId = null;

        if ($data['upt'] === 'rusunawa') {
            $parentId = $this->findOrCreateRusunawaTransaction($data['npwrd'], $total, $data['upt']);
        }

        $this->createTransaction($data, $product, $service_id, $total, $parentId);
    }
    // }

    // Example usage
    // $transactionHandler = new TransactionHandler();
    // $transactionHandler->handleTransactions($data, $product, $id);


    // Example usage
    // handleTransactions($data, $product, $id, $npwrd_code);

}
