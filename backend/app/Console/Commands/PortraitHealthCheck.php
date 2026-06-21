<?php

namespace App\Console\Commands;

use App\Models\UserPortrait;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class PortraitHealthCheck extends Command
{
    protected $signature = 'portraits:health-check';

    protected $description = 'Check health of user portrait data: count active portraits with zero tags';

    public function handle()
    {
        $totalActive = UserPortrait::where('status', 'active')->count();
        $emptyActive = UserPortrait::where('status', 'active')
            ->whereDoesntHave('tags')
            ->count();

        $emptyRate = $totalActive > 0 ? ($emptyActive / $totalActive) * 100 : 0;

        $this->table(
            ['Metric', 'Value'],
            [
                ['Total Active Portraits', $totalActive],
                ['Active Portraits with 0 Tags', $emptyActive],
                ['Empty Portrait Rate', number_format($emptyRate, 2) . '%'],
            ]
        );

        $level = 'info';
        $message = "[Portrait Health Check] active={$totalActive}, empty={$emptyActive}, rate=" . number_format($emptyRate, 2) . "%";

        if ($emptyRate >= 20) {
            $level = 'warning';
            $this->warn("WARNING: Empty portrait rate is " . number_format($emptyRate, 2) . "%, which exceeds the 20% warning threshold.");
            Log::warning($message, ['threshold' => '20%']);
        } elseif ($emptyRate >= 10) {
            $this->warn("NOTICE: Empty portrait rate is " . number_format($emptyRate, 2) . "%, approaching warning threshold.");
            Log::notice($message);
        } else {
            $this->info("Health check passed. Empty portrait rate is " . number_format($emptyRate, 2) . "%.");
            Log::info($message);
        }

        return $emptyRate >= 20 ? 1 : 0;
    }
}
