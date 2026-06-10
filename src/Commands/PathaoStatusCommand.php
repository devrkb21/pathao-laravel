<?php

namespace devrkb21\PathaoLaravel\Commands;

use devrkb21\PathaoLaravel\Facades\PathaoLaravel;
use devrkb21\PathaoLaravel\Services\PathaoHelperFunction;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class PathaoStatusCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pathao:status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Display connection details, credentials status, and cached location counts';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->newLine();
        $this->info('==================================================');
        $this->info('             PATHAO COURIER STATUS');
        $this->info('==================================================');

        // 1. Connection Config
        $clientId = config('pathao.pathao_client_id');
        $sandbox = config('pathao.sandbox', false);
        $tableName = PathaoHelperFunction::getTableName();
        $secretToken = PathaoHelperFunction::getSecretToken();

        $this->newLine();
        $this->line('Configuration:');
        $this->line('  - Environment:   '.($sandbox ? 'Sandbox (Staging)' : 'Hermes (Production)'));
        $this->line('  - Client ID:     '.($clientId ?: 'Not Configured'));
        $this->line('  - DB Table:      '.$tableName);
        $this->line('  - Secret Token:  '.($secretToken ?: 'Not Configured'));

        // 2. Token Status
        $this->newLine();
        $this->line('Access Token Status:');
        try {
            $tokenData = PathaoHelperFunction::getPathaoTokenData();
            if ($tokenData) {
                $this->line('  - Stored Token:  Present (snippet: '.substr($tokenData->token, 0, 30).'...)');
                $this->line('  - Secret Key:    '.$tokenData->secret_token);
                $this->line('  - Expires At:    '.date('Y-m-d H:i:s', $tokenData->expires_in));

                $expiryResult = PathaoLaravel::GET_ACCESS_TOKEN_EXPIRY_DAYS_LEFT();
                if (! empty($expiryResult['data']['days_left'])) {
                    $this->line('  - Time Left:     '.$expiryResult['data']['days_left'].' days');
                }
            } else {
                $this->warn('  - Stored Token:  None found in database matching secret token!');
            }
        } catch (\Exception $e) {
            $this->error('  - Error fetching token: '.$e->getMessage());
        }

        // 3. Cache Metrics
        $this->newLine();
        $this->line('Locations Database Cache:');
        try {
            $cities = DB::table('pathao_cities')->count();
            $zones = DB::table('pathao_zones')->count();
            $areas = DB::table('pathao_areas')->count();
            $this->line("  - Cities:        {$cities} cached");
            $this->line("  - Zones:         {$zones} cached");
            $this->line("  - Areas:         {$areas} cached");
        } catch (\Exception $e) {
            $this->error('  - Cache Tables:  Non-existent or migrations not run: '.$e->getMessage());
        }

        $this->newLine();
        $this->info('==================================================');
        $this->newLine();

        return 0;
    }
}
