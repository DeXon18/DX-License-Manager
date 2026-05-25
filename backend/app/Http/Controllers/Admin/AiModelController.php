<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AiModel;
use App\Models\AiRoute;
use Illuminate\Http\Request;

class AiModelController extends Controller
{
    public function index()
    {
        $models = AiModel::orderBy('name')->get();
        $routes = AiRoute::with(['primaryModel', 'fallbackModel'])->get();

        return view('admin.system.ai-routing.index', compact('models', 'routes'));
    }

    public function storeModel(Request $request)
    {
        $validated = $request->validate([
            'openrouter_id' => 'required|string|unique:ai_models,openrouter_id',
            'name' => 'required|string|max:255',
            'is_free' => 'boolean',
            'price_prompt' => 'numeric|min:0',
            'price_completion' => 'numeric|min:0',
        ]);

        $validated['is_active'] = true;

        AiModel::create($validated);

        return back()->with('success', 'Modelo IA añadido correctamente.');
    }

    public function toggleModel(AiModel $aiModel)
    {
        $aiModel->is_active = !$aiModel->is_active;
        $aiModel->save();

        return back()->with('success', 'Estado del modelo actualizado.');
    }

    public function updateRoute(Request $request, $task_name)
    {
        $validated = $request->validate([
            'primary_model_id' => 'required|exists:ai_models,id',
            'fallback_model_id' => 'nullable|exists:ai_models,id',
        ]);

        $route = AiRoute::findOrFail($task_name);
        $route->update($validated);

        return back()->with('success', 'Ruta IA actualizada correctamente.');
    }
}
