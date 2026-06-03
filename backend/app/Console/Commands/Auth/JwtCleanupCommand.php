<?php

namespace App\Console\Commands\Auth;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Redis;

class JwtCleanupCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'auth:jwt-cleanup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Limpia la blacklist de JWT en Redis eliminando tokens expirados';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Iniciando limpieza de JWT blacklist...');

        // Eliminar tokens cuyo tiempo de gracia (score) sea menor al tiempo actual
        $removed = Redis::zremrangebyscore('jwt_blacklist', '-inf', time());

        $this->success("Limpieza completada. Tokens eliminados: {$removed}");
    }
}
