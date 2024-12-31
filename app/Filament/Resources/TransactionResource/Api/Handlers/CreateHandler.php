<?php

namespace App\Filament\Resources\TransactionResource\Api\Handlers;

use Illuminate\Http\Request;
use Rupadana\ApiService\Http\Handlers;
use App\Filament\Resources\TransactionResource;
use App\Http\Requests\ValidateTransactionRequest;
use App\Models\BjbTransactionApi;
use App\Models\HeavyToolCost;
use App\Models\Iplt;
use App\Models\LabCost;
use App\Models\RusunawaCost;
use App\Models\ServiceRetributor;
use App\Models\Transaction;
use App\Models\TransactionDetail;

class CreateHandler extends Handlers
{
    public static string | null $uri = '/';
    public static string | null $resource = TransactionResource::class;

    public static function getMethod()
    {
        return Handlers::POST;
    }

    public static function getModel()
    {
        return static::$resource::getModel();
    }

    public function handler(Request $request)
    {
        // $model = new (static::getModel());

        $total = 0; // Inisialisasi total
        $parent = null;



        $npwrd = $request->npwrd;
        $amount = $request->amount;

        if ($request->upt === 'rusunawa') {
            $rusunawaTransaction = Transaction::where('type', 'partial')->where('npwrd', $npwrd)->first();

            if ($rusunawaTransaction) {
                $rusunawaTransactionItems = Transaction::where('parent_id', $rusunawaTransaction->id)->where('type','unit')->orderBy('created_at','asc')->get();
                // dd($rusunawaTransactionItems);
                $rusunawaTransaction->amount += $amount; // Update total amount


                $remainingAmount = $amount; // Variabel untuk melacak sisa amount yang perlu didistribusikan
                $total = 0;
                $bill_details = [];
                foreach ($rusunawaTransactionItems as $item) {
                    $outstandingAmount = $item->total - $item->amount; // Sisa tagihan untuk item ini
                    $service = ServiceRetributor::find($item->service_id);
                    if ($outstandingAmount <= 0) {
                        $bill_detail = [
                            'billing_id' => $item->payment_code,
                            'due_date' => $service->service_date,
                            'name' => $service->retributor->first_name .' '.$service->retributor->last_name,
                            'address' => $service->retributor->address,
                            'status' => $item->status,
                            'total' => $item->total,
                            'amount' => $item->amount,
                            'defisit' => $outstandingAmount,
                        ];
                        $bill_details[] = $bill_detail;
                        continue; // Jika tagihan sudah lunas, lanjutkan ke item berikutnya
                    }

                    $paid = min($remainingAmount, $outstandingAmount); // Jumlah yang akan dibayarkan untuk item ini
                    $item->amount += $paid; // Tambahkan jumlah yang telah dibayarkan ke item
                    $item->status = ($item->amount == $item->total) ? Transaction::STATUS_FULL_PAYMENT : Transaction::STATUS_PARTIAL_PAYMENT; // Tambahkan jumlah yang telah dibayarkan ke item
                    $remainingAmount -= $paid; // Kurangi sisa amount dengan jumlah yang telah dibayarkan
                    $item->save(); // Simpan perubahan pada item
                    $total += $item->total;

                    $bill_detail = [
                        'billing_id' => $item->payment_code,
                        'due_date' => $service->service_date,
                        'name' => $service->retributor->first_name .' '.$service->retributor->last_name,
                        'address' => $service->retributor->address,
                        'status' => $item->status,
                        'total' => $item->total,
                        'amount' => $item->amount,
                        'defisit' => $outstandingAmount,
                    ];
                    $bill_details[] = $bill_detail;
                    // if ($remainingAmount <= 0) {
                    //     break; // Jika tidak ada sisa amount, keluar dari loop
                    // }
                }
                $rusunawaTransaction->total = $total;
                $rusunawaTransaction->status = $rusunawaTransaction->amount == $rusunawaTransaction->total ? Transaction::STATUS_FULL_PAYMENT : Transaction::STATUS_PARTIAL_PAYMENT;
                $rusunawaTransaction->save(); // Jangan lupa menyimpan perubahan

                // Membuat entri transaksi baru untuk pembayaran parsial
                $bill = Transaction::create([
                    'npwrd' => $npwrd,
                    'payment_code' => now()->format('Ymd') . rand(1000, 9999),
                    'service_id' => null,
                    'attribute_code' => '',
                    'type' => 'payment_partial',
                    'value' => '',
                    'upt' => 'rusunawa',
                    'status' => Transaction::STATUS_PENDING_PAYMENT,
                    'subtotal' => $amount, // Subtotal cukup angka
                    'amount' => $amount,  // Amount cukup angka
                    'total' => $amount,   // Total cukup angka
                    'payment_expired' => now()->addDay(), // Set expired satu hari dari sekarang
                    'invoice_date' => now()->format('Y-m-d'), // Set tanggal hari ini
                    'parent_id' => $rusunawaTransaction->id,
                    'details' => json_encode($bill_details)
                ]);
            } else {
                return static::sendNotFoundResponse(
                    null,
                    "Billing not found."
                );
            }
        }else{
            // Validasi input
            if (!isset($request->attr) || !is_array($request->attr)) {
                return response()->json(['error' => 'Invalid attributes provided.'], 400);
            }
            if ($request->has('attr') && count($request->attr) > 1) {
                // Buat transaksi parent jika ada lebih dari satu atribut
                $parent = Transaction::create([
                    'npwrd' => $npwrd,
                    'payment_code' => now()->format('Ymd') . rand(1000, 9999),
                    'service_id' => null,
                    'attribute_code' => '',
                    'type' => 'bundling_payment',
                    'value' => '',
                    'status' => Transaction::STATUS_PENDING_PAYMENT,
                    'subtotal' => 0, // Jangan encode json untuk subtotal, cukup angka
                    'amount' => 0, // Jangan encode json untuk subtotal, cukup angka
                    'total' => 0, // Jangan encode json untuk subtotal, cukup angka
                    'payment_expired' => null, // Set expired date satu hari dari sekarang
                    'invoice_date' => date('Y-m-d') // Set expired date satu hari dari sekarang
                ])->id; // Simpan ID parent
            }

            if ($request->has('attr')){
                $bill_details = [];
                foreach ($request->attr as $attr) {
                    // Validasi input atribut
                    if (!isset($attr['bill_id']) || !isset($attr['values']) || !is_array($attr['values'])) {
                        return response()->json(['error' => 'Invalid bill ID or values provided.'], 400);
                    }

                    $bill = Transaction::find($attr['bill_id']);
                    if (!$bill) {
                        return response()->json(['error' => "Bill with ID {$attr['bill_id']} not found."], 404);
                    }

                    $service_id = $bill->service_id;
                    $service = ServiceRetributor::find($service_id);
                    if (!$service) {
                        return response()->json(['error' => "Service with ID {$service_id} not found."], 404);
                    }

                    $upt = $service->upt;
                    $targetClass = match ($upt) {
                        'iplt' => Iplt::class,
                        'heavy_tools' => HeavyToolCost::class,
                        'lab' => LabCost::class,
                        default => null,
                    };

                    if (!$targetClass) {
                        return response()->json(['error' => "Unsupported UPT type: {$upt}."], 400);
                    }

                    $target = $targetClass::find($service->product_id);
                    if (!$target) {
                        return response()->json(['error' => "Product with ID {$service->product_id} not found in {$upt}."], 404);
                    }

                    // Hitung subtotal

                    if($upt == 'iplt'){
                        $transport = Iplt::where('area',$target->area)->where('description','Biaya transportasi')->first();
                        $subtotal = $target->cost * $attr['values'][0]??0;
                        $subtotal += $transport->cost * $attr['values'][1]??0;
                    }else{
                        $subtotal = $target->cost * $this->array_multiplication($attr['values']);
                    }

                    $bill_detail = [
                        'billing_id' => $bill->payment_code,
                        'due_date' => $service->service_date,
                        'name' => $service->retributor->first_name .' '.$service->retributor->last_name,
                        'address' => $service->retributor->address,
                        'status' => $bill->status,
                        'total' => $bill->total,
                        'amount' => $bill->amount,
                        'defisit' => $bill->total - $bill->amount,
                    ];
                    $bill_details[] = $bill_detail;

                    // Update tagihan
                    $bill->update([
                        'value' => json_encode($attr['values']),
                        'subtotal' => $subtotal,
                        'total' => $subtotal,
                        'amount' => $subtotal,
                        'payment_expired' => now()->addDay(),
                        'parent_id' => $parent,
                        'type' => 'unit_payment',
                        'details' => json_encode([$bill_detail])
                    ]);

                    $total += $subtotal; // Tambahkan ke total
                    // }
                }

                // Update total pada transaksi parent
                if ($parent) {
                    $parentTransaction = Transaction::find($parent);
                    if ($parentTransaction) {
                        $parentTransaction->update(['upt'=>@$upt,'total' => $total,'amount'=>$total,'details' => json_encode($bill_details)]);
                    }
                }
            }
        }


        $bjb = new BjbTransactionApi();

        // $response = $bjb->setInvoice([]);
        // $kdBayar = $response['kd_bayar'];
        // $qrisData = $bjb->createQris($kdBayar);

        // $bill = isset($parent) && $parent ? $parentTransaction : $bill;

        // $bill->bjb_client_type = $qrisData['client_type'];
        // $bill->bjb_product_code = $qrisData['product_code'];
        // $bill->bjb_invoice_no = $qrisData['invoice_no'];
        // $bill->bjb_description = $qrisData['description'];
        // $bill->bjb_customer_name = $qrisData['customer_name'];
        // $bill->bjb_customer_email = $qrisData['customer_email'];
        // $bill->bjb_customer_phone = $qrisData['customer_phone'];
        // $bill->bjb_expired_date = $qrisData['expired_date'];
        // $bill->bjb_amount = $qrisData['amount'];
        // $bill->bjb_qrcode = $qrisData['qrcode'];

        // $bill->save();

        return static::sendSuccessResponse(
            (isset($parent) && $parent) ? $parentTransaction : $bill,
            "Successfully created resource."
        );
    }

    function array_multiplication($array){
        $result = null;

        foreach($array as $a){
            if($result){
                $result *= $a;
            }else{
                $result = $a;
            }
        }

        return $result;
    }
}
