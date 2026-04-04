<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Logbook extends Model
{
    use SoftDeletes;
    protected $table = 'tr_logbooks';
    protected $fillable = [
        'internship_id', 'date', 'activity', 'file_path', 'status', 'approved_by', 'approved_at', 'revision_note'
    ];

    public function internship()
    {
        return $this->belongsTo(Internship::class, 'internship_id');
    }
    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
