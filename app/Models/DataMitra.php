<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DataMitra extends Model
{
    protected $fillable = ['user_id', 'cabang', 'alamat', 'no_telp'];
    protected $table = 'data_mitra';

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
