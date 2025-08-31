<?php

namespace App\Http\Controllers\Autentikasi;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Profile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AccountSettingController extends Controller
{
    /**
     * Tampilkan halaman profil user
     */
    public function index()
    {
        $user = Auth::user();
        $profile = $user->profile; // otomatis ambil relasi

        return view('Dashboard.Profile.index', compact('user', 'profile'));
    }

    /**
     * Tampilkan form edit profil
     */
    public function edit()
    {
        $user = Auth::user();
        $profile = $user->profile;

        return view('Dashboard.Profile.edit_profile', compact('user', 'profile'));
    }

    /**
     * Simpan update profil
     */
    public function update(Request $request)
    {
        $request->validate([
            'jabatan' => 'nullable|string',
            'tanggal_lahir' => 'nullable|date',
            'agama' => 'nullable|string',
            'jenis_kelamin' => 'nullable|in:L,P',
            'nomor_telepon' => 'nullable|string|max:20',
            'alamat' => 'nullable|string',
        ]);

        $user = Auth::user();

        // Update atau buat profile baru kalau belum ada
        Profile::updateOrCreate(
            ['user_id' => $user->id],
            $request->only([
                'jabatan',
                'tanggal_lahir',
                'agama',
                'jenis_kelamin',
                'nomor_telepon',
                'alamat'
            ])
        );

        return redirect()->route('account.index')->with('success', 'Profil berhasil diperbarui.');
    }

    /**
     * Tampilkan halaman account setting (ubah password)
     */
    public function accountSetting()
    {
        $user = Auth::user();
        return view('Dashboard.Profile.account_setting', compact('user'));
    }

    /**
     * Simpan perubahan password / account setting
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateAccountSetting(Request $request)
    {
        /** @var User $user */
        $user = Auth::user();

        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|confirmed|min:6',
        ]);

        // Cek password lama
        if (!Hash::check($request->current_password, $user->password)) {
            return redirect()->back()->withErrors(['current_password' => 'Password lama salah']);
        }

        // Update password
        $user->password = Hash::make($request->new_password);
        $user->save();

        return redirect()->route('account.setting')->with('success', 'Password berhasil diperbarui.');
    }
}