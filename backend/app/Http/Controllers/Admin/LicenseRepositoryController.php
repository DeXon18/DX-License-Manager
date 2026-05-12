<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\Licensing\LicenseRepositoryService;
use App\Models\LicenseArchive;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class LicenseRepositoryController extends Controller
{
    protected $repositoryService;

    public function __construct(LicenseRepositoryService $repositoryService)
    {
        $this->repositoryService = $repositoryService;
    }

    /**
     * Muestra el historial de repositorios.
     */
    public function index()
    {
        $archives = $this->repositoryService->getHistory();
        return view('admin.licenses.repository', compact('archives'));
    }

    /**
     * Genera manualmente un repositorio para la semana pasada.
     */
    public function generate()
    {
        try {
            $archive = $this->repositoryService->generateWeeklyArchive();

            if ($archive) {
                return back()->with('success', "Repositorio semanal generado con éxito: {$archive->filename}");
            }

            return back()->with('info', 'No se encontraron licencias nuevas para archivar en el periodo solicitado.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error al generar el repositorio: ' . $e->getMessage());
        }
    }

    /**
     * Descarga un archivo del repositorio.
     */
    public function download(LicenseArchive $archive)
    {
        if (!Storage::disk('local')->exists($archive->storage_path)) {
            return abort(404, 'Archivo no encontrado en el almacenamiento.');
        }

        return Storage::disk('local')->download(
            $archive->storage_path,
            $archive->filename
        );
    }
}
