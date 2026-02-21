<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Petugas extends Authenticatable
{
    use HasFactory;

    protected $table = 'petugas';

    protected $fillable = [
        'role_id',
        'nama',
        'email',
        'password',
        'alamat',
        'no_telepon',
        'photo',
    ];

    protected $hidden = [
        'password',
    ];

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }
}
