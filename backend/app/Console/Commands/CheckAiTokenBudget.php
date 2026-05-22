<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\AiTokenLog;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class CheckAiTokenBudget extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ai:check-budget {--threshold=5000000}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check if the monthly AI token usage exceeds the threshold.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $threshold = (int) $this->option('threshold');
        $currentMonth = Carbon::now()->startOfMonth();

        $totalTokensThisMonth = AiTokenLog::where('created_at', '>=', $currentMonth)->sum('total_tokens');

        if ($totalTokensThisMonth >= $threshold) {
            $msg = "ALERTA CRÍTICA: El consumo de IA de este mes ha superado el presupuesto de tokens ({$totalTokensThisMonth} >= {$threshold}). Revisa el panel de costes inmediatamente.";
            
            Log::critical($msg);
            $this->error($msg);
            
            // TODO: Integrar notificación Slack o Email
            
            return Command::FAILURE;
        }

        $this->info("Consumo IA en rango seguro: {$totalTokensThisMonth} tokens de {$threshold} límite mensual.");
        return Command::SUCCESS;
    }
}
