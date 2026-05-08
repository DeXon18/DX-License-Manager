<?php

namespace App\Http\Controllers\Tools;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\CodCertificate;
use App\Services\Tools\CodService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CodController extends Controller
{
    protected CodService $codService;

    public function __construct(CodService $codService)
    {
        $this->codService = $codService;
    }

    public function index(Request $request)
    {
        $clients = Client::with('inventoryDaemons')->orderBy('name')->get();
        $selectedClient = null;

        if ($request->has('client_id')) {
            $selectedClient = Client::find($request->client_id);
        }

        return view('tools.cod', compact('clients', 'selectedClient'));
    }

    public function preview(Request $request)
    {
        $request->validate([
            'docType' => 'required|string',
            'Language' => 'required|string',
            'Data_SoldTo' => 'required|string',
            'Data_Solicitante' => 'required|string',
            'Data_Empresa' => 'required|string',
            'os' => 'required|string',
        ]);

        $pdf = $this->codService->generatePdf($request->all(), $request->Language);

        return $pdf->stream('preview-cod.pdf');
    }

    public function store(Request $request)
    {
        $request->validate([
            'client_id' => 'required|exists:clients,id',
            'docType' => 'required|string',
            'Language' => 'required|string',
            'Data_SoldTo' => 'required|string',
            'Data_Solicitante' => 'required|string',
            'Data_Empresa' => 'required|string',
            'os' => 'required|string',
        ]);

        $client = Client::findOrFail($request->client_id);
        $clientSlug = Str::slug($client->name);
        
        $pdf = $this->codService->generatePdf($request->all(), $request->Language);
        
        $fileName = 'COD_' . strtoupper($request->docType) . '_' . date('Ymd_His') . '.pdf';
        $directory = $this->codService->getStoragePath($clientSlug);
        $filePath = $directory . '/' . $fileName;

        Storage::disk('private')->put($filePath, $pdf->output());

        $certificate = CodCertificate::create([
            'client_id' => $client->id,
            'sold_to' => $request->Data_SoldTo,
            'type' => strtoupper($request->docType),
            'os' => strtoupper($request->os),
            'language' => strtoupper($request->Language),
            'status' => 'PENDING',
            'file_path' => $filePath,
            'form_data' => $request->all(),
            'created_by' => Auth::id(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Certificado generado y guardado correctamente.',
            'download_url' => route('tools.cod.download', ['uuid' => $certificate->uuid])
        ]);
    }

    public function download(Request $request)
    {
        $uuid = $request->query('uuid');
        
        $certificate = CodCertificate::where('uuid', $uuid)->firstOrFail();
        
        if (!Storage::disk('private')->exists($certificate->file_path)) {
            abort(404, 'Archivo no encontrado en el almacenamiento.');
        }

        // Security check: ensure user can access this file (RBAC)
        // For now, technician/admin can access, which is handled by auth.jwt middleware

        return Storage::disk('private')->download($certificate->file_path);
    }
}
