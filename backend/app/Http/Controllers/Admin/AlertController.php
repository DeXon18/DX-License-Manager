<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AlertSetting;
use App\Models\EmailLog;
use Illuminate\Http\Request;

class AlertController extends Controller
{
    public function index()
    {
        $settings = AlertSetting::firstOrCreate(['id' => 1]);
        $logs = EmailLog::orderBy('created_at', 'desc')->paginate(15);

        return view('admin.alerts.index', compact('settings', 'logs'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'threshold_alerta' => 'required|integer|min:1',
            'threshold_aviso' => 'required|integer|min:1',
            'threshold_recordatorio' => 'required|integer|min:1',
            'internal_copy_emails' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $settings = AlertSetting::firstOrCreate(['id' => 1]);
        $settings->update($request->all());

        return back()->with('success', 'Configuración de alertas actualizada correctamente.');
    }

    public function toggle()
    {
        $settings = AlertSetting::firstOrCreate(['id' => 1]);
        $settings->is_active = !$settings->is_active;
        $settings->save();

        return back()->with('success', 'Sistema de alertas ' . ($settings->is_active ? 'activado' : 'desactivado') . '.');
    }
}
