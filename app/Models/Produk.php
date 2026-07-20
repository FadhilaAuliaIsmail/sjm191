<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Produk extends Model
{
    use HasFactory;

    protected $table = 'produk';

    protected $fillable = [
        'nama_produk',
        'harga',
        'stok',
        'satuan',
        'deskripsi',
        'foto',
        'user_id',
    ];

    protected $casts = [
        'harga' => 'decimal:2',
        'stok'  => 'integer',
    ];

    // Selalu ikut terkirim ke frontend setiap kali produk di-serialize
    protected $appends = ['foto_url'];

    /**
     * URL lengkap ke foto produk, atau null kalau belum ada foto.
     * Frontend (Vue) tinggal pakai `produk.foto_url` — tidak perlu
     * menyusun path '/storage/...' manual sama sekali.
     */
    public function getFotoUrlAttribute(): ?string
    {
        if (! $this->foto) {
            return null;
        }

        return asset('storage/' . $this->foto);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}