<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;
    protected $table = 'pengguna';
    protected $fillable = ['name', 'email', 'password', 'peran'];
    protected $hidden = ['password', 'remember_token'];
    protected function casts(): array
    {
        return ['email_verified_at' => 'datetime', 'password' => 'hashed'];
    }
    public function isPemilik(): bool
    {
        return $this->peran === 'pemilik_usaha';
    }
    public function isKasir(): bool
    {
        return $this->peran === 'kasir';
    }
    public function produk()
    {
        return $this->hasMany(Produk::class);
    }
    public function dataMitra()
    {
        return $this->hasMany(DataMitra::class);
    }
    public function barangProduks()
    {
        return $this->hasMany(barangProduksi::class);
    }
    public function transaksi()
    {
        return $this->hasMany(Transaksi::class);
    }
}
