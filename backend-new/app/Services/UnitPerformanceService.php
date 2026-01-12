<?php

namespace App\Services;

use App\Repositories\UnitPerformanceRepository;
use App\Repositories\BuildingRepository;
use App\Models\Building;
use Illuminate\Support\Facades\Cache;

class UnitPerformanceService extends BaseService
{
    protected $unitPerformanceRepository;
    protected $buildingRepository;

    public function __construct(UnitPerformanceRepository $unitPerformanceRepository, BuildingRepository $buildingRepository)
    {
        parent::__construct($unitPerformanceRepository);
        $this->unitPerformanceRepository = $unitPerformanceRepository;
        $this->buildingRepository = $buildingRepository;
    }

    /**
     * Get unit performance data dynamically generated from Building data
     *
     * @return array
     */
    public function getUnitPerformance()
    {
        // Menggunakan kunci cache baru untuk memastikan data langsung terupdate
        return Cache::remember('unit_performance_fresh', 30, function () {
            // Ambil data CCTV beserta relasinya
            $cctvs = \App\Models\Cctv::with(['ATCSHistory', 'building', 'room'])->get();
            
            if ($cctvs->isEmpty()) {
                return [];
            }
            
            // Group by Building and Room
            $groupedData = $cctvs->groupBy(function($cctv) {
                $buildingName = $cctv->building->name ?? 'Unknown Building';
                $roomName = $cctv->room->name ?? 'Unknown Room';
                return "{$buildingName} - {$roomName}";
            });

            return $groupedData->map(function ($items, $label) {
                $count = $items->count();
                $totalEfficiency = 0;
                $totalDensity = 0;
                $totalOptimization = 0;

                foreach ($items as $cctv) {
                    // Ambil data terbaru dari histori jika ada
                    $latestHistory = $cctv->ATCSHistory()->orderBy('date', 'desc')->first();
                    
                    // Metrik per CCTV
                    $cctvEfficiency = $latestHistory->production ?? (75 + (min(25, $cctv->ATCSHistory->count() * 5)));
                    $cctvDensity = $latestHistory->traffic_volume ?? 0;
                    $cctvOptimization = $latestHistory->green_wave_efficiency ?? (65 + (min(35, $cctv->ATCSHistory->count() * 7)));
                    
                    // Simulasi density jika belum diinput
                    if ($cctvDensity == 0) {
                        mt_srand(crc32($cctv->name));
                        $cctvDensity = mt_rand(40, 90);
                        mt_srand();
                    }

                    $totalEfficiency += $cctvEfficiency;
                    $totalDensity += $cctvDensity;
                    $totalOptimization += $cctvOptimization;
                }

                return [
                    'unit' => $label,
                    'efficiency' => round($totalEfficiency / $count),
                    'traffic_density' => round($totalDensity / $count),
                    'signal_optimization' => round($totalOptimization / $count),
                    'capacity' => 100,
                ];
            })->take(1)->values()->toArray();
        });
    }
}