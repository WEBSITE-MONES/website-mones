<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SettingAplikasi;

class SettingAplikasiController extends Controller
{
    public function index()
    {
        $setting = SettingAplikasi::first();
        return view('Dashboard.setting_aplikasi', compact('setting'));
    }

    public function edit($id)
    {
        $setting = SettingAplikasi::findOrFail($id);
        return view('Dashboard.edit_setting_aplikasi', compact('setting'));
    }

    public function update(Request $request, $id)
    {
        $setting = SettingAplikasi::findOrFail($id);

        $data = $request->validate([
            'nama_aplikasi' => 'required|string|max:100',
            'nama_perusahaan' => 'required|string|max:100',
            'ucapan' => 'nullable|string',
            'logo' => 'nullable|image|mimes:png,jpg,jpeg|max:2048'
        ]);

        if($request->hasFile('logo')) {
            $file = $request->file('logo');
            $filename = time().'_'.$file->getClientOriginalName();
            $file->move(public_path('img/mnp'), $filename);
            $data['logo'] = $filename;
        }

        $setting->update($data);

        return redirect()->route('setting_aplikasi.index')->with('success', 'Setting aplikasi berhasil diperbarui.');
    }
}