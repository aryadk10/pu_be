<?php

namespace App\Filament\Resources\WidgetsResource\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\Transaction;
use Filament\Widgets\Concerns\InteractsWithPageFilters;

class PendapatanUptChart extends ChartWidget
{
    use InteractsWithPageFilters;

    protected static ?string $heading = 'Total Pendapatan Berdasarkan UPT';

    protected function getData(): array
    {
        $date = $this->filters['date'] ?? \Carbon\Carbon::now()->format('d/m/Y') . ' - ' . \Carbon\Carbon::now()->format('d/m/Y');

        // Pisahkan tanggal berdasarkan pemisah "-"
        list($start, $end) = explode(' - ', $date);

        // Gunakan Carbon untuk mengonversi dan menetapkan startOfDay dan endOfDay
        $startDate = $start ? \Carbon\Carbon::createFromFormat('d/m/Y', $start)->startOfDay() : null;
        $endDate = $end ? \Carbon\Carbon::createFromFormat('d/m/Y', $end)->endOfDay() : null;

        if (!$startDate || !$endDate) {
            throw new \Exception('Tanggal tidak valid.');
        }

        // Kategori UPT yang akan digunakan dalam chart
        $categories = [
            'iplt' => 'IPLT',
            'heavy_tools' => 'Alat Berat',
            'rusunawa' => 'Rusunawa',
            'lab' => 'Laboratorium',
        ];
        $upt = $this->filters['upt'] ?? [];
        $category = $this->filters['category'] ?? [];
        $labels = [];

        // Data untuk chart
        $data = [];

        // Menghitung jumlah transaksi untuk setiap kategori UPT
        foreach ($upt as $key) {
            $labels[] = $categories[$key];
            // Query untuk mendapatkan jumlah transaksi berdasarkan UPT
            $count = Transaction::whereHas('service', function ($query) use ($key) {
                $query->where('upt', $key); // Asumsi ada kolom `upt` pada relasi service
            })
                ->when($category, fn($query) => $query->whereHas('service', fn($query) => $query->whereIn('product_text', $category)))
                ->when($startDate, fn($query) => $query->whereDate('created_at', '>=', $startDate))
                ->when($endDate, fn($query) => $query->whereDate('created_at', '<=', $endDate))
                ->whereIn('status', ['paid'])
                ->whereIn('type', ['unit_payment'])
                ->sum('total');

            // Menambahkan data hasil hitung untuk kategori UPT ke dalam array data
            $data[] = $count;
        }

        // Mengembalikan data untuk chart
        return [
            'datasets' => [
                [
                    'data' => $data,
                    'backgroundColor' => array_values([
                        'danger' => '#C13056',
                        'info' => '#DEF1F7',
                        'primary' => '#E9A62A',
                        'success' => '#24758F',
                        'warning' => '#EC8815',
                    ]), // Warna untuk setiap kategori UPT
                ]
            ],
            'labels' => $labels, // Mengambil hanya nilai kategori untuk label chart
        ];
    }


    protected function getType(): string
    {
        return 'pie';
    }
}
