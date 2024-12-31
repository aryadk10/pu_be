<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ServiceRetributorResource\Pages;
use App\Filament\Resources\ServiceRetributorResource\RelationManagers;
use App\Models\HeavyToolCost;
use App\Models\Iplt;
use App\Models\LabCost;
use App\Models\Retributor;
use App\Models\RusunawaCost;
use App\Models\ServiceRetributor;
use App\Models\Transaction;
use Filament\Forms;
use Filament\Actions;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Wizard;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Illuminate\Database\Eloquent\Model;

class ServiceRetributorResource extends Resource
{
    protected static ?string $model = ServiceRetributor::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static bool $shouldRegisterNavigation = false;

    private $id;
    // public function mount($record)
    // {
    //     $this->id = request()->route('retributor'); // Mendapatkan route parameter
    //     // var_dump("Record ID: $id");die;
    // }



    public static function form(Form $form): Form
    {
        $retribtor = URL::current();

        return $form
            ->schema([
                // TextInput::make('name')->default($retribtor),
                Select::make('upt')
                    ->label('Pilih UPT')
                    ->options(['iplt' => 'IPLT', 'lab' => 'Lab', 'heavy_tools' => 'Alat Berat', 'rusunawa' => 'Rusunawa'])
                    ->searchable()
                    ->live()
                    ->columnSpan(2),
                TextInput::make('layanan')
                    ->label('Pilih Layanan')
                    ->default('Sedot lumpur & tinja')
                    ->columnSpan(2)
                    ->disabled()
                    ->hidden(function (Get $get) {
                        return $get('upt') != "iplt";
                    }),
                Select::make('iplt_services')
                    ->options(['Dalam Kota' => 'Dalam Kota', 'Luar Kota' => 'Luar Kota'])
                    ->searchable()
                    ->hiddenLabel()
                    ->columnSpan(2)
                    ->live()
                    ->hidden(function (Get $get) {
                        return $get('upt') != "iplt";
                    }),
                Repeater::make('heavy_tool_services')
                    ->label('Layanan Alat Berat')
                    ->columnSpan(2)
                    ->schema([
                        Select::make('services')
                            ->label('Pilih Layanan')
                            ->options(function () {
                                $data = HeavyToolCost::join('heavy_tools', 'heavy_tool_costs.heavy_tool_id', '=', 'heavy_tools.id')
                                    ->selectRaw("CONCAT(heavy_tools.description,' (',heavy_tool_costs.area, ') - ', heavy_tool_costs.cost) AS cost_description, CONCAT(heavy_tools.id,'|',heavy_tools.code,'|',heavy_tools.description, ', ', heavy_tool_costs.area) AS cost_id")
                                    ->get()
                                    ->pluck('cost_description', 'cost_id');

                                return $data;
                            })
                            ->columnSpan(2),
                    ])
                    ->hidden(function (Get $get) {
                        return $get('upt') != "heavy_tools";
                    }),
                Repeater::make('rusunawa_services')
                    ->label('Layanan Rusunawa')
                    ->columnSpan(2)
                    ->schema([
                        Select::make('services')
                            ->label('Pilih Layanan')
                            ->options(function () {
                                // return RusunawaCost::all()->pluck('description', 'id');
                                return RusunawaCost::join('rusunawa', 'rusunawa_costs.rusunawa_id', '=', 'rusunawa.id')
                                    ->selectRaw("CONCAT(rusunawa.description,' (',rusunawa_costs.description, ') - ', rusunawa_costs.cost) AS cost_description, CONCAT(rusunawa.id,'|',rusunawa.code,'|',rusunawa.description, ', ', rusunawa_costs.description) AS cost_id")
                                    ->get()
                                    ->pluck('cost_description', 'cost_id');
                            })
                            ->columnSpan(2),
                    ])
                    ->hidden(function (Get $get) {
                        return $get('upt') != "rusunawa";
                    }),
                Repeater::make('lab_services')
                    ->label('Layanan Laboratorium')
                    ->columnSpan(2)
                    ->schema([
                        Select::make('services')
                            ->label('Pilih Layanan')
                            ->options(function () {
                                // return LabCost::all()->pluck('description', 'id');
                                $data = LabCost::join('lab_categories', 'lab_costs.category_id', '=', 'lab_categories.id')
                                    ->selectRaw("CONCAT(lab_categories.description, ' (' ,lab_costs.description, ') - ', lab_costs.cost) AS cost_description, CONCAT(lab_categories.id,'|',lab_categories.code,'|',lab_categories.description, ', ', lab_costs.description) AS cost_id")
                                    ->get()
                                    ->pluck('cost_description', 'cost_id');
                                return $data;
                            })
                            ->columnSpan(2),
                    ])
                    ->hidden(function (Get $get) {
                        return $get('upt') != "lab";
                    }),
                DatePicker::make('service_date')
                    ->label(
                        function (Get $get) {
                            switch ($get('upt')) {
                                case "heavy_tools":
                                    return 'Pilih tanggal peminjaman';
                                case "rusunawa":
                                    return 'Pilih tanggal tagihan';
                                case "lab":
                                    return 'Pilih tanggal pengujian';
                                case "iplt":
                                    return 'Pilih tanggal penyedotan';
                            }
                        }
                    )
                    ->columnSpan(2)
                    ->minDate(now()->startOfDay())
                    ->hidden(function (Get $get) {
                        return !$get('upt');
                    }),
                Checkbox::make('repeat')
                    ->label('Ulangi pada bulan berikutnya')
                    ->columnSpan(2)
                    ->hidden(function (Get $get) {
                        return $get('upt') !== 'rusunawa';
                    }),
                Placeholder::make('service_cost')->label('Biaya Layanan')->content(function (Get $get) {
                    $area = $get('iplt_services');
                    $iplt = Iplt::where('area', $area)->where('description', 'Sedot lumpur & tinja')->first();

                    return $iplt->cost ?? 0;
                })->hidden(function (Get $get) {
                    return $get('upt') != 'iplt';
                }),
                Placeholder::make('transport_cost')->label('Biaya Transportasi')->content(function (Get $get) {
                    $area = $get('iplt_services');
                    $iplt = Iplt::where('area', $area)->where('description', 'Biaya transportasi')->first();

                    return $iplt->cost ?? 0;
                })->hidden(function (Get $get) {
                    return $get('upt') != 'iplt';
                })

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(function (Builder $query) {
                $retributorId = request()->input('retributor') ?? request()->session()->get('sr_retributor_id');

                if ($retributorId) {
                    $query->where('retributor_id', $retributorId);
                }
            })
            ->columns([
                Tables\Columns\TextColumn::make('no')
                    ->rowIndex()
                    ->label('No')
                    ->sortable(),
                Tables\Columns\TextColumn::make('product_text')
                    ->label('Kategori Produk Layanan')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('service_date')
                    ->label('Tanggal')
                    ->sortable(),
                Tables\Columns\TextColumn::make('repeat')
                    ->label('Berulang')
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->sortable(),
            ])
            ->filters([])
            ->headerActions([
                Tables\Actions\CreateAction::make()->mutateFormDataUsing(function (array $data): array {
                    $retributor_id = request()->session()->get('sr_retributor_id') ?? null;
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
                    }
                    if($upt == 'iplt'){
                        $data['iplt_services'];

                        $product = Iplt::where('area',$data['iplt_services'])->where('description','Sedot lumpur & tinja')->first();

                        $service = $model::create([
                            'retributor_id' => $data['retributor_id'],
                            'upt' => $data['upt'],
                            'product_id' => $product->id,
                            'product_code' => $product->code,
                            'product_text' => $product->description.', '.$product->area,
                            'service_date' => $data['service_date'],
                            'repeat' => $data['repeat'] ?? 0,
                            'status' => $data['status']
                        ]);
                        $serviceDate = date('Y-m-d', strtotime($data['service_date']));

                        if (date('Y-m-d') == $serviceDate || 1 == 1) {
                            $data['npwrd'] = $npwrd_code;
                            $transactionHandler = new Transaction();
                            $transactionHandler->handleTransactions($data, $product, $service->id);
                        }

                    }else{
                        foreach ($services as $product) {
                            $product = explode('|', $product['services']);
                            $service = $model::create([
                                'retributor_id' => $data['retributor_id'],
                                'upt' => $data['upt'],
                                'product_id' => $product[0],
                                'product_code' => $product[1],
                                'product_text' => $product[2],
                                'service_date' => $data['service_date'],
                                'repeat' => $data['repeat'] ?? 0,
                                'status' => $data['status']
                            ]);
                            $serviceDate = date('Y-m-d', strtotime($data['service_date']));

                            if (date('Y-m-d') == $serviceDate || 1 == 1) {
                                $data['npwrd'] = $npwrd_code;
                                $transactionHandler = new Transaction();
                                $transactionHandler->handleTransactions($data, $product, $service->id);
                            }
                        }
                    }
                    // }
                    return $model::find($service->id);
                })
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->modalDescription('Data ini tidak akan dapat digunakan lagi setelah dilanjutkan untuk dihapus dan akan memutus relevansi pada data lainnya'),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageServiceRetributors::route('/'),
        ];
    }
}
