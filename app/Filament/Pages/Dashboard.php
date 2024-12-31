<?php

namespace App\Filament\Pages;

use App\Models\HeavyToolCost;
use App\Models\LabCost;
use App\Models\RusunawaCost;
use Filament\Actions;
// use Filament\Forms\Components\Actions;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Pages\Dashboard as BaseDashboard;
use Filament\Pages\Dashboard\Concerns\HasFiltersForm;
use Malzariey\FilamentDaterangepickerFilter\Fields\DateRangePicker;

class Dashboard extends \Filament\Pages\Dashboard
{
    use HasFiltersForm;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ActionGroup::make([
                Actions\Action::make('pdf')->label('Unduh PDF (*.pdf)'),
                Actions\Action::make('csv')->label('Unduh CSV (*.csv)'),
                Actions\Action::make('xlsx')->label('Unduh Excel (*.xlsx)'),
            ])->label('Unduh Data')
            ->icon('heroicon-m-ellipsis-vertical')
            ->color('primary')
            ->button(),
        ];
    }

    public function filtersForm(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()
                    ->schema([
                        CheckboxList::make('upt')
                            ->options([
                                'iplt' => 'IPLT',
                                'heavy_tools' => 'Alat Berat',
                                'rusunawa' => 'Rusunawa',
                                'lab' => 'Laboratorium',
                            ])
                            ->columns(2)
                            ->live()
                            ->gridDirection('row'),
                        DateRangePicker::make('date')->defaultToday(),
                        Select::make('category')->label('Kategori')->placeholder('Pilih Kategori')
                            ->multiple()
                            ->searchable()
                            ->options(function (Get $get) {
                                $upt = $get('upt');
                                $data = [];
                                if (in_array('iplt', $upt)) {
                                    $data = [
                                        'Sedot lumpur & tinja, Dalam Kota' => 'Sedot lumpur & tinja, Dalam Kota',
                                        'Sedot lumpur & tinja, Luar Kota' => 'Sedot lumpur & tinja, Luar Kota'
                                    ];
                                }
                                if (in_array('lab', $upt)) {
                                    $lab = LabCost::join('lab_categories', 'lab_costs.category_id', '=', 'lab_categories.id')
                                        ->selectRaw("CONCAT(lab_categories.description, ', ', lab_costs.description) AS cost_id")
                                        ->get()
                                        ->pluck('cost_id', 'cost_id')->toArray();
                                    $data = array_merge($data,$lab );
                                }

                                if (in_array('rusunawa', $upt)) {
                                    $rus = RusunawaCost::join('rusunawa', 'rusunawa_costs.rusunawa_id', '=', 'rusunawa.id')
                                        ->selectRaw("CONCAT(rusunawa.description, ', ', rusunawa_costs.description) AS cost_id")
                                        ->get()
                                        ->pluck('cost_id', 'cost_id')
                                        ->toArray();
                                    $data = array_merge($data, $rus);
                                }

                                if (in_array('heavy_tools', $upt)) {
                                    $ab = HeavyToolCost::join('heavy_tools', 'heavy_tool_costs.heavy_tool_id', '=', 'heavy_tools.id')
                                        ->selectRaw("CONCAT(heavy_tools.description, ', ', heavy_tool_costs.area) AS cost_id")
                                        ->get()
                                        ->pluck('cost_id', 'cost_id')
                                        ->toArray();
                                    $data = array_merge($data, $ab);
                                }

                                return $data;
                            })
                    ])
                    ->columns(4),
            ]);
    }
}
