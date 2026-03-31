<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Letter extends Model
{
    use SoftDeletes;
    protected $table = 't_letters';
    protected $fillable = ['internship_id', 'letter_number', 'status', 'file_path', 'signed_at'];

    public function internship()
    {
        return $this->belongsTo(Internship::class, 'internship_id');
    }
}
