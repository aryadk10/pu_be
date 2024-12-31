<?php

namespace App\Filament\Resources\ServiceRetributorResource\Pages;

use App\Filament\Resources\ServiceRetributorResource;
use App\Models\Iplt;
use App\Models\Retributor;
use App\Models\RusunawaCost;
use App\Models\ServiceRetributor;
use App\Models\Transaction;
use App\Models\TransactionDetail;
use Filament\Actions;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;
use Filament\Resources\Pages\ManageRecords;
use Illuminate\Contracts\View\View;

class ManageServiceRetributors extends ManageRecords
{
    protected static string $resource = ServiceRetributorResource::class;

    public $retributor_id;

    function mount(): void
    {
        $this->retributor_id = request()->input('retributor');
        request()->session()->put('sr_retributor_id', $this->retributor_id);
    }

    public function edit($recordId)
    {
        $record = ServiceRetributor::findOrFail($recordId)->toArray();
        $record['iplt_services'] = is_string($record->iplt_services) ?json_decode($record->iplt_services) : [];
        $record['heavy_tool_services'] = is_string($record->heavy_tool_services) ?json_decode($record->heavy_tool_services) : [];
        $record['rusunawa_services'] = is_string($record->rusunawa_services) ?json_decode($record->rusunawa_services) : [];
        $record['lab_services'] = is_string($record->lab_services) ?json_decode($record->lab_services) : [];

        $this->form->fill($record);
    }

    protected function getHeaderActions(): array
    {
        $retributor_id = $this->retributor_id;
        return [

        ];
    }



    public function getHeader(): ?View
    {
        return view('custom.service-retributor.custom-header');
    }
}
