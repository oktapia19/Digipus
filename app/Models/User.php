<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\Peminjaman;
use App\Models\Wishlist;
use App\Models\Review;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class User extends Authenticatable
{
    use Notifiable;

    protected $fillable = [
        'role_id',
        'name',
        'email',
        'password',

        // TAMBAHAN PROFIL
        'tanggal_masuk_akun',
        'nama_lengkap',
        'alamat',
        'no_telepon',
        'photo',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Auto isi tanggal masuk akun
     */
    protected static function booted()
    {
        static::creating(function ($user) {
            $user->tanggal_masuk_akun = now();
        });
    }

    public function peminjamans()
    {
        return $this->hasMany(Peminjaman::class);
    }

    public function wishlists()
    {
        return $this->hasMany(Wishlist::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    public function getIsAdminAttribute(): bool
    {
        return optional($this->role)->name === 'admin';
    }
}
