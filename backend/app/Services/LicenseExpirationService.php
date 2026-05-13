<?php

namespace App\Services;

use App\Models\Client;
use App\Models\LicenseInventoryProduct;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class LicenseExpirationService
{
    /**
     * Get clients that have at least one contact subscribed to alerts.
     */
    public function getClientsToNotify(): Collection
    {
        return Client::whereHas('contacts', function ($query) {
            $query->where('receives_alerts', true);
        })->get();
    }

    /**
     * Get expiring licenses for a specific client, grouped by threshold.
     */
    public function getExpiringLicensesForClient(Client $client): array
    {
        $today = Carbon::today();
        $limit = Carbon::today()->addDays(30);

        // Get all products for this client that expire in the next 30 days
        $products = LicenseInventoryProduct::whereHas('daemon', function ($query) use ($client) {
            $query->where('client_id', $client->id);
        })
        ->where('status', 'active')
        ->whereBetween('expiration_date', [$today, $limit])
        ->orderBy('expiration_date')
        ->with('daemon')
        ->get();

        $results = [
            'alerta' => new Collection(),       // 0-7 days
            'aviso' => new Collection(),        // 7-15 days
            'recordatorio' => new Collection(), // 15-30 days
        ];

        foreach ($products as $product) {
            $daysLeft = $today->diffInDays($product->expiration_date);

            if ($daysLeft <= 7) {
                $results['alerta']->push($product);
            } elseif ($daysLeft <= 15) {
                $results['aviso']->push($product);
            } else {
                $results['recordatorio']->push($product);
            }
        }

        return $results;
    }

    /**
     * Get all data for the weekly report.
     */
    public function getWeeklyReportData(): Collection
    {
        $clients = $this->getClientsToNotify();
        $reportData = collect();

        foreach ($clients as $client) {
            $expiring = $this->getExpiringLicensesForClient($client);
            
            // Only include if there are actual expiring licenses
            if ($expiring['alerta']->isNotEmpty() || $expiring['aviso']->isNotEmpty() || $expiring['recordatorio']->isNotEmpty()) {
                $reportData->push([
                    'client' => $client,
                    'expiring' => $expiring,
                    'recipients' => $client->contacts()->where('receives_alerts', true)->get(),
                ]);
            }
        }

        return $reportData;
    }
}
