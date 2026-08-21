<?php

namespace App\Console\Commands;

use App\Models\SubscriptionTransaction;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class CheckExpiredSubscriptions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'subscriptions:check-expired';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check user subscriptions and suspend those that have expired';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $now = Carbon::now();

        $expiredSubscriptions = SubscriptionTransaction::where('status', 'active')
            ->whereNotNull('expires_at')
            ->where('expires_at', '<=', $now)
            ->get();

        $count = $expiredSubscriptions->count();

        if ($count > 0) {
            foreach ($expiredSubscriptions as $sub) {
                $sub->update([
                    'status' => 'suspended',
                ]);
            }

            $message = "Successfully suspended {$count} expired subscription(s).";
            $this->info($message);
            Log::info("[CheckExpiredSubscriptions] {$message}");
        } else {
            $message = "No expired active subscriptions found.";
            $this->info($message);
            Log::info("[CheckExpiredSubscriptions] {$message}");
        }

        return Command::SUCCESS;
    }
}
