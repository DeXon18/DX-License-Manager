<?php

namespace App\Http\Controllers;

use App\Models\LicenseInventoryDaemon;
use App\Models\LicenseInventoryProduct;
use Illuminate\Http\Request;

class InventoryController extends Controller
{
    public function destroyDaemon(LicenseInventoryDaemon $daemon)
    {
        $daemon->delete();
        return back()->with('success', 'Bloque de licencia eliminado del inventario.');
    }

    public function destroyProduct(LicenseInventoryProduct $product)
    {
        $product->delete();
        return back()->with('success', 'Producto eliminado del inventario.');
    }
}
