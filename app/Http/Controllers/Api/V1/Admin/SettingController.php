<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\AuditLog;

class SettingController extends Controller
{
    public function index()
    {
        $settings = Setting::pluck('setting_value', 'setting_key')->toArray();
        return response()->json($settings);
    }

    public function update(Request $request)
    {
        $data = $request->all();

        DB::transaction(function () use ($data) {
            foreach ($data as $key => $value) {
                Setting::updateOrCreate(
                    ['setting_key' => $key],
                    ['setting_value' => $value]
                );
            }
        });

        AuditLog::record(
            'm_settings', 
            'update', 
            "Memperbarui pengaturan sistem: " . implode(', ', array_map(function($k, $v) { return "$k=$v"; }, array_keys($data), $data))
        );
        return response()->json(['message' => 'Pengaturan sistem berhasil disimpan.']);
    }
}