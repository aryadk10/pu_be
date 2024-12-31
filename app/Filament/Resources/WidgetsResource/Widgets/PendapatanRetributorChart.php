<?php

namespace App\Filament\Resources\WidgetsResource\Widgets;

use App\Models\Transaction;
use Filament\Widgets\ChartWidget;
use Filament\Widgets\Concerns\InteractsWithPageFilters;

class PendapatanRetributorChart extends ChartWidget
{
    use InteractsWithPageFilters;

    protected static ?string $heading = 'Total Pendapatan Berdasarkan Jenis Retributor';

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
            'Individu' => 'Individu',
            'Nirlaba' => 'Nirlaba',
            'Perusahaan' => 'Perusahaan'
        ];
        $category = $this->filters['category'] ?? [];
        $upt = $this->filters['upt'] ?? [];

        // Data untuk chart
        $data = [];

        // Menghitung jumlah transaksi untuk setiap kategori UPT
        foreach ($categories as $key => $label) {
            // Query untuk mendapatkan jumlah transaksi berdasarkan UPT
            $count = Transaction::whereHas('retributor', function ($query) use ($key) {
                $query->where('retributor_type', $key); // Asumsi ada kolom `upt` pada relasi service
            })
                ->when($upt, fn($query) => $query->whereHas('service', fn($query) => $query->whereIn('upt', $upt)))
                ->when($category, fn($query) => $query->whereHas('service', fn($query) => $query->whereIn('product_text', $category)))
                ->when($startDate, fn($query) => $query->whereDate('created_at', '>=', $startDate))
                ->when($endDate, fn($query) => $query->whereDate('created_at', '<=', $endDate))
                ->whereIn('status', ['paid'])
                ->whereIn('type', ['unit_payment'])
                ->sum('total');

            // Menambahkan data hasil hitung untuk kategori UPT ke dalam array data
            $data[] = $count;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Menunggu pembayaran',
                    'data' => $data,
                    'backgroundColor' => array_values([
                        'primary' => '#E9A62A',
                        'success' => '#24758F',
                        'warning' => '#EC8815',
                    ]),
                ]
            ],
            'labels' => ['Individu', 'Nirlaba', 'Perusahaan']
        ];
    }

    protected function getType(): string
    {
        return 'pie';
    }
}
