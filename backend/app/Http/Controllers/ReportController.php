<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Services\AnalyticsService;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportController extends Controller
{
    protected $analyticsService;

    public function __construct(AnalyticsService $analyticsService)
    {
        $this->analyticsService = $analyticsService;
    }

    public function index()
    {
        // For the search functionality, we get clients that have either contracts or inventory products
        $clients = Client::whereHas('contracts')
            ->orWhereHas('inventoryDaemons', function($q) {
                $q->whereHas('products', function($q2) {
                    $q2->where('status', 'active');
                });
            })
            ->orderBy('name')
            ->get();

        $topProducts = $this->analyticsService->getTopProducts(10);
        $expirationsTimeline = $this->analyticsService->getExpirationsTimeline();
        $topClients = $this->analyticsService->getTopClients(5);

        return view('reports.index', compact('clients', 'topProducts', 'expirationsTimeline', 'topClients'));
    }

    public function downloadClientReport(Client $client)
    {
        $reportData = $this->analyticsService->getClientReportData($client);

        // dompdf configuration and generation
        $pdf = Pdf::loadView('reports.client-pdf', $reportData);
        
        // Optional: set paper size to A4
        $pdf->setPaper('a4', 'portrait');

        $filename = 'Reporte_Licencias_' . str_replace(' ', '_', $client->name) . '_' . date('Ymd') . '.pdf';

        return $pdf->download($filename);
    }
}
