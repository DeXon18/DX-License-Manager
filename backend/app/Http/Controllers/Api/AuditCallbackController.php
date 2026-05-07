<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\AI\AuditService;
use Illuminate\Http\Request;

class AuditCallbackController extends Controller
{
    protected $auditService;

    public function __construct(AuditService $auditService)
    {
        $this->auditService = $auditService;
    }

    public function __invoke(Request $request)
    {
        // n8n envía el JSON resultado
        $data = $request->all();

        $success = $this->auditService->handleCallback($data);

        if (!$success) {
            return response()->json(['error' => 'Audit record not found or invalid data'], 404);
        }

        return response()->json(['message' => 'Audit updated successfully']);
    }
}
