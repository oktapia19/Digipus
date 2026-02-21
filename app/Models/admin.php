<?php
namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Admin extends Authenticatable
{
    protected $table = 'admins';
    protected $fillable = ['role_id', 'name', 'email', 'password', 'photo'];
    protected $hidden = ['password'];

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }
}
