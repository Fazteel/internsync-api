<?php

namespace App\Services\Admin;

use App\Models\Notification;
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

    public function sendNotifications($userId, $title, $message, $type = 'info')
    {
        $isEnabled = $this->settingRepository->getValByKey('enable_notifications', 'true');

        if ($isEnabled === 'true') {
            return Notification::create([
                'user_id' => $userId,
                'title' => $title,
                'message' => $message,
                'type' => $type,
                'is_read' => false,
            ]);
        }
        return false;
    }
}
