<?php

namespace App\Filament\Resources\WidgetsResource\Widgets;

use Filament\Widgets\ChartWidget;
use Carbon\Carbon;
use App\Models\Transaction;
use Filament\Widgets\Concerns\InteractsWithPageFilters;

class PendapatanChart extends ChartWidget
{
    protected static ?string $heading = 'Statistik Pendapatan';
    protected int | string | array $columnSpan = 'full';

    use InteractsWithPageFilters;

    public function getTransactionStats()
    {
        $date = $this->filters['date'] ?? Carbon::now()->format('d/m/Y') . ' - ' . Carbon::now()->format('d/m/Y');

        // Pisahkan tanggal berdasarkan pemisah "-"
        list($start, $end) = explode(' - ', $date);

        // Gunakan Carbon untuk mengonversi dan menetapkan startOfDay dan endOfDay
        $startDate = $start ? Carbon::createFromFormat('d/m/Y', $start)->startOfDay() : null;
        $endDate = $end ? Carbon::createFromFormat('d/m/Y', $end)->endOfDay() : null;

        if (!$startDate || !$endDate) {
            throw new \Exception('Tanggal tidak valid.');
        }

        // Mendapatkan label tanggal dari $startDate hingga $endDate
        $labels = collect();
        $currentDate = $startDate->copy();

        while ($currentDate <= $endDate) {
            $labels->push($currentDate->format('d/M'));
            $currentDate->addDay();
        }

        // Inisialisasi array untuk data chart
        $waitingPaymentData = [];
        $successfulTransactionData = [];

        $upt = $this->filters['upt'] ?? [];
        $category = $this->filters['category'] ?? [];

        // Mengambil jumlah transaksi untuk setiap tanggal dalam periode
        $currentDate = $startDate->copy();
        while ($currentDate <= $endDate) {
            $day = $currentDate->format('Y-m-d');

            // Mendapatkan jumlah transaksi "Menunggu Pembayaran" untuk tanggal ini
            $waitingPaymentData[] = Transaction::whereIn('status', ['pending','unpaid'])
                ->whereDate('created_at', '=', $day)
                ->whereIn('type', ['unit_payment','unit'])
                ->when($upt, fn($query) => $query->whereHas('service', fn($query) => $query->whereIn('upt', $upt)))
                ->when($category, fn($query) => $query->whereHas('service', fn($query) => $query->whereIn('product_text', $category)))
                ->sum('total');

            // Mendapatkan jumlah transaksi "Transaksi Berhasil" untuk tanggal ini
            $successfulTransactionData[] = Transaction::where('status', 'paid')
                ->whereDate('created_at', '=', $day)
                ->whereIn('type', ['unit_payment'])
                ->when($upt, fn($query) => $query->whereHas('service', fn($query) => $query->whereIn('upt', $upt)))
                ->when($category, fn($query) => $query->whereHas('service', fn($query) => $query->whereIn('product_text', $category)))
                ->sum('total');

            $currentDate->addDay();
        }


        // Format data untuk chart
        $chartData = [
            'datasets' => [
                [
                    'label' => 'Menunggu pembayaran',
                    'data' => $waitingPaymentData,
                    'borderColor' => '#E9A62A',
                    'backgroundColor' => '#E9A62A',
                ],
                [
                    'label' => 'Transaksi Berhasil',
                    'data' => $successfulTransactionData,
                    'borderColor' => '#2ADA88',
                    'backgroundColor' => '#2ADA88',
                ],
            ],
            'labels' => $labels->toArray(),
        ];

        return $chartData; // Menampilkan data chart dalam bentuk JSON
    }


    protected function getData(): array
    {
        return $this->getTransactionStats();
        // return [
        //     'datasets' => [
        //         [
        //             'label' => 'Menunggu pembayaran',
        //             'data' => [0, 10, 5, 2, 21, 32, 45, 74, 65, 45, 77, 89],
        //             'borderColor' => '#E9A62A',
        //             'backgroundColor' => '#E9A62A',
        //         ],
        //         [
        //             'label' => 'Transaksi Berhasil',
        //             'data' => [0, 10, 5, 2, 21, 32, 45, 74, 65, 45, 77, 89],
        //             'borderColor' => '#2ADA88',
        //             'backgroundColor' => '#2ADA88',
        //         ],
        //     ],
        //     'labels' => ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
        // ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
