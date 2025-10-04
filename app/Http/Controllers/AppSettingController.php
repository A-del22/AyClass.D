<?php

namespace App\Http\Controllers;

use App\Models\AppSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AppSettingController extends Controller
{
    /**
     * Display app settings page
     */
    public function index()
    {
        $setting = AppSetting::first();

        return view('dashboard.settings.index', compact('setting'));
    }

    /**
     * Show edit form
     */
    public function edit()
    {
        $setting = AppSetting::first();

        return view('dashboard.settings.edit', compact('setting'));
    }

    /**
     * Update app settings
     */
    public function update(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'url' => 'required|url|max:255',
            'nama_sekolah' => 'required|string',
            'alamat_sekolah' => 'required|string|max:255',
            'logo' => 'nullable|image|mimes:png,jpg,jpeg|max:2048',
        ]);

        $setting = AppSetting::first();

        if (!$setting) {
            $setting = new AppSetting();
        }

        $setting->name = $request->name;
        $setting->description = $request->description;
        $setting->url = $request->url;
        $setting->nama_sekolah = $request->nama_sekolah;
        $setting->alamat_sekolah = $request->alamat_sekolah;

        // Handle logo upload
        if ($request->hasFile('logo')) {
            // Delete old logo if exists (except default logo.png)
            if (file_exists(public_path('logo.png')) && $request->file('logo')->getClientOriginalName() !== 'logo.png') {
                @unlink(public_path('logo.png'));
            }

            // Save new logo
            $request->file('logo')->move(public_path(), 'logo.png');
        }

        $setting->save();

        return redirect()->route('settings.index')->with('success', 'Pengaturan aplikasi berhasil diperbarui!');
    }
}
