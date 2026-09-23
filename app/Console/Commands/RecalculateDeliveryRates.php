<?php

namespace App\Console\Commands;

use App\Models\Client;
use Illuminate\Console\Command;

class RecalculateDeliveryRates extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'clients:recalculate-delivery-rates';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Recalculate and persist the delivery_rate for all clients based on their delivered/returned orders.';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Recalculating delivery rates for all clients...');

        $total = Client::withTrashed()->count();
        $bar   = $this->output->createProgressBar($total);
        $bar->start();

        Client::withTrashed()->chunkById(200, function ($clients) use ($bar) {
            foreach ($clients as $client) {
                $client->recalculateDeliveryRate();
                $bar->advance();
            }
        });

        $bar->finish();
        $this->newLine();
        $this->info("Done! Processed {$total} client(s).");

        return self::SUCCESS;
    }
}
