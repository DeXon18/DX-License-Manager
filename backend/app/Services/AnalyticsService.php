<?php

namespace App\Services;

use App\Models\Client;
use App\Models\Contract;
use App\Models\LicenseInventoryProduct;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AnalyticsService
{
    /**
     * Get the top products based on the quantity of seats in the actual .lic files.
     * We filter out expired/obsolete ones to show what's currently active.
     */
    public function getTopProducts(int $limit = 10)
    {
        return LicenseInventoryProduct::where('status', 'active')
            ->select('product_code', DB::raw('SUM(quantity) as total_quantity'))
            ->groupBy('product_code')
            ->orderByDesc('total_quantity')
            ->limit($limit)
            ->get();
    }

    /**
     * Get the top clients by number of active seats.
     */
    public function getTopClients(int $limit = 5)
    {
        return DB::table('clients')
            ->join('license_inventory_daemons', 'clients.id', '=', 'license_inventory_daemons.client_id')
            ->join('license_inventory_products', 'license_inventory_daemons.id', '=', 'license_inventory_products.daemon_id')
            ->where('license_inventory_products.status', 'active')
            ->select('clients.id', 'clients.name', DB::raw('SUM(license_inventory_products.quantity) as total_seats'))
            ->groupBy('clients.id', 'clients.name')
            ->orderByDesc('total_seats')
            ->limit($limit)
            ->get();
    }

    /**
     * Get expirations grouped by month for the next 12 months.
     * Based on .lic expiration dates.
     */
    public function getExpirationsTimeline()
    {
        $startDate = Carbon::now()->startOfMonth();
        $endDate = Carbon::now()->addMonths(11)->endOfMonth();

        // Get expirations within the next 12 months
        $expirations = LicenseInventoryProduct::whereBetween('expiration_date', [$startDate, $endDate])
            ->select(
                DB::raw('DATE_FORMAT(expiration_date, "%Y-%m") as month'),
                DB::raw('SUM(quantity) as total_expiring')
            )
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->keyBy('month');

        // Fill in missing months with 0
        $timeline = [];
        $currentDate = clone $startDate;

        for ($i = 0; $i < 12; $i++) {
            $monthKey = $currentDate->format('Y-m');
            $timeline[] = [
                'month' => $monthKey,
                'label' => $currentDate->translatedFormat('M Y'), // e.g., "May 2026"
                'count' => $expirations->has($monthKey) ? $expirations->get($monthKey)->total_expiring : 0
            ];
            $currentDate->addMonth();
        }

        return $timeline;
    }

    /**
     * Get all relevant report data for a specific client.
     */
    public function getClientReportData(Client $client)
    {
        $client->load([
            'contracts',
            'inventoryDaemons.products'
        ]);

        $activeProducts = $client->inventoryDaemons->flatMap->products->where('status', 'active');
        $expiredProducts = $client->inventoryDaemons->flatMap->products->where('status', 'expired');

        $totalActiveSeats = $activeProducts->sum('quantity');
        
        // Products grouped by code
        $groupedProducts = $activeProducts->groupBy('product_code')->map(function ($group) {
            return [
                'quantity' => $group->sum('quantity'),
                'expirations' => $group->pluck('expiration_date')->unique()->filter()->values()
            ];
        })->sortByDesc('quantity');

        return [
            'client' => $client,
            'total_active_seats' => $totalActiveSeats,
            'total_expired_seats' => $expiredProducts->sum('quantity'),
            'total_daemons' => $client->inventoryDaemons->count(),
            'grouped_products' => $groupedProducts,
            'contracts' => $client->contracts
        ];
    }
}
