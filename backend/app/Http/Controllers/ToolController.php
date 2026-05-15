<?php

namespace App\Http\Controllers;

use App\Models\FeatureFlag;
use Illuminate\Http\Request;

class ToolController extends Controller
{
    /**
     * Display the tools hub.
     */
    public function index()
    {
        $priority = [
            'siemens_nx_suite' => 1,
            'siemens_star_ccm' => 2,
            'siemens_heeds'    => 3,
            'siemens_cod'      => 4,
            'siemens_recursos' => 5,
        ];

        $features = FeatureFlag::all()
            ->sortBy(fn($f) => $priority[$f->key] ?? 99)
            ->groupBy('vendor');

        return view('tools.index', [
            'features' => $features
        ]);
    }
}
