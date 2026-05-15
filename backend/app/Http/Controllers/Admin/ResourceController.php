<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ResourceLink;
use Illuminate\Http\Request;

class ResourceController extends Controller
{
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'vendor' => 'required|string|in:siemens,moldex3d',
            'category' => 'required|string|in:official,internal,utility,support',
            'label' => 'required|string|max:255',
            'url' => 'required|url',
            'description' => 'nullable|string',
            'icon' => 'nullable|string|max:50',
            'order' => 'nullable|integer',
        ]);

        $resource = ResourceLink::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Recurso creado correctamente',
            'resource' => $resource
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ResourceLink $resource)
    {
        $validated = $request->validate([
            'category' => 'required|string|in:official,internal,utility,support',
            'label' => 'required|string|max:255',
            'url' => 'required|url',
            'description' => 'nullable|string',
            'icon' => 'nullable|string|max:50',
            'order' => 'nullable|integer',
        ]);

        $resource->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Recurso actualizado correctamente',
            'resource' => $resource
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ResourceLink $resource)
    {
        $resource->delete();

        return response()->json([
            'success' => true,
            'message' => 'Recurso eliminado correctamente'
        ]);
    }
}
