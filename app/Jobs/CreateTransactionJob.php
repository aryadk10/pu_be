<?php

namespace App\Jobs;

use App\Models\ServiceRetributor;
use App\Models\Transaction;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Throwable;

class CreateTransactionJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // Ambil tanggal hari ini
        $today = Carbon::now();
        Log::debug('Job created at ' . $today->toDateString());

        // Get the day, month, and year
        $day = $today->day;
        $month = $today->month;
        $year = $today->year;

        // Determine the days to query based on month and year
        $daysToQuery = [$day]; // Start with today

        // Handle February
        if ($month == 2) {
            // If it's February, check if it's a leap year
            if ($day == 29 && (int) $year % 4 == 0) {
                $daysToQuery = [29,30,31]; // If the date is 29 and it's not a leap year, just check 28
            } elseif ($day == 28 && (int) $year % 4 == 1 ) {
                $daysToQuery = [28,29,30,31];
            }
        }

        // Handle months with 30 days (April, June, September, November)
        elseif (in_array($month, [4, 6, 9, 11]) && $day == 30) {
            $daysToQuery = [30,31];
        }

        // Handle other months (31 days)
        elseif (in_array($month, [1, 3, 5, 7, 8, 10, 12]) && $day == 31) {
            $daysToQuery = [31];
        }

        // Query dengan pengecekan day dari tanggal 'service_date'
        $services = ServiceRetributor::whereRaw('DAY(service_date) IN (?)', [implode(',', $daysToQuery)])
            ->where('repeat', 1)
            ->get();

        Log::debug('$services ' . json_encode($services));
        foreach ($services as $service) {
            // Cek jika belum ada transaksi untuk service_id ini
            $existingTransaction = Transaction::where('service_id', $service->id)
                ->whereDate('created_at', now()->toDateString())
                ->exists();

            if (!$existingTransaction) {
                $transactionHandler = new Transaction();
                $data = [
                    'npwrd' => $service->retributor->npwrd_code,
                    'upt' => $service->upt,
                    'service_date' => $service->service_date
                ];

                $product = [$service->product_id, $service->product_code, $service->product_text];
                try {
                    $transactionHandler->handleTransactions($data, $product, $service->id);
                    Log::info($service->id . ' - ' . $service->product_text . ' success created');
                } catch (Throwable $error) {
                    Log::error($error->getMessage());
                }
            }
        }
    }
}
