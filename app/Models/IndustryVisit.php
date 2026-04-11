<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IndustryVisit extends Model
{
    use HasFactory;
    protected $table = 'tr_visit_requests';

    protected $fillable = [
        'pembimbing_id',
        'coordinator_id',
        'industry_id',
        'planned_date',
        'purpose',
        'file_path',
        'status',
        'feedback'
    ];

    public function pembimbing()
    {
        return $this->belongsTo(User::class, 'pembimbing_id');
    }

    public function industry()
    {
        return $this->belongsTo(Industry::class, 'industry_id');
    }

    public function coordinator()
    {
        return $this->belongsTo(User::class, 'coordinator_id');
    }
}
