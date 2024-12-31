<?php

namespace App\Filament\Resources\WidgetsResource\Widgets;

use App\Models\Retributor;
use App\Models\Transaction;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Log;
use Filament\Widgets\Concerns\InteractsWithPageFilters;

class StatsOverview extends BaseWidget
{
    use InteractsWithPageFilters;

    protected function getStats(): array
    {

        $date = $this->filters['date'] ?? \Carbon\Carbon::now() . ' - ' . \Carbon\Carbon::now();

        // Pisahkan tanggal berdasarkan pemisah "-"
        list($start, $end) = explode(' - ', $date);

        // Gunakan Carbon untuk mengonversi dan menetapkan startOfDay dan endOfDay
        $startDate = $start ? \Carbon\Carbon::createFromFormat('d/m/Y', $start)->startOfDay() : null;
        $endDate = $end ? \Carbon\Carbon::createFromFormat('d/m/Y', $end)->endOfDay() : null;

        $upt = $this->filters['upt'] ?? [];
        if ((count($upt) == 1 && in_array('all', $upt))) {
            $upt = ['iplt', 'heavy_tools', 'rusunawa', 'lab'];
        }
        $category = $this->filters['category'] ?? [];

        if ($start == $end) {
            $begin = date('Y-m-d', strtotime('-1 day', strtotime($end)));
            $beginStartDate = \Carbon\Carbon::parse($begin)->startOfDay();
        } else {
            $beginStartDate = $startDate;
        }

        $total_revenues = Transaction::query()
            ->when($upt, fn($query) => $query->whereHas('service', fn($query) => $query->whereIn('upt', $upt)))
            ->when($category, fn($query) => $query->whereHas('service', fn($query) => $query->whereIn('product_text', $category)))
            ->when($beginStartDate, fn($query) => $query->whereDate('created_at', '>=', $beginStartDate))
            ->when($endDate, fn($query) => $query->whereDate('created_at', '<=', $endDate))
            ->whereIn('status', ['paid'])
            ->whereIn('type', ['unit_payment'])
            ->selectRaw('sum(total) as total,  DATE(created_at) as created_date')
            ->groupBy('created_date')
            ->get();

        // var_dump($total_today);die;
        $growth = 0;
        if ($total_revenues && count($total_revenues) > 0) {
            $before = $total_revenues[0]->total;
            $after = $total_revenues[count($total_revenues) - 1]->total;
            $growth = ($after - $before) / $before * 100;

            if ($total_revenues[0]->created_date != date('Y-m-d', strtotime($beginStartDate))) {
                $growth = 100;
            }
        }

        /** Retributor */
        $newRetributor = Retributor::query()
            ->when($beginStartDate, fn($query) => $query->whereDate('created_at', '>=', $startDate))
            ->when($endDate, fn($query) => $query->whereDate('created_at', '<=', $endDate))->count();

        return [
            Stat::make(
                label: 'Retributor',
                value: Retributor::query()->count(),
            )->description($newRetributor . ' Retributor Baru')->descriptionIcon('heroicon-m-arrow-trending-up')->color('success'),
            Stat::make(
                label: 'Pendapatan rata-rata',
                value: 'Rp ' . number_format(
                    Transaction::query()
                        ->when($upt, fn($query) => $query->whereHas('service', fn($query) => $query->whereIn('upt', $upt)))
                        ->when($startDate, fn($query) => $query->whereDate('created_at', '>=', $startDate))
                        ->when($endDate, fn($query) => $query->whereDate('created_at', '<=', $endDate))
                        ->when($category, fn($query) => $query->whereHas('service', fn($query) => $query->whereIn('product_text', $category)))
                        ->whereIn('status', ['paid'])
                        ->whereIn('type', ['unit_payment'])
                        ->avg('total'),
                    0, // Jumlah desimal
                    ',', // Pemisah desimal
                    '.' // Pemisah ribuan
                ),
            ),
            Stat::make(
                label: 'Pendapatan',
                value: 'Rp ' . number_format(
                    Transaction::query()
                        ->when($upt, fn($query) => $query->whereHas('service', fn($query) => $query->whereIn('upt', $upt)))
                        ->when($category, fn($query) => $query->whereHas('service', fn($query) => $query->whereIn('product_text', $category)))
                        ->when($startDate, fn($query) => $query->whereDate('created_at', '>=', $startDate))
                        ->when($endDate, fn($query) => $query->whereDate('created_at', '<=', $endDate))
                        ->whereIn('status', ['paid'])
                        ->whereIn('type', ['unit_payment'])
                        ->sum('total'),
                    0, // Jumlah desimal
                    ',', // Pemisah desimal
                    '.' // Pemisah ribuan
                ),
            ),
            Stat::make(
                label: 'Pertumbuhan',
                value: number_format(
                    $growth,
                    0, // Jumlah desimal
                    ',', // Pemisah desimal
                    '.'
                ) . '%',
            ),
        ];
    }
}
