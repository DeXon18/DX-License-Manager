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

    public function toggleDaemonStatus(LicenseInventoryDaemon $daemon)
    {
        $newStatus = $daemon->status === 'dropped' ? 'active' : 'dropped';
        $daemon->status = $newStatus;
        $daemon->save();
        
        if ($newStatus === 'dropped') {
            $daemon->products()->update(['status' => 'dropped']);
        }
        
        $msg = $newStatus === 'dropped' ? 'Servidor marcado como baja.' : 'Servidor reactivado.';
        return back()->with('success', $msg);
    }

    public function toggleProductStatus(LicenseInventoryProduct $product)
    {
        $newStatus = $product->status === 'dropped' ? 'active' : 'dropped';
        $product->status = $newStatus;
        $product->save();

        // If product is reactivated, ensure its daemon is also active
        if ($newStatus === 'active' && $product->daemon->status === 'dropped') {
            $product->daemon->status = 'active';
            $product->daemon->save();
        }

        $msg = $newStatus === 'dropped' ? 'Producto marcado como baja.' : 'Producto reactivado.';
        return back()->with('success', $msg);
    }
}
