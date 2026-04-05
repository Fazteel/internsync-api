<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    protected $table = 'tr_permissions';
    protected $fillable = [
        'internship_id',
        'start_date',
        'end_date',
        'type',
        'reason',
        'attachment',
        'status'
    ];

    public function internship()
    {
        return $this->belongsTo(Internship::class, 'internship_id');
    }
}
