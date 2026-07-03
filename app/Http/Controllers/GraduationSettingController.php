<?php

namespace App\Http\Controllers;

use App\Models\GraduationSetting;
use Illuminate\Http\Request;

class GraduationSettingController extends Controller
{

    public function index()
    {
        $settings = GraduationSetting::all();
        return view('admin.graduation-settings.index', compact('settings'));
    }

    public function create()
    {
        return view('admin.graduation-settings.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'waktu_buka' => 'required|date_format:Y-m-d\TH:i',
            'waktu_tutup' => 'required|date_format:Y-m-d\TH:i|after:waktu_buka',
            'status' => 'required|in:active,inactive',
            'keterangan' => 'nullable|string|max:500',
        ]);

        $validated['waktu_buka'] = date('Y-m-d H:i:s', strtotime($validated['waktu_buka']));
        $validated['waktu_tutup'] = date('Y-m-d H:i:s', strtotime($validated['waktu_tutup']));

        GraduationSetting::create($validated);

        return redirect()->route('graduation-settings.index')
            ->with('success', 'Pengaturan kelulusan berhasil ditambahkan');
    }

    public function edit(GraduationSetting $graduationSetting)
    {
        return view('admin.graduation-settings.edit', compact('graduationSetting'));
    }

    public function update(Request $request, GraduationSetting $graduationSetting)
    {
        $validated = $request->validate([
            'waktu_buka' => 'required|date_format:Y-m-d\TH:i',
            'waktu_tutup' => 'required|date_format:Y-m-d\TH:i|after:waktu_buka',
            'status' => 'required|in:active,inactive',
            'keterangan' => 'nullable|string|max:500',
        ]);

        $validated['waktu_buka'] = date('Y-m-d H:i:s', strtotime($validated['waktu_buka']));
        $validated['waktu_tutup'] = date('Y-m-d H:i:s', strtotime($validated['waktu_tutup']));

        $graduationSetting->update($validated);

        return redirect()->route('graduation-settings.index')
            ->with('success', 'Pengaturan kelulusan berhasil diperbarui');
    }

    public function destroy(GraduationSetting $graduationSetting)
    {
        if ($graduationSetting->status === 'active' && GraduationSetting::where('status', 'active')->count() === 1) {
            return redirect()->route('graduation-settings.index')
                ->with('error', 'Tidak dapat menghapus pengaturan aktif yang terakhir');
        }

        $graduationSetting->delete();

        return redirect()->route('graduation-settings.index')
            ->with('success', 'Pengaturan kelulusan berhasil dihapus');
    }

    public function toggleStatus(GraduationSetting $graduationSetting)
    {
        $newStatus = $graduationSetting->status === 'active' ? 'inactive' : 'active';
        $graduationSetting->update(['status' => $newStatus]);

        return redirect()->route('graduation-settings.index')
            ->with('success', 'Status pengaturan berhasil diubah');
    }
}
