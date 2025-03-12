<?php

namespace App\Services;

use App\Models\StatusCheck;
use Exception;
use Illuminate\Database\Capsule\Manager as DB;

class DatabaseService
{
    public function __construct()
    {
        try {
            DB::connection()->getDatabaseName();
        } catch (Exception $e) {
            $capsule = new DB;
            $capsule->addConnection([
                'driver' => 'sqlite',
                'database' => __DIR__ . '/../../database/database.sqlite',
                'prefix' => '',
            ]);

            $capsule->setAsGlobal();
            $capsule->bootEloquent();
        }
    }

    public function saveStatus(array $statusData): void
    {
        StatusCheck::create([
            'current_status' => $statusData['current_status'],
            'maintenance_data' => json_encode($statusData['maintenance_data'], JSON_THROW_ON_ERROR)
        ]);
    }

    public function getLatestStatus(): ?array
    {
        $latestStatus = StatusCheck::orderBy('created_at', 'desc')->first();

        if (!$latestStatus) {
            return null;
        }

        return [
            'current_status' => $latestStatus->current_status,
            'maintenance_data' => $this->getDecodedMaintenanceData($latestStatus->maintenance_data),
            'checked_at' => $latestStatus->created_at
        ];
    }

    /**
     * Get complete status history
     */
    public function getHistory(int $limit = 100): array
    {
        $history = StatusCheck::where('current_status', '!=', 'Online')
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'current_status' => $item->current_status,
                    'maintenance_data' => $this->getDecodedMaintenanceData($item->maintenance_data),
                    'checked_at' => $item->created_at->format('Y-m-d H:i:s')
                ];
            });

        return $history->toArray();
    }

    /**
     * Get only downtime history (non-online statuses)
     */
    public function getDowntimeHistory(int $limit = 50): array
    {
        $history = StatusCheck::where('current_status', '!=', 'Online')
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->id,
                    'current_status' => $item->current_status,
                    'maintenance_data' => $this->getDecodedMaintenanceData($item->maintenance_data),
                    'checked_at' => $item->created_at->format('Y-m-d H:i:s')
                ];
            });

        return $history->toArray();
    }

    public function pruneOldRecords(int $keepCount = 500): void
    {
        $idsToKeep = StatusCheck::orderBy('created_at', 'desc')
            ->limit($keepCount)
            ->pluck('id');

        if ($idsToKeep->isNotEmpty()) {
            StatusCheck::whereNotIn('id', $idsToKeep)->delete();
        }
    }

    /**
     * Helper method to safely decode maintenance data
     * Handles both string JSON and arrays
     */
    private function getDecodedMaintenanceData($maintenanceData): array
    {
        if (is_string($maintenanceData)) {
            return json_decode($maintenanceData, true, 512, JSON_THROW_ON_ERROR) ?? [];
        }

        if (is_array($maintenanceData)) {
            return $maintenanceData;
        }

        return [];
    }
}