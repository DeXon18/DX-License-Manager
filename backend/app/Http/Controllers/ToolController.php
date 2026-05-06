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
        $features = FeatureFlag::all()->groupBy('vendor');

        return view('tools.index', [
            'features' => $features
        ]);
    }
}
