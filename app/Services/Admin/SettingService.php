<?php

namespace App\Services\Admin;

use App\Models\Notification;
use App\Repositories\Admin\SettingRepository;
use App\Services\BaseService;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
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
            $logoValue = $settings['school_logo'] ?? null;

            if (!empty($logoValue) && (str_starts_with($logoValue, 'data:image') || strlen($logoValue) > 500)) {
                try {
                    $privateKey = env('IMAGEKIT_PRIVATE_KEY', '');
                    if (!empty($privateKey)) {
                        $response = Http::withBasicAuth($privateKey, '')
                            ->asMultipart()
                            ->post('https://upload.imagekit.io/api/v1/files/upload', [
                                [
                                    'name' => 'file',
                                    'contents' => $logoValue
                                ],
                                [
                                    'name' => 'fileName',
                                    'contents' => 'school_logo_' . time() . '.png'
                                ],
                                [
                                    'name' => 'useUniqueFileName',
                                    'contents' => 'true'
                                ]
                            ]);

                        if ($response->successful()) {
                            $uploadedUrl = $response->json('url');
                            if ($uploadedUrl) {
                                $settings['school_logo'] = $uploadedUrl;
                                $settings['school_logo_url'] = $uploadedUrl;
                            }
                        } else {
                            Log::error('ImageKit upload error: ' . $response->body());
                            $this->saveLogoLocally($logoValue, $settings);
                        }
                    } else {
                        $this->saveLogoLocally($logoValue, $settings);
                    }
                } catch (\Exception $e) {
                    Log::error('ImageKit upload exception: ' . $e->getMessage());
                    $this->saveLogoLocally($logoValue, $settings);
                }
            } elseif (isset($settings['school_logo']) && str_starts_with($settings['school_logo'], 'http')) {
                $settings['school_logo_url'] = $settings['school_logo'];
            }

            foreach ($settings as $key => $value) {
                $this->settingRepository->updateByKey($key, $value ?? '');
            }
            return true;
        });
    }

    private function saveLogoLocally($logoValue, &$settings)
    {
        try {
            if (preg_match('/^data:image\/(\w+);base64,/', $logoValue, $type)) {
                $image = substr($logoValue, strpos($logoValue, ',') + 1);
                $type = strtolower($type[1]); // e.g. png, jpeg, webp
                if (in_array($type, ['jpg', 'jpeg', 'png', 'gif', 'svg', 'webp'])) {
                    $image = base64_decode($image);
                    if ($image !== false) {
                        $fileName = 'school_logo_' . time() . '.' . $type;
                        $path = 'settings/' . $fileName;

                        // Save to public disk
                        \Illuminate\Support\Facades\Storage::disk('public')->put($path, $image);

                        // Set the public URL
                        $uploadedUrl = asset('storage/' . $path);

                        $settings['school_logo'] = $uploadedUrl;
                        $settings['school_logo_url'] = $uploadedUrl;
                        return;
                    }
                }
            }
        } catch (\Exception $e) {
            Log::error('Local logo save exception: ' . $e->getMessage());
        }

        // Clear fields on failure to prevent DB truncation issues
        $settings['school_logo'] = '';
        $settings['school_logo_url'] = '';
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
