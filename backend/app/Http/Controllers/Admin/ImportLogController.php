<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ImportLog;
use Illuminate\Http\Request;

class ImportLogController extends Controller
{
    /**
     * Display a listing of the import logs.
     */
    public function index()
    {
        $logs = ImportLog::orderBy('created_at', 'desc')->paginate(10);
        return view('admin.import.logs.index', compact('logs'));
    }

    /**
     * Display the specified import log.
     */
    public function show(ImportLog $log)
    {
        return view('admin.import.logs.show', compact('log'));
    }

    /**
     * Remove the specified import log.
     */
    public function destroy(ImportLog $log)
    {
        $log->delete();
        return redirect()->route('admin.import.logs.index')->with('success', 'Log eliminado correctamente.');
    }
}
