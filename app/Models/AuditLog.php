<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class AuditLog extends Model
{
    protected $table = 'tr_audit_logs';
    public $timestamps = false;

    protected $fillable = ['user_id', 'table_name', 'action', 'description', 'created_at'];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public static function record($tableName, $action, $description)
    {
        $userId = Auth::id(); 

        self::create([
            'user_id' => $userId,
            'table_name' => $tableName,
            'action' => $action,
            'description' => $description,
            'created_at' => now(),
        ]);
    }
}