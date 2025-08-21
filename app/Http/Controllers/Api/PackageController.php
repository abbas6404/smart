<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Package;

class PackageController extends Controller
{
    /**
     * Get all packages
     */
    public function index()
    {
        $packages = Package::where('is_active', true)->get();
        
        return response()->json([
            'packages' => $packages
        ]);
    }

    /**
     * Get specific package
     */
    public function show(Package $package)
    {
        return response()->json([
            'package' => $package
        ]);
    }
}
