<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable, SoftDeletes, HasRoles;

    protected $table = 'm_users';

    protected $fillable = [
        'email',
        'password',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function student()
    {
        return $this->hasOne(Student::class, 'user_id');
    }

    public function teacher()
    {
        return $this->hasOne(Teacher::class, 'user_id');
    }

    public static function findByIdentifier(string $identifier): ?self
    {
        return static::where('email', $identifier)
            ->orWhere(function ($query) use ($identifier) {
                $query->whereHas('student', fn ($q) => $q->where('nis', $identifier))
                    ->orWhereHas('teacher', fn ($q) => $q->where('nip', $identifier));
            })->first();
    }
}
