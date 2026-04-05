<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use App\Services\Admin\AdminDashboardService;
use App\Services\Hubin\HubinDashboardService;
use App\Services\Koordinator\KoordinatorDashboardService;

class UpdateDashboardCache extends Command
{
    protected $signature = 'dashboard:refresh-cache';
    protected $description = 'Refresh semua statistik dashboard ke dalam cache';

    public function handle()
    {
        $this->info('Mulai refresh cache dashboard...');
        $timestamp = now()->translatedFormat('H:i');

        $adminService = app(AdminDashboardService::class);
        Cache::put('admin_stats', array_merge($adminService->getDashboardStats(), ['last_updated' => $timestamp]));

        $hubinService = app(HubinDashboardService::class);
        Cache::put('hubin_stats', array_merge($hubinService->getStats(), ['last_updated' => $timestamp]));

        $koordinatorService = app(KoordinatorDashboardService::class);
        Cache::put('koordinator_stats', array_merge($koordinatorService->getDashboardStats(), ['last_updated' => $timestamp]));

        $this->info("Cache berhasil diupdate pada {$timestamp}");
    }
}
