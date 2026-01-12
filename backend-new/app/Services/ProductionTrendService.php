<?php

namespace App\Services;

use App\Repositories\ProductionTrendRepository;
use Illuminate\Support\Facades\Cache;

class ProductionTrendService extends BaseService
{
    protected $productionTrendRepository;

    public function __construct(ProductionTrendRepository $productionTrendRepository)
    {
        parent::__construct($productionTrendRepository);
        $this->productionTrendRepository = $productionTrendRepository;
    }

    /**
     * Get production trends data dynamically generated from Building, Room, and CCTV data
     *
     * @param string|null $startDate
     * @param string|null $endDate
     * @return array
     */
    public function getProductionTrends($startDate = null, $endDate = null)
    {
        // Create cache key based on parameters
        $cacheKey = 'production_trends';
        if ($startDate) {
            $cacheKey .= '_from_' . $startDate;
        }
        if ($endDate) {
            $cacheKey .= '_to_' . $endDate;
        }

        return Cache::remember('production_trends_grouped', 30, function () use ($startDate, $endDate) {
            // Ambil data histori yang terhubung ke CCTV beserta relasi gedungnya
            $query = \App\Models\ProductionTrend::with(['cctv.building', 'cctv.room'])
                ->whereNotNull('cctv_id');

            if ($startDate) $query->where('date', '>=', $startDate);
            if ($endDate) $query->where('date', '<=', $endDate);

            $records = $query->get();

            if ($records->isEmpty()) {
                return [];
            }

            // Group by Building and Room
            $grouped = $records->groupBy(function($record) {
                $buildingName = $record->cctv->building->name ?? 'Unknown';
                $roomName = $record->cctv->room->name ?? 'Unknown';
                return "{$buildingName} - {$roomName}";
            });

            $data = [];
            foreach ($grouped as $location => $items) {
                $count = $items->count();
                
                $data[] = [
                    'date' => $location, // Kita gunakan label lokasi sebagai 'date' agar frontend tetap jalan
                    'production' => round($items->avg('production') ?: 0),
                    'target' => round($items->avg('target') ?: 0),
                    'traffic_volume' => round($items->avg('traffic_volume') ?: 0),
                    'average_speed' => round($items->avg('average_speed') ?: 0),
                    'incidents' => round($items->sum('incidents')),
                    'congestion_index' => round($items->avg('congestion_index') ?: 0),
                    'signal_changes' => round($items->avg('signal_changes') ?: 0),
                    'green_wave_efficiency' => round($items->avg('green_wave_efficiency') ?: 0),
                    'is_manual' => true,
                ];
            }

            return $data;
        });
    }
}