<?php

namespace App\Livewire;

use App\Models\Retributor;
use App\Models\RusunawaCost;
use App\Models\Transaction;
use Filament\Actions;
use Illuminate\Database\Eloquent\Model;
use Livewire\Component;

class CreateServiceAction extends Component
{
    public function createAction() {
        $retributor_id = 1;
        return Actions\CreateAction::make()
        ->mutateFormDataUsing(function (array $data) use ($retributor_id): array {
            $data['retributor_id'] = (int) $retributor_id;
            $data['status'] = 'active';
            return $data;
        })->using(function (array $data, string $model): Model {

            $upt = $data['upt'];
            $npwrd_code = Retributor::find($data['retributor_id'])->npwrd_code;

            // var_dump($data['heavy_tool_services']);die;

            $services = [];
            switch ($upt) {
                case 'heavy_tools':
                    $services = $data['heavy_tool_services'];
                    break;
                case 'lab':
                    $services = $data['lab_services'];
                    break;
                case 'rusunawa':
                    $services = $data['rusunawa_services'];
                    break;
                case 'iplt':
                    $services = $data['iplt_services'];
                    break;
            }
            // if($upt == 'heavy_tools'){
            foreach ($services as $product) {
                $product = explode('|', $product['services']);
                $id = $model::create([
                    'retributor_id' => $data['retributor_id'],
                    'upt' => $data['upt'],
                    'product_id' => $product[0],
                    'product_code' => $product[1],
                    'product_text' => $product[2],
                    'service_date' => $data['service_date'],
                    'repeat' => $data['repeat'],
                    'status' => $data['status']
                ]);
                $serviceDate = date('Y-m-d', strtotime($data['service_date']));

                if (date('Y-m-d') == $serviceDate) {
                    $total = $data['upt'] === 'rusunawa' ? RusunawaCost::find($product[0])->cost : 0;


                    if($data['upt'] === 'rusunawa'){
                        $rusunawaTransaction = Transaction::where('type', 'partial')->where('npwrd', $npwrd_code)->first();
                        if($rusunawaTransaction){
                            $rusunawaTransaction->total = $total;
                            $rusunawaTransaction->save();
                        }else{
                            $rusunawaTransaction = Transaction::create([
                                'npwrd' => $npwrd_code,
                                'payment_code' => now()->format('Ymd') . rand(1000, 9999),
                                'service_id' => null,
                                'attribute_code' => '',
                                'type' => 'partial',
                                'value' => '',
                                'status' => Transaction::STATUS_BILLING,
                                'subtotal' => 0, // Jangan encode json untuk subtotal, cukup angka
                                'amount' => 0, // Jangan encode json untuk subtotal, cukup angka
                                'total' => 0, // Jangan encode json untuk subtotal, cukup angka
                                'payment_expired' => null, // Set expired date satu hari dari sekarang
                                'invoice_date' => date('Y-m-d') // Set expired date satu hari dari sekarang
                            ]);
                        }
                        $parent = $rusunawaTransaction->id;
                    }

                    Transaction::create([
                        'npwrd' => $npwrd_code,
                        'payment_code' => now()->format('Ymd') . rand(1000, 9999),
                        'service_id' => $id->id,
                        'attribute_code' => $product[1],
                        'value' => '[0]',
                        'status' => Transaction::STATUS_BILLING,
                        'type' => 'unit',
                        'parent_id' => isset($parent) ? $parent : null,
                        'subtotal' => 0, // Jangan encode json untuk subtotal, cukup angka
                        'amount' => 0, // Jangan encode json untuk subtotal, cukup angka
                        'total' => $total, // Jangan encode json untuk subtotal, cukup angka
                        'payment_expired' => null, // Set expired date satu hari dari sekarang
                        'invoice_date' => $data['service_date'] // Set expired date satu hari dari sekarang
                    ]);
                }
            }
            // }
            return $model::find(1);
        });
    }
    public function render()
    {
        return view('livewire.create-service-action');
    }
}
