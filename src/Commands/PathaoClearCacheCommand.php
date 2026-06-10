<?php

namespace devrkb21\PathaoLaravel\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class PathaoClearCacheCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pathao:clear-cache';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clear the cached locations database';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->line('Clearing cached locations...');

        try {
            DB::table('pathao_cities')->truncate();
            DB::table('pathao_zones')->truncate();
            DB::table('pathao_areas')->truncate();
            $this->info('Cached location tables cleared successfully!');
        } catch (\Exception $e) {
            $this->error('Failed to clear cached locations: '.$e->getMessage());

            return 1;
        }

        return 0;
    }
}
