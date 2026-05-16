<?php

namespace App\Http\Controllers\Tools;

use App\Services\AI\CompositeParserService;
use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\CodCertificate;
use App\Services\Tools\CodService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CodController extends Controller
{
    protected CodService $codService;
    protected CompositeParserService $compositeParser;

    public function __construct(CodService $codService, CompositeParserService $compositeParser)
    {
        $this->codService = $codService;
        $this->compositeParser = $compositeParser;
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
        
        $pdf = $this->codService->generatePdf($request->all(), $request->Language);
        $fileName = 'COD_' . strtoupper($request->docType) . '_' . date('Ymd_His') . '.pdf';
        
        $directory = $this->codService->getStoragePath($client->name);
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

        return response()->download(
            Storage::disk('private')->path($certificate->file_path),
            basename($certificate->file_path)
        );
    }

    public function uploadSigned(Request $request, $uuid)
    {
        Log::info('COD Upload Attempt', ['uuid' => $uuid, 'has_file' => $request->hasFile('signed_file')]);

        $request->validate([
            'signed_file' => 'required|file|mimes:pdf,application/pdf|max:20480', // Aumentado a 20MB y validación dual
        ]);

        $certificate = CodCertificate::where('uuid', $uuid)->firstOrFail();
        $client = $certificate->client;

        $directory = $this->codService->getStoragePath($client->name);
        $fileName = 'COD_SIGNED_' . $certificate->type . '_' . now()->format('Ymd_His') . '.pdf';
        
        // Uso de putFileAs para mayor seguridad y manejo de streams
        $filePath = Storage::disk('private')->putFileAs($directory, $request->file('signed_file'), $fileName);

        if (!$filePath) {
            Log::error('COD Upload Failed to save file', ['directory' => $directory, 'fileName' => $fileName]);
            return back()->with('error', 'Error al guardar el archivo en el servidor.');
        }

        $certificate->update([
            'signed_file_path' => $filePath,
            'signed_at' => now(),
            'status' => 'SIGNED'
        ]);

        Log::info('COD Upload Success', ['uuid' => $uuid, 'path' => $filePath]);

        return back()->with('success', 'Certificado firmado subido correctamente.');
    }

    public function downloadSigned(Request $request)
    {
        $uuid = $request->query('uuid');
        $certificate = CodCertificate::where('uuid', $uuid)->firstOrFail();

        if (!$certificate->signed_file_path || !Storage::disk('private')->exists($certificate->signed_file_path)) {
            abort(404, 'Archivo firmado no encontrado.');
        }

        return response()->download(
            Storage::disk('private')->path($certificate->signed_file_path),
            basename($certificate->signed_file_path)
        );
    }

    public function destroy($uuid)
    {
        $certificate = CodCertificate::where('uuid', $uuid)->firstOrFail();

        // Delete files from private storage
        if (Storage::disk('private')->exists($certificate->file_path)) {
            Storage::disk('private')->delete($certificate->file_path);
        }

        if ($certificate->signed_file_path && Storage::disk('private')->exists($certificate->signed_file_path)) {
            Storage::disk('private')->delete($certificate->signed_file_path);
        }

        $certificate->delete();

        return back()->with('success', 'Certificado eliminado correctamente.');
    }

    /**
     * Procesa el texto de adaptadores con IA.
     */
    public function parseComposite(Request $request)
    {
        $request->validate([
            'text' => 'required|string|min:10|max:10000',
        ]);

        $result = $this->compositeParser->parse($request->text);

        if (isset($result['error'])) {
            return response()->json([
                'success' => false,
                'message' => $result['message']
            ], 500);
        }

        return response()->json([
            'success' => true,
            'data' => $result
        ]);
    }
}
