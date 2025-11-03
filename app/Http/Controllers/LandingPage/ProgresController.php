<?php

namespace App\Http\Controllers\LandingPage;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ProgresController extends Controller
{
    public function index()
    {
        return view('LandingPage.index');
    }
    public function pelaporan()
    {
        return view('LandingPage.pelaporan');
    }
    public function pelaporanform()
    {
        return view('LandingPage.pelaporan-form');
    }
    public function pelaporanformedit()
    {
        return view('LandingPage.pelaporan-form_edit');
    }
    public function dokumentasi()
    {
        return view('LandingPage.dokumentasi');
    }
}