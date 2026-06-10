<?php

namespace devrkb21\PathaoLaravel\Commands;

use devrkb21\PathaoLaravel\Facades\PathaoLaravel;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class PathaoSyncLocationsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pathao:sync-locations';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch and cache cities, zones, and areas from the Pathao API into the database';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Starting location synchronization from Pathao Courier API...');

        try {
            set_time_limit(300);
            DB::beginTransaction();

            $this->line('Truncating existing locations tables...');
            DB::table('pathao_cities')->truncate();
            DB::table('pathao_zones')->truncate();
            DB::table('pathao_areas')->truncate();

            // 1. Cities
            $this->line('Fetching cities...');
            $citiesResponse = PathaoLaravel::GET_CITIES();
            $rawCities = $citiesResponse['data']['data'] ?? $citiesResponse['data'] ?? [];
            $citiesInserted = 0;
            foreach ($rawCities as $city) {
                $id = $city['city_id'] ?? $city['id'] ?? null;
                $name = $city['city_name'] ?? $city['name'] ?? '';
                if ($id) {
                    DB::table('pathao_cities')->insert([
                        'id' => $id,
                        'name' => $name,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                    $citiesInserted++;
                }
            }
            $this->info("Cities stored successfully: {$citiesInserted}");

            // 2. Zones
            $this->line('Fetching zones in bulk...');
            $zonesResponse = PathaoLaravel::GET_ZONES_BULK();
            $rawZones = $zonesResponse['data']['data'] ?? $zonesResponse['data'] ?? [];
            $zonesInserted = 0;
            foreach ($rawZones as $zone) {
                $id = $zone['zone_id'] ?? $zone['id'] ?? null;
                $name = $zone['zone_name'] ?? $zone['name'] ?? '';
                $cityId = $zone['city_id'] ?? null;
                if ($id && $cityId) {
                    DB::table('pathao_zones')->insert([
                        'id' => $id,
                        'city_id' => $cityId,
                        'name' => $name,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                    $zonesInserted++;
                }
            }
            $this->info("Zones stored successfully: {$zonesInserted}");

            // 3. Areas
            $this->line('Fetching areas in bulk (this might take a few seconds)...');
            $areasResponse = PathaoLaravel::GET_AREAS_BULK();
            $rawAreas = $areasResponse['data']['data'] ?? $areasResponse['data'] ?? [];
            $areasInserted = 0;
            foreach ($rawAreas as $area) {
                $id = $area['area_id'] ?? $area['id'] ?? null;
                $name = $area['area_name'] ?? $area['name'] ?? '';
                $zoneId = $area['zone_id'] ?? null;
                if ($id && $zoneId) {
                    DB::table('pathao_areas')->insert([
                        'id' => $id,
                        'zone_id' => $zoneId,
                        'name' => $name,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                    $areasInserted++;
                }
            }
            $this->info("Areas stored successfully: {$areasInserted}");

            DB::commit();
            $this->newLine();
            $this->info('Locations synchronization completed successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->error('Failed to sync locations: '.$e->getMessage());

            return 1;
        }

        return 0;
    }
}
