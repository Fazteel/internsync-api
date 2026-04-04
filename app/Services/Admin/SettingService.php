<?php

namespace App\Services\Admin;

use App\Repositories\Admin\SettingRepository;
use App\Services\BaseService;
use Illuminate\Support\Facades\DB;

class SettingService extends BaseService
{
    protected $settingRepository;

    public function __construct(SettingRepository $settingRepository)
    {
        parent::__construct($settingRepository);
        $this->settingRepository = $settingRepository;
    }

    public function getAllSettings()
    {
        return $this->settingRepository->getAllAsKeyValue();
    }

    public function updateSettings(array $settings)
    {
        return DB::transaction(function () use ($settings) {
            foreach ($settings as $key => $value) {
                $this->settingRepository->updateByKey($key, $value ?? '');
            }
            return true;
        });
    }
}