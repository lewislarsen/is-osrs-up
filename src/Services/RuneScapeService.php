<?php

namespace App\Services;

use DateTime;
use DateInterval;

class RuneScapeService
{
    private CacheService $cacheService;
    private DatabaseService $databaseService;
    private array $settings;
    private const STATUS_CACHE_KEY = 'rs_status';
    private const CHECK_INTERVAL = 180; // Default: 3 minutes

    public function __construct(
        CacheService $cacheService,
        DatabaseService $databaseService,
        array $settings
    ) {
        $this->cacheService = $cacheService;
        $this->databaseService = $databaseService;
        $this->settings = $settings;
    }

    public function getLatestStatus(): array
    {
        // Try to get status from cache first
        $cachedStatus = $this->cacheService->get(self::STATUS_CACHE_KEY);

        // If not in cache, or time to refresh, fetch a new status
        if (empty($cachedStatus) || $this->isTimeToRefresh($cachedStatus['last_checked'])) {
            $status = $this->fetchStatus();

            // Save to cache with timestamp
            $status['last_checked'] = date('Y-m-d H:i:s');
            $nextCheck = new DateTime();
            $nextCheck->add(new DateInterval('PT' . $this->getCheckInterval() . 'S'));
            $status['next_check'] = $nextCheck->format('Y-m-d H:i:s');

            $this->cacheService->set(self::STATUS_CACHE_KEY, $status, $this->getCheckInterval() + 30);

            // Also save to database for history
            $this->databaseService->saveStatus([
                'current_status' => $status['current_status'],
                'maintenance_data' => $status['maintenance_data'] ?? []
            ]);

            // Prune old records periodically
            $this->databaseService->pruneOldRecords(500);

            return $status;
        }

        return $cachedStatus;
    }

    public function getStatusHistory(): array
    {
        return $this->databaseService->getHistory(10);
    }

    public function getDowntimeHistory(): array
    {
        return $this->databaseService->getDowntimeHistory(50);
    }

    private function fetchStatus(): array
    {
        try {
            // Implement real status fetching logic here
            // For now, return a mock status
            return [
                'current_status' => 'Online',
                'maintenance_data' => []
            ];
        } catch (\Exception $e) {
            return [
                'current_status' => 'Error',
                'error' => 'Failed to fetch status: ' . $e->getMessage(),
                'error_details' => [
                    'message' => $e->getMessage(),
                    'code' => $e->getCode(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine()
                ],
                'maintenance_data' => []
            ];
        }
    }

    private function isTimeToRefresh(string $lastChecked): bool
    {
        $lastCheckedTime = strtotime($lastChecked);
        $currentTime = time();

        return ($currentTime - $lastCheckedTime) >= $this->getCheckInterval();
    }

    private function getCheckInterval(): int
    {
        return $this->settings['check_interval'] ?? self::CHECK_INTERVAL;
    }
}