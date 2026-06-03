<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DatabaseMonitorController extends Controller
{
    /**
     * Display the database monitor dashboard.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Obtener tamaño total de la BD y lista de tablas
        $databaseName = env('DB_DATABASE', 'dxportal_beta'); // Fallback a beta por defecto
        
        // O usar select database()
        $dbResult = DB::select('SELECT database() AS db_name');
        $dbName = $dbResult[0]->db_name ?? $databaseName;

        // Query para información del schema
        $tables = DB::select("
            SELECT 
                TABLE_NAME as name, 
                TABLE_ROWS as `rows`, 
                ROUND((DATA_LENGTH + INDEX_LENGTH) / 1024 / 1024, 2) as size_mb,
                CREATE_TIME as created_at
            FROM information_schema.TABLES 
            WHERE TABLE_SCHEMA = ?
            ORDER BY size_mb DESC
        ", [$dbName]);

        $totalSizeMb = array_sum(array_column($tables, 'size_mb'));

        // Obtener estado de conexiones y uptime
        $statusVars = DB::select("SHOW GLOBAL STATUS WHERE Variable_name IN ('Threads_connected', 'Uptime', 'Questions')");
        $status = [];
        foreach ($statusVars as $var) {
            $status[$var->Variable_name] = $var->Value;
        }

        // Obtener versión
        $versionResult = DB::select('SELECT VERSION() as version');
        $version = $versionResult[0]->version ?? 'Desconocida';

        return view('admin.database.index', compact('tables', 'totalSizeMb', 'status', 'version', 'dbName'));
    }
}
