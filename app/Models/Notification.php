<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Setting;

class Notification extends Model
{
    protected $table = 'tr_notifications';

    protected $fillable = ['user_id', 'title', 'message', 'type', 'is_read'];

    public static function send($userId, $title, $message, $type = 'info')
    {
        $isEnabled = Setting::where('setting_key', 'enable_notifications')->value('setting_value') ?? 'true';

        if ($isEnabled === 'false') {
            return false;
        }

        self::create([
            'user_id' => $userId,
            'title'   => $title,
            'message' => $message,
            'type'    => $type,
            'is_read' => false,
        ]);

        return true;
    }
}
