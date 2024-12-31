<?php

namespace App\Livewire;

use App\Models\Post;
use App\Models\Retributor;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Illuminate\Contracts\View\View;
use Livewire\Component;
use Illuminate\Support\Facades\Storage;



class RetributorHeader extends Component implements HasForms
{
    use InteractsWithForms;

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill();
        $retributorId = request()->input('retributor') ?? request()->session()->get('sr_retributor_id');
        $retributor = Retributor::find($retributorId);
        $this->data['ktp_url'] = Storage::url($retributor->ktp_photo);
        $this->data['kk_url'] = Storage::url($retributor->family_card_photo);
        $this->data['passport_url'] = Storage::url($retributor->passport_photo);
        $this->data['seritifikat_url'] = Storage::url($retributor->certificate_no_home_ownership);

    }

    public function form(Form $form): Form
    {
        $retributorId = request()->input('retributor') ?? request()->session()->get('sr_retributor_id');

        $tipe = 'Individus';
        $retributor = Retributor::find($retributorId);
        return $form
            ->schema([
                Grid::make([
                    'default' => 3,
                ])
                    ->schema([
                        Placeholder::make('Tipe Retributor')->content(function () use ($retributor) { return  $retributor->retributor_type;}),
                        Placeholder::make('Kode NPWRD')->content(function () use ($retributor) { return  $retributor->npwrd_code;}),
                        Placeholder::make(''),
                        Placeholder::make('Nama')->content(function () use ($retributor) { return  $retributor->first_name.' '.$retributor->last_name;}),
                        Placeholder::make('Nomor telp')->content(function () use ($retributor) { return  $retributor->phone_number;}),
                        Placeholder::make('Email')->content(function () use ($retributor) { return  $retributor->email;}),
                        Placeholder::make('Alamat')->content(function () use ($retributor) { return  $retributor->address;}),
                    ])
                // ...
            ])
            ->statePath('data');
    }

    public function create(): void
    {
        dd($this->form->getState());
    }

    public function render(): View
    {
        return view('livewire.retributor-header');
    }
}
