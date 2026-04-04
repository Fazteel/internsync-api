<?php

namespace App\Repositories\Admin;

use App\Models\Setting;
use App\Repositories\BaseRepository;

class SettingRepository extends BaseRepository
{
    public function __construct(Setting $model)
    {
        parent::__construct($model);
    }

    public function getAllAsKeyValue()
    {
        return $this->model->pluck('setting_value', 'setting_key');
    }

    public function updateByKey($key, $value)
    {
        return $this->model->updateOrCreate(
            ['setting_key' => $key],
            ['setting_value' => $value]
        );
    }

    public function getValByKey($key, $default = null)
    {
        return $this->model->where('setting_key', $key)->value('setting_value') ?? $default;
    }
}
