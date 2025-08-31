<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Wilayah;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        return view('Dashboard.index');
    }
    
    public function kota($id)
    {
        $wilayah = Wilayah::with('pekerjaans')->findOrFail($id);
        return view('Dashboard.kota', compact('wilayah'));
    }

    
}