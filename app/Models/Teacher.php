<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Teacher extends Model
{
    use SoftDeletes;
    protected $table = 'm_teachers';
    protected $fillable = ['user_id', 'nip', 'name', 'phone', 'address', 'signature_path'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
