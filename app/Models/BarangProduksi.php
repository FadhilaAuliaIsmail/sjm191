<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BarangProduksi extends Model
{
    protected $fillable = ['user_id', 'sumber', 'nama_barang', 'tanggal_masuk', 'jumlah', 'satuan', 'jumlah_dibeli', 'berat_per_satuan', 'harga_beli', 'keterangan'];
    protected $table = 'barang_produksi';

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
