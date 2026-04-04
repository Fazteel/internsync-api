<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Services\Admin\SettingService;
use Illuminate\Http\Request;
use App\Models\AuditLog;

class SettingController extends Controller
{
    protected $settingService;

    public function __construct(SettingService $settingService)
    {
        $this->settingService = $settingService;
    }

    public function index()
    {
        return response()->json($this->settingService->getAllSettings());
    }

    public function update(Request $request)
    {
        $data = $request->all();

        $this->settingService->updateSettings($data);

        AuditLog::record('m_settings', 'update', "Update setting: " . json_encode($data));

        return response()->json(['message' => 'Pengaturan berhasil disimpan.']);
    }
}
