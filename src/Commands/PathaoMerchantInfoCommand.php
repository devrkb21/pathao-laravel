<?php

namespace devrkb21\PathaoLaravel\Commands;

use devrkb21\PathaoLaravel\APIBase\PathaoAuth;
use devrkb21\PathaoLaravel\Facades\PathaoLaravel;
use devrkb21\PathaoLaravel\Services\PathaoHelperFunction;
use Illuminate\Console\Command;

class PathaoMerchantInfoCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pathao:merchant-info';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check connection to Pathao API and retrieve short merchant profile info';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Checking Pathao API connection status...');

        try {
            // Check if token exists, if not refresh it
            $tokenData = PathaoHelperFunction::getPathaoTokenData();
            if (! $tokenData) {
                $this->warn('No active token found in database. Attempting fresh token exchange...');
                $auth = new PathaoAuth;
                $result = $auth->getNewAccesstoken();
                if (! $result->isSuccess()) {
                    $this->error('Connection Failed: Could not authenticate with credentials. '.$result->getMessage());

                    return 1;
                }
                $tokenData = PathaoHelperFunction::getPathaoTokenData();
            }

            // Retrieve merchant info
            $response = PathaoLaravel::GET_MERCHANT_INFO();
            $data = $response['data']['data'] ?? $response['data'] ?? [];

            if (! empty($data['merchant_name'])) {
                $this->newLine();
                $this->info('==================================================');
                $this->info('         CONNECTION SUCCESSFUL!');
                $this->info('==================================================');
                $this->line('  - Merchant ID:     '.($data['merchant_id'] ?? 'N/A'));
                $this->line('  - Merchant Name:   '.$data['merchant_name']);
                $this->line('  - Merchant Email:  '.($data['merchant_email'] ?? 'N/A'));
                $this->line('  - Contact Number:  '.($data['merchant_contact_number'] ?? 'N/A'));
                $this->line('  - Country:         '.($data['country_id'] == 1 ? 'Bangladesh' : 'Nepal'));
                $this->info('==================================================');
                $this->newLine();
            } else {
                $this->error('Connection Failed: Invalid or empty response received from user profile endpoint.');
                $this->line('Raw response: '.json_encode($response));

                return 1;
            }
        } catch (\Exception $e) {
            $this->error('Connection Failed: Exception occurred: '.$e->getMessage());

            return 1;
        }

        return 0;
    }
}
